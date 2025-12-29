<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    if( !isset($_POST['events_id'])||!$_POST['events_id'] ){
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
    // Check
    /*$email = Helper::stringSave($_POST['email']);
    $check = User::one("SELECT id FROM events_lists WHERE email=:email LIMIT 1;", array('email'=>$email));
    if( isset($check['id'])&&$check['id'] ){
        Status::error( 'อีเมลนี้มีอยู่แล้ว !!!', array('onfocus'=>"email") );
    }*/
    // Begin
    $parameters = array();
    $fields = "`id`";
    $values = ":id";
    $parameters['id'] = (new datetime())->format("YmdHis").'000';
    $fields .= ',`events_id`';
    $values .= ",:events_id";
    $parameters['events_id'] = $_POST['events_id'];
    $fields .= ',`type`';
    $values .= ",:type";
    $parameters['type'] = $_POST['type'];
    $fields .= ',`email`';
    $values .= ",:email";
    $parameters['email'] = ( (isset($_POST['email'])&&$_POST['email']) ? Helper::stringSave($_POST['email']) : null );
    $fields .= ',`prefix`';
    $values .= ",:prefix";
    $parameters['prefix'] = ( (isset($_POST['prefix'])&&$_POST['prefix']) ? Helper::stringSave($_POST['prefix']) : null );
    $fields .= ',`firstname`';
    $values .= ",:firstname";
    $parameters['firstname'] = ( (isset($_POST['firstname'])&&$_POST['firstname']) ? Helper::stringSave($_POST['firstname']) : null );
    $fields .= ',`lastname`';
    $values .= ",:lastname";
    $parameters['lastname'] = ( (isset($_POST['lastname'])&&$_POST['lastname']) ? Helper::stringSave($_POST['lastname']) : null );
    $fields .= ',`organization`';
    $values .= ",:organization";
    $parameters['organization'] = ( (isset($_POST['organization'])&&$_POST['organization']) ? Helper::stringSave($_POST['organization']) : null );
    $fields .= ',`department`';
    $values .= ",:department";
    $parameters['department'] = ( (isset($_POST['department'])&&$_POST['department']) ? Helper::stringSave($_POST['department']) : null );
    $fields .= ',`date_create`';
    $values .= ",NOW()";
    $fields .= ',`user_create`';
    $values .= ",:user_create";
    $parameters['user_create'] = User::get('email');
    if( User::create("INSERT INTO `events_lists` ($fields) VALUES ($values)", $parameters) ){
        // Summary
        DB::update("UPDATE `events` SET `participants`=(SELECT COUNT(events_lists.id) FROM events_lists WHERE events_lists.events_id=:events_id) WHERE events_id=:events_id;", array('events_id'=>$parameters['events_id']));
        Status::success( "บันทึกรายชื่อเข้าสู่กิจกรรมแล้ว", array('title'=>"เพิ่มรายชื่อแล้ว") );
    }
    Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถเพิ่มได้") );
?>