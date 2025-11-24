<?php 
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    
    // Load autoload
    require_once __DIR__ . '/../app/autoload.php';

    try {
        // รับ event_id จาก query parameter (ถ้ามี)
        $events_id = isset($_GET['event_id']) ? trim($_GET['event_id']) : null;
        
        // สร้าง WHERE clause สำหรับกรองตาม event (ถ้ามี)
        $whereClause = '';
        $params = [];
        if($events_id) {
            $whereClause = 'WHERE c.events_id = :events_id';
            $params['events_id'] = $events_id;
        }

        // นับจำนวนผู้เช็คอิน (ทั้งหมดหรือตาม event)
        $countSql = "SELECT COUNT(*) AS c FROM `checkin` c";
        if($whereClause) {
            $countSql .= ' ' . $whereClause;
        }
        $countResult = DB::one($countSql, $params);
        $count = $countResult ? (int)$countResult['c'] : 0;

        // รายชื่อผู้เช็คอินล่าสุด (10 รายการ)
        $latestSql = "SELECT 
                p.participant_id,
                CONCAT(COALESCE(p.prefix, ''), ' ', COALESCE(p.firstname, ''), ' ', COALESCE(p.lastname, '')) AS name,
                c.checkin_at AS checkin_time
            FROM `checkin` c
            INNER JOIN `participants` p ON p.participant_id = c.participant_id";
        if($whereClause) {
            $latestSql .= ' ' . $whereClause;
        }
        $latestSql .= " ORDER BY c.checkin_at DESC LIMIT 10";
        
        $latest = DB::query($latestSql, $params);

        // Format checkin_time
        if(is_array($latest)) {
            foreach($latest as &$item) {
                if(isset($item['checkin_time'])) {
                    $date = new DateTime($item['checkin_time']);
                    $item['checkin_time'] = $date->format('d/m/Y H:i:s');
                }
                // Clean up name (remove extra spaces)
                if(isset($item['name'])) {
                    $item['name'] = trim(preg_replace('/\s+/', ' ', $item['name']));
                }
            }
        } else {
            $latest = [];
        }

        $data = [
            'ok' => true,
            'count' => $count,
            'latest' => $latest
        ];

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        echo json_encode([
            'ok' => false,
            'error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
            'count' => 0,
            'latest' => []
        ], JSON_UNESCAPED_UNICODE);
    }
?>

