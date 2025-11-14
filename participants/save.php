<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
// ไฟล์นี้ถูกใช้สำหรับการบันทึกข้อมูลผ่าน AJAX หรือ form submission
// แต่ตอนนี้เราใช้ create.php และ edit.php แทนแล้ว
// เก็บไฟล์นี้ไว้เพื่อความเข้ากันได้กับโค้ดเดิม

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $action = isset($_GET['action']) ? $_GET['action'] : 'create';
    
    if ($action === 'update') {
        // อัพเดทข้อมูล
        $participantId = isset($_GET['participant_id']) ? intval($_GET['participant_id']) : 0;
        
        if ($participantId <= 0) {
            $_SESSION['participant_update_error'] = 'ไม่พบข้อมูลผู้เข้าร่วม';
            header('Location: index.php');
            exit();
        }
        
        $participant_id = isset($_GET['participant_id']) ? trim($_GET['participant_id']) : '';
        $events_id = isset($_GET['events_id']) ? trim($_GET['events_id']) : '';
        $type = isset($_GET['type']) ? trim($_GET['type']) : '';
        $prefix = isset($_GET['prefix']) ? trim($_GET['prefix']) : '';
        $firstname = isset($_GET['firstname']) ? trim($_GET['firstname']) : '';
        $lastname = isset($_GET['lastname']) ? trim($_GET['lastname']) : '';
        $email = isset($_GET['email']) ? trim($_GET['email']) : '';
        $organization = isset($_GET['organization']) ? trim($_GET['organization']) : '';
        $status = isset($_GET['status']) ? trim($_GET['status']) : 'รอเข้าร่วม';
        $note = isset($_GET['note']) ? trim($_GET['note']) : '';
        
        $participantData = [
            'participant_id' => Helper::stringSave($participant_id),
            'events_id' => Helper::stringSave($events_id),
            'type' => Helper::stringSave($type),
            'prefix' => Helper::stringSave($prefix),
            'firstname' => Helper::stringSave($firstname),
            'lastname' => Helper::stringSave($lastname),
            'email' => Helper::stringSave($email),
            'organization' => Helper::stringSave($organization),
            'status' => $status,
            'note' => Helper::stringSave($note)
        ];
        
        try {
            $result = Participant::updateParticipant($participantId, $participantData);
            if ($result) {
                $_SESSION['participant_update_success'] = 'อัพเดทข้อมูลผู้เข้าร่วมสำเร็จ';
            } else {
                $_SESSION['participant_update_error'] = 'ไม่สามารถอัพเดทข้อมูลได้';
            }
        } catch (Exception $e) {
            $_SESSION['participant_update_error'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
        }
    } else {
        // สร้างใหม่
        $events_id = isset($_GET['events_id']) ? trim($_GET['events_id']) : '';
        $type = isset($_GET['type']) ? trim($_GET['type']) : '';
        $prefix = isset($_GET['prefix']) ? trim($_GET['prefix']) : '';
        $firstname = isset($_GET['firstname']) ? trim($_GET['firstname']) : '';
        $lastname = isset($_GET['lastname']) ? trim($_GET['lastname']) : '';
        $email = isset($_GET['email']) ? trim($_GET['email']) : '';
        $organization = isset($_GET['organization']) ? trim($_GET['organization']) : '';
        $status = isset($_GET['status']) ? trim($_GET['status']) : 'รอเข้าร่วม';
        $note = isset($_GET['note']) ? trim($_GET['note']) : '';
        
        // สร้าง participant_id อัตโนมัติ (จะถูกสร้างใน model ถ้าไม่มี)
        $participantData = [
            'participant_id' => '', // จะถูก generate อัตโนมัติใน model
            'events_id' => Helper::stringSave($events_id),
            'type' => Helper::stringSave($type),
            'prefix' => Helper::stringSave($prefix),
            'firstname' => Helper::stringSave($firstname),
            'lastname' => Helper::stringSave($lastname),
            'email' => Helper::stringSave($email),
            'organization' => Helper::stringSave($organization),
            'status' => $status,
            'note' => Helper::stringSave($note)
        ];
        
        try {
            $result = Participant::createParticipant($participantData);
            if ($result) {
                $_SESSION['participant_create_success'] = 'เพิ่มรายชื่อผู้เข้าร่วมสำเร็จ';
            } else {
                $_SESSION['participant_create_error'] = 'ไม่สามารถเพิ่มรายชื่อได้';
            }
        } catch (Exception $e) {
            $_SESSION['participant_create_error'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
        }
    }
    
    header('Location: index.php');
    exit();
} else {
    header('Location: index.php');
    exit();
}
?>
