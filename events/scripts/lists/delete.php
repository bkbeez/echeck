<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    if( !isset($_POST['id'])||!$_POST['id'] ){
        Status::error( 'ไม่พบรหัส !!!' );
    }else if( !isset($_POST['events_id'])||!$_POST['events_id'] ){
        Status::error( 'ไม่พบรหัสกิจกรรม !!!' );
    }
    // Begin
    $parameters = array();
    $parameters['id'] = $_POST['id'];
    $parameters['events_id'] = $_POST['events_id'];
    if( DB::delete("DELETE FROM `events_lists` WHERE id=:id AND events_id=:events_id;", $parameters) ){
        // Summary
        DB::update("UPDATE `events` SET `participants`=(SELECT COUNT(events_lists.id) FROM events_lists WHERE events_lists.events_id=:events_id) WHERE events_id=:events_id;", array('events_id'=>$parameters['events_id']));
        Status::success( "รายชื่อถูกลบเรียบร้อยแล้ว", array('title'=>"ลบรายชื่อแล้ว") );
    }
    Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถลบได้") );
?>