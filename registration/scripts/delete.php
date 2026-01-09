<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/registration'); ?>
<?php
    $id = $_POST['id'] ?? null;
    $events_id = $_POST['events_id'] ?? null;
    if(!$id || !$events_id){
        Status::error('ข้อมูลไม่ครบถ้วน !!!');
    }
    $parameters = ['id' => $id, 'events_id' => $events_id];
    if( DB::query("DELETE FROM `events_lists` WHERE `id` = :id AND `events_id` = :events_id", $parameters) ){
        
        DB::update("UPDATE `events`
                    SET `participants` = (SELECT COUNT(id) FROM events_lists WHERE events_id = :events_id) 
                    WHERE events_id = :events_id",
                    ['events_id' => $events_id]);
        Status::success("ลบรายชื่อผู้ลงทะเบียนเรียบร้อยแล้ว", ['title' => "สำเร็จ"]);
    } else {
        Status::error("ไม่สามารถลบข้อมูลได้ กรุณาลองใหม่อีกครั้ง");
    }
?>