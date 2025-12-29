<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    if( !isset($_POST['events_id'])||!$_POST['events_id'] ){
        Status::error( 'ไม่พบรหัสกิจกรรม !!!' );
    }else if(!isset($_POST['email'])||!$_POST['email']){
        Status::error( 'กรุณากรอกอีเมล !!!' );
    }
    // Begin
    $parameters = array();
    $parameters['events_id'] = $_POST['events_id'];
    $parameters['email'] = $_POST['email'];
    Status::success( "ยกเลิกแชร์เรียบร้อยแล้ว", $parameters );
    if( DB::delete("DELETE FROM `events_shared` WHERE events_id=:events_id AND email=:email;", $parameters) ){
        Status::success( "ยกเลิกแชร์เรียบร้อยแล้ว", $parameters );
    }
    Status::error( 'ยกเลิกแชร์ไม่ได้, <em class="on-blink">กรุณาลองใหม่อีกครั้ง !!!</em>' );
?>