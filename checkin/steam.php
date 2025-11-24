<?php 
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Connection: keep-alive');
    header('X-Accel-Buffering: no'); // Disable nginx buffering

    // Load autoload
    require_once __DIR__ . '/../app/autoload.php';

    // Set time limit for long-running script
    set_time_limit(0);
    ignore_user_abort(false);

    while (true) {
        try {
            // นับจำนวนผู้เช็คอินทั้งหมด
            $countResult = DB::one("SELECT COUNT(*) AS c FROM `checkin`");
            $count = $countResult ? (int)$countResult['c'] : 0;

            // รายชื่อผู้เช็คอินล่าสุด (200 รายการ)
            $latest = DB::query(
                "SELECT 
                    p.participant_id,
                    CONCAT(COALESCE(p.prefix, ''), ' ', COALESCE(p.firstname, ''), ' ', COALESCE(p.lastname, '')) AS name,
                    c.checkin_at AS checkin_time
                FROM `checkin` c
                INNER JOIN `participants` p ON p.participant_id = c.participant_id
                ORDER BY c.checkin_at DESC
                LIMIT 200"
            );

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

            echo "data: " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n\n";
            
            if (ob_get_level() > 0) {
                ob_flush();
            }
            flush();

            // ตรวจสอบว่าผู้ใช้ยังเชื่อมต่ออยู่หรือไม่
            if (connection_aborted()) {
                break;
            }

            sleep(1);
        } catch (Exception $e) {
            $errorData = [
                'ok' => false,
                'error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
                'count' => 0,
                'latest' => []
            ];
            echo "data: " . json_encode($errorData, JSON_UNESCAPED_UNICODE) . "\n\n";
            if (ob_get_level() > 0) {
                ob_flush();
            }
            flush();
            sleep(1);
        }
    }
?>