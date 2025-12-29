<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    if( !isset($_POST['events_id'])||!$_POST['events_id'] ){
        Status::error( 'ไม่พบรหัสกิจกรรม !!!' );
    }
    // Begin
    $today = new datetime();
    $parameters = array();
    $datas  = '`status`';
    $datas .= "=:status";
    $parameters['status'] = ( (isset($_POST['status'])&&$_POST['status']) ? $_POST['status'] : 0 );
    $datas .= ',`date_update`';
    $datas .= "=NOW()";
    $datas .= ',`user_update`';
    $datas .= "=:user_update";
    $parameters['user_update'] = User::get('email');
    if( DB::update("UPDATE `events` SET $datas WHERE events_id=:events_id;", $parameters) ){
        Status::success( "เปลี่ยนสถานะกิจกรรมเรียบร้อยแล้ว", array('title'=>"บันทึกเรียบร้อยแล้ว") );
    }
    Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถเปลี่ยนได้") );
?>