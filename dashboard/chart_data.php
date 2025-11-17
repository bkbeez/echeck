<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php 
    include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php');
    
    $user_id = '';
    if (isset($_SESSION['login']) && isset($_SESSION['login']['user'])) {
        $user_id = isset($_SESSION['login']['user']['email']) ? $_SESSION['login']['user']['email'] :
                    (isset($_SESSION['login']['user']['id']) ? $_SESSION['login']['user']['id'] : '');
    }
    
    try {
        if (isset($_SESSION['login']) && isset($_SESSION['login']['user']) && $user_id) {
            // ดึงเฉพาะกิจกรรมของผู้ใช้
            $events = Event::listForUser($user_id);
        } else {
            // ดึงกิจกรรมทั้งหมด
            $events = DB::query(
                "SELECT * FROM `events` ORDER BY `start_date` DESC, `id` DESC"
            );
        }
        
        if (!is_array($events)) {
            $events = [];
        }
        
        $labels = [];
        $values = [];
        
        foreach ($events as $event) {
            if (!isset($event['events_id']) || !isset($event['events_name'])) {
                continue;
            }
            
            try {
                $eventId = $event['events_id'];
                $participants = Participant::listByEvent($eventId);
                $count = is_array($participants) ? count($participants) : 0;
                
                $labels[] = $event['events_name'];
                $values[] = $count;
            } catch (Exception $e) {
                // Skip this event if there's an error
                continue;
            }
        }
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['labels' => $labels, 'values' => $values], JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['labels' => [], 'values' => [], 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
?>