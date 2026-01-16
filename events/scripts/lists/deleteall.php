<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    if( !isset($_POST['events_id'])||!$_POST['events_id'] ){
        Status::error( 'ไม่พบรหัสกิจกรรม !!!' );
    }else if( !isset($_POST['deletes'])||count($_POST['deletes'])<=0 ){
        Status::error( 'ไม่พบรายชื่อที่ต้องการลบ !!!' );
    }
    // Begin
    $parameters = array();
    $parameters['events_id'] = $_POST['events_id'];
    if( DB::delete("DELETE FROM `events_lists` WHERE events_id=:events_id AND id IN (".Helper::stringSqlIn(implode(',',$_POST['deletes'])).");", $parameters) ){
        // Summary
        DB::update("UPDATE `events` SET `participants`=(SELECT COUNT(events_lists.id) FROM events_lists WHERE events_lists.events_id=:events_id) WHERE events_id=:events_id;", array('events_id'=>$parameters['events_id']));
        Status::success( "รายชื่อถูกลบเรียบร้อยแล้ว", array('title'=>"ลบรายชื่อแล้ว") );
    }
    Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถลบได้") );
?>