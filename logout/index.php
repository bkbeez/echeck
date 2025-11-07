<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    session_destroy();
    if( isset($_REQUEST['ajax']) ){
        Status::success( ( (App::lang()=='en') ? 'Thank you for using our service.' : 'ขอขอบพระคุณที่ใช้บริการ' ), array('title'=>( (App::lang()=='en') ? 'Logged out' : 'ออกจากระบบแล้ว' ), 'url'=>APP_HOME ) );
    }else{
        header('Location: '.APP_HOME);
        exit;
    }
?>