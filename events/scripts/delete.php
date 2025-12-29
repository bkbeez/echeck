<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    if(!isset($_POST['events_id'])||!$_POST['events_id']){
        Status::error( Lang::get('NotFound').Lang::get('events_id').' !!!' );
    }
    // Begin
    $parameters = array();
    $parameters['events_id'] = $_POST['events_id'];
    if( DB::delete("DELETE FROM `events` WHERE events_id=:events_id;", $parameters) ){
        DB::delete("DELETE FROM `events_lists` WHERE events_id=:events_id;", $parameters);
        DB::delete("DELETE FROM `events_shared` WHERE events_id=:events_id;", $parameters);
        Status::success( "กิจกรรมถูกลบเรียบร้อยแล้ว", array('title'=>"ลบกิจกรรมแล้ว") );
    }
    Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถลบได้") );
?>