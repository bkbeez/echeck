<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    if( !isset($_POST['id'])||!$_POST['id'] ){
        Status::error( 'ไม่พบรหัส !!!' );
    }else if( !isset($_POST['events_id'])||!$_POST['events_id'] ){
        Status::error( 'ไม่พบรหัสกิจกรรม !!!' );
    }else if( !isset($_POST['type'])||!$_POST['type'] ){
        Status::error( 'กรุณาเลือกประเภทผู้เข้าร่วม !!!', array('onselect'=>"type") );
    }else if( !isset($_POST['email'])||!$_POST['email'] ){
        Status::error( 'กรุณากรอกอีเมล !!!', array('onfocus'=>"email") );
    }else if( !isset($_POST['firstname'])||!$_POST['firstname'] ){
        Status::error( 'กรุณากรอกชื่อ !!!', array('onfocus'=>"firstname") );
    }else if( !isset($_POST['organization'])||!$_POST['organization'] ){
        Status::error( 'กรุณากรอกสังกัด !!!', array('onfocus'=>"organization") );
    }
    // Begin
    $parameters = array();
    $parameters['id'] = $_POST['id'];
    $parameters['events_id'] = $_POST['events_id'];
    $datas  = '`type`';
    $datas .= "=:type";
    $parameters['type'] = $_POST['type'];
    $datas .= ',`email`';
    $datas .= "=:email";
    $parameters['email'] = ( (isset($_POST['email'])&&$_POST['email']) ? Helper::stringSave($_POST['email']) : null );
    $datas .= ',`prefix`';
    $datas .= "=:prefix";
    $parameters['prefix'] = ( (isset($_POST['prefix'])&&$_POST['prefix']) ? Helper::stringSave($_POST['prefix']) : null );
    $datas .= ',`firstname`';
    $datas .= "=:firstname";
    $parameters['firstname'] = ( (isset($_POST['firstname'])&&$_POST['firstname']) ? Helper::stringSave($_POST['firstname']) : null );
    $datas .= ',`lastname`';
    $datas .= "=:lastname";
    $parameters['lastname'] = ( (isset($_POST['lastname'])&&$_POST['lastname']) ? Helper::stringSave($_POST['lastname']) : null );
    $datas .= ',`organization`';
    $datas .= "=:organization";
    $parameters['organization'] = ( (isset($_POST['organization'])&&$_POST['organization']) ? Helper::stringSave($_POST['organization']) : null );
    $datas .= ',`department`';
    $datas .= "=:department";
    $parameters['department'] = ( (isset($_POST['department'])&&$_POST['department']) ? Helper::stringSave($_POST['department']) : null );
    $datas .= ',`date_update`';
    $datas .= "=NOW()";
    $datas .= ',`user_update`';
    $datas .= "=:user_update";
    $parameters['user_update'] = User::get('email');
    if( DB::update("UPDATE `events_lists` SET $datas WHERE id=:id AND events_id=:events_id;", $parameters) ){
        Status::success( "ข้อมูลถูกเปลี่ยนแปลงเรียบร้อยแล้ว", array('title'=>"บันทึกเรียบร้อยแล้ว") );
    }
    Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถบันทึกได้") );
?>