<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/registration'); ?>
<?php
    if( !isset($_POST['events_id']) || !$_POST['events_id'] ){
        Status::error( 'ไม่พบรหัสกิจกรรม !!!' );
    }
    if( !isset($_POST['type']) || !$_POST['type'] ){
        Status::error( 'ไม่พบประเภท !!!' );
    }
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
    $fields .= ',`student_id`';
    $values .= ",:student_id";
    $parameters['student_id'] = ( (isset($_POST['student_id'])&&$_POST['student_id']) ? Helper::stringSave($_POST['student_id']) : null );
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
    $fields .= ',`status`';
    $values .= ",:status";
    $parameters['status'] = 0;
    $values .= ",NOW()";
    $fields .= ',`user_create`';
    $values .= ",:user_create";
    $parameters['user_create'] = User::get('email');
    if( DB::create("INSERT INTO `events_lists` ($fields) VALUES ($values)", $parameters) ){
        // Summary
        DB::update("UPDATE `events` SET `participants`=(SELECT COUNT(events_lists.id) FROM events_lists WHERE events_lists.events_id=:events_id) WHERE events_id=:events_id;", array('events_id'=>$parameters['events_id']));
        Status::success( "ลงทะเบียนเรียบร้อยแล้ว", array('title'=>"บันทึกเรียบร้อยแล้ว") );
    }else{
        Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถบันทึกได้") );
    }
?>