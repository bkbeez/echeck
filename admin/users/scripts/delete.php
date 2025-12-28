<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/admin/?users'); ?>
<?php
    if(!isset($_POST['id'])||!$_POST['id']){
        Status::error( 'ไม่พบรหัสผู้ใช้ !!!' );
    }else if( !isset($_POST['email'])||!$_POST['email'] ){
        Status::error( 'กรุณากรอกอีเมล !!!', array('onfocus'=>"email") );
    }
    // Begin
    $parameters = array();
    $parameters['id'] = $_POST['id'];
    $parameters['email'] = $_POST['email'];
    if( User::delete("DELETE FROM `member` WHERE id=:id AND email=:email;", $parameters) ){
        $logs = array();
        $logs['member_id'] = $parameters['id'];
        $logs['mode'] = "DELETE";
        $logs['title'] = "Delete user";
        $logs['remark'] = $parameters['email'];
        User::log($logs);
        Status::success( "ข้อมูลผู้ใช้ถูกลบเรียบร้อยแล้ว", array('title'=>"ลบผู้ใช้แล้ว") );
    }
    Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถลบได้") );
?>