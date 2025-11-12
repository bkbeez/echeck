<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    // ดึง event ID จาก query parameter
    $eventId = isset($_GET['delete']) ? intval($_GET['delete']) : 0;
    
    if ($eventId <= 0) {
        $_SESSION['event_delete_error'] = 'ไม่พบข้อมูลกิจกรรม';
        header('Location: index.php');
        exit();
    }
    
    // ดึง user_id จาก session (ถ้ามีการล็อกอิน) หรือใช้ค่าดีฟอลต์
    $user_id = '';
    if (isset($_SESSION['login']) && isset($_SESSION['login']['user'])) {
        $user_id = isset($_SESSION['login']['user']['email']) ? $_SESSION['login']['user']['email'] :
                    (isset($_SESSION['login']['user']['id']) ? $_SESSION['login']['user']['id'] : '');
    }
    
    // ตรวจสอบว่าผู้ใช้เป็นเจ้าของกิจกรรมหรือไม่
    $event = Event::getOwnedEvent($eventId, $user_id);
    
    if (!$event) {
        $_SESSION['event_delete_error'] = 'ไม่พบกิจกรรมหรือคุณไม่มีสิทธิ์ลบกิจกรรมนี้';
        header('Location: index.php');
        exit();
    }
    
    // ลบกิจกรรมด้วย Event model
    try {
        $result = Event::deleteEvent($eventId, $user_id);
        
        if ($result) {
            // ลบสำเร็จ
            $_SESSION['event_delete_success'] = 'ลบกิจกรรมสำเร็จ';
        } else {
            $_SESSION['event_delete_error'] = 'ไม่สามารถลบกิจกรรมได้ กรุณาลองใหม่อีกครั้ง';
        }
    } catch (Exception $e) {
        $_SESSION['event_delete_error'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
    }
    
    // Redirect ไปยังหน้า index
    header('Location: index.php');
    exit();
?>

