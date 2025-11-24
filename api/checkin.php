<?php 
    header('content-type: application/json; charset=utf-8');
    
    // Load autoload
    require_once __DIR__ . '/../app/autoload.php';

    $input = json_decode(file_get_contents('php://input'), true);
    if(!$input){
        echo json_encode(['ok' => false, 'error' => 'ไม่มีข้อมูลส่งมา']);
        exit;
    }
    $participant_id = isset($input['participant_id']) ? trim($input['participant_id']) : null;
    $events_id = isset($input['event_id']) ? trim($input['event_id']) : null;

    if(!$participant_id) {
        echo json_encode(['ok' => false, 'error' => 'ไม่มีรหัสผู้เข้าร่วมกิจกรรม']);
        exit;
    }
    
    try {
        // ตรวจสอบว่าผู้เข้าร่วมกิจกรรมมีอยู่ในระบบหรือไม่
        $participant = Participant::findByParticipantId($participant_id);
        if(!$participant) {
            echo json_encode(['ok' => false, 'error' => 'ไม่พบผู้เข้าร่วมกิจกรรมในระบบ']);
            exit;
        }

        // ถ้าไม่ได้ส่ง event_id มา ให้ใช้ events_id จาก participant
        if(!$events_id) {
            $events_id = $participant['events_id'];
        }

        // ตรวจสอบว่า events_id ตรงกับ participant หรือไม่
        if($participant['events_id'] != $events_id) {
            echo json_encode(['ok' => false, 'error' => 'รหัสกิจกรรมไม่ตรงกับผู้เข้าร่วมกิจกรรม']);
            exit;
        }

        // ตรวจสอบว่ามีการเช็คอินแล้วหรือยัง
        $existing = DB::one(
            "SELECT id FROM `checkin` WHERE `participant_id` = :participant_id AND `events_id` = :events_id LIMIT 1",
            [
                'participant_id' => $participant_id,
                'events_id' => $events_id
            ]
        );

        if($existing) {
            echo json_encode(['ok' => false, 'error' => 'เช็คอินแล้ว']);
            exit;
        }

        // บันทึกการเช็คอิน
        $sql = "INSERT INTO `checkin` (`participant_id`, `events_id`, `checkin_at`) VALUES (:participant_id, :events_id, NOW())";
        $result = DB::create($sql, [
            'participant_id' => $participant_id,
            'events_id' => $events_id
        ]);

        if($result) {
            $name = trim(($participant['prefix'] ?? '') . ' ' . ($participant['firstname'] ?? '') . ' ' . ($participant['lastname'] ?? ''));
            $name = $name ?: $participant_id;
            echo json_encode(['ok' => true, 'participant_id' => $participant_id, 'name' => $name]);
        } else {
            echo json_encode(['ok' => false, 'error' => 'ไม่สามารถบันทึกการเช็คอินได้']);
        }
    } catch (Exception $e) {
        echo json_encode(['ok' => false, 'error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    }
?>