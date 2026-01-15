<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    $returns = array();
    if( !isset($_POST['employee_id'])||!$_POST['employee_id'] ){
        Status::error( 'ไม่พบรหัส !!!' );
    }
    $returns['at'] = 'AT-'.$_POST['employee_id'];
    if( !isset($_POST['events_id'])||!$_POST['events_id'] ){
        Status::error( '<em class="fs-10 text-red">ไม่พบรหัสกิจกรรม !!!</em>', $returns );
    }else if( !isset($_POST['firstname'])||!$_POST['firstname'] ){
        Status::error( '<em class="fs-10 text-red">ไม่พบชื่อ !!!</em>', $returns );
    }else if( !isset($_POST['organization'])||!$_POST['organization'] ){
        Status::error( '<em class="fs-10 text-red">ไม่พบสังกัด !!!</em>', $returns );
    }
    // Begin
    // Name exist
    $checks = array();
    $checks['events_id'] = $_POST['events_id'];
    $checksql = "SELECT id FROM events_lists WHERE events_id=:events_id";
    $parameters = array();
    $fields = "`id`";
    $values = ":id";
    $parameters['id'] = (new datetime())->format("YmdHis").sprintf("%1$09d", $_POST['employee_id']);
    $fields .= ',`events_id`';
    $values .= ",:events_id";
    $parameters['events_id'] = $_POST['events_id'];
    $fields .= ',`type`';
    $values .= ",:type";
    $parameters['type'] = $_POST['type'];
    $fields .= ',`student_id`';
    $values .= ",:student_id";
    $parameters['student_id'] = null;
    $fields .= ',`email`';
    $values .= ",:email";
    $parameters['email'] = null;
    if( isset($_POST['email'])&&$_POST['email'] ){
        $parameters['email'] = $_POST['email'];
        $checks['email'] = $parameters['email'];
        $checksql .= " AND email=:email";
    }else{
        $checksql .= " AND email IS NULL";
    }
    $fields .= ',`prefix`';
    $values .= ",:prefix";
    $parameters['prefix'] = ( (isset($_POST['prefix'])&&$_POST['prefix']) ? $_POST['prefix'] : null );
    $fields .= ',`firstname`';
    $values .= ",:firstname";
    $parameters['firstname'] = null;
    if( isset($_POST['firstname'])&&$_POST['firstname'] ){
        $parameters['firstname'] = $_POST['firstname'];
        $checks['firstname'] = $parameters['firstname'];
        $checksql .= " AND firstname=:firstname";
    }else{
        $checksql .= " AND firstname IS NULL";
    }
    $fields .= ',`lastname`';
    $values .= ",:lastname";
    $parameters['lastname'] = null;
    if( isset($_POST['lastname'])&&$_POST['lastname'] ){
        $parameters['lastname'] = $_POST['lastname'];
        $checks['lastname'] = $parameters['lastname'];
        $checksql .= " AND lastname=:lastname";
    }else{
        $checksql .= " AND lastname IS NULL";
    }
    $namecheck = DB::one($checksql, $checks);
    if( isset($namecheck['id'])&&$namecheck['id'] ){
        Status::error( '<em class="fs-10 text-green">รายชื่อนี้มีอยู่แล้ว</em>', $returns );
    }
    $fields .= ',`organization`';
    $values .= ",:organization";
    $parameters['organization'] = ( (isset($_POST['organization'])&&$_POST['organization']) ? $_POST['organization'] : null );
    $fields .= ',`department`';
    $values .= ",:department";
    $parameters['department'] = ( (isset($_POST['department'])&&$_POST['department']) ? $_POST['department'] : null );
    $fields .= ',`date_create`';
    $values .= ",NOW()";
    $fields .= ',`user_create`';
    $values .= ",:user_create";
    $parameters['user_create'] = User::get('email');
    if( User::create("INSERT INTO `events_lists` ($fields) VALUES ($values)", $parameters) ){
        // Summary
        DB::update("UPDATE `events` SET `participants`=(SELECT COUNT(events_lists.id) FROM events_lists WHERE events_lists.events_id=:events_id) WHERE events_id=:events_id;", array('events_id'=>$parameters['events_id']));
        Status::success( '<em class="fs-10 text-green">เพิ่มรายชื่อเรียบร้อยแล้ว</em>', $returns );
    }
    Status::error( '<em class="fs-10 text-red">ไม่สามารถเพิ่มรายชื่อนี้ได้ !!!</em>', $returns );
?>