<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/registration'); ?>
<?php
    if( !isset($_POST['events_lists_id'])||!$_POST['events_lists_id'] ){
        Status::error( 'ไม่พบรหัสผู้เข้าร่วมกิจกรรม !!!' );
    }
    // Begin
    $parameters = array();
    $parameters['events_lists_id'] = $_POST['events_lists_id'];
    $parameters['events_id'] = $_POST['events_id'];
    $parameters['student_id'] = $_POST['student_id'];
    $parameters['email'] = $_POST['email'];
    $parameters['prefix'] = $_POST['prefix'];
    $parameters['first_name'] = $_POST['first_name'];
    $parameters['last_name'] = $_POST['last_name'];
    $parameters['organization'] = $_POST['organization'];
    $parameters['checked_in'] = date('Y-m-d H:i:s');
    $datas = " checked_in=:checked_in ";
    if( DB::update("UPDATE `events_lists` SET $datas WHERE events_lists_id=:events_lists_id;", $parameters) ){
        Status::success( "เช็คอินเรียบร้อยแล้ว", array('title'=>"บันทึกเรียบร้อยแล้ว") );
    }else{
    Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถบันทึกได้") );
    }
?>