<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    // ดึง participant ID จาก query parameter
    $participantId = isset($_GET['delete']) ? intval($_GET['delete']) : 0;
    
    if ($participantId <= 0) {
        $_SESSION['participant_delete_error'] = 'ไม่พบข้อมูลผู้เข้าร่วม';
        header('Location: index.php');
        exit();
    }
    
    // ตรวจสอบว่ามีข้อมูลผู้เข้าร่วมหรือไม่
    $participant = Participant::getParticipant($participantId);
    
    if (!$participant) {
        $_SESSION['participant_delete_error'] = 'ไม่พบข้อมูลผู้เข้าร่วม';
        header('Location: index.php');
        exit();
    }
    
    // ลบผู้เข้าร่วมด้วย Participant model
    try {
        $result = Participant::deleteParticipant($participantId);
        
        if ($result === true) {
            $_SESSION['participant_delete_success'] = 'ลบรายชื่อผู้เข้าร่วมสำเร็จ';
        } else {
            $_SESSION['participant_delete_error'] = 'ไม่สามารถลบรายชื่อได้ อาจจะไม่มีข้อมูลนี้อยู่แล้ว หรือเกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง';
        }
    } catch (Exception $e) {
        $_SESSION['participant_delete_error'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
        error_log('Participant delete error: ' . $e->getMessage());
    } catch (PDOException $e) {
        $_SESSION['participant_delete_error'] = 'เกิดข้อผิดพลาดฐานข้อมูล: ' . $e->getMessage();
        error_log('Participant delete PDO error: ' . $e->getMessage());
    }
    
    // Redirect ไปยังหน้า index
    header('Location: index.php');
    exit();
?>
