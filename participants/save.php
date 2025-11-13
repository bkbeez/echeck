<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
// ไฟล์นี้ถูกใช้สำหรับการบันทึกข้อมูลผ่าน AJAX หรือ form submission
// แต่ตอนนี้เราใช้ create.php และ edit.php แทนแล้ว
// เก็บไฟล์นี้ไว้เพื่อความเข้ากันได้กับโค้ดเดิม

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = isset($_POST['action']) ? $_POST['action'] : 'create';
    
    if ($action === 'update') {
        // อัพเดทข้อมูล
        $participantId = isset($_POST['participant_id']) ? intval($_POST['participant_id']) : 0;
        
        if ($participantId <= 0) {
            $_SESSION['participant_update_error'] = 'ไม่พบข้อมูลผู้เข้าร่วม';
            header('Location: index.php');
            exit();
        }
        
        $events_id = isset($_POST['events_id']) ? trim($_POST['events_id']) : '';
        $type = isset($_POST['type']) ? trim($_POST['type']) : '';
        $prefix = isset($_POST['prefix']) ? trim($_POST['prefix']) : '';
        $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
        $lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $organization = isset($_POST['organization']) ? trim($_POST['organization']) : '';
        $status = isset($_POST['status']) ? trim($_POST['status']) : 'รอเข้าร่วม';
        $note = isset($_POST['note']) ? trim($_POST['note']) : '';
        
        $participantData = [
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
        $events_id = isset($_POST['events_id']) ? trim($_POST['events_id']) : '';
        $type = isset($_POST['type']) ? trim($_POST['type']) : '';
        $prefix = isset($_POST['prefix']) ? trim($_POST['prefix']) : '';
        $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
        $lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $organization = isset($_POST['organization']) ? trim($_POST['organization']) : '';
        $status = isset($_POST['status']) ? trim($_POST['status']) : 'รอเข้าร่วม';
        $note = isset($_POST['note']) ? trim($_POST['note']) : '';
        
        $participant_id = 'PART-' . date('YmdHis') . '-' . substr(md5(uniqid(rand(), true)), 0, 8);
        
        $participantData = [
            'participant_id' => $participant_id,
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
