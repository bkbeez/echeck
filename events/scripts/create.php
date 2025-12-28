<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    if( !isset($_POST['events_name'])||!$_POST['events_name'] ){
        Status::error( 'กรุณากรอกชื่อ !!!', array('onfocus'=>"events_name") );
    }else if( !isset($_POST['participant_type'])||!$_POST['participant_type'] ){
        Status::error( 'กรุณาเลือกประเภท !!!', array('onselect'=>"participant_type") );
    }else if( !isset($_POST['start_date'])||!$_POST['start_date'] ){
        Status::error( 'กรุณากรอกวันที่ !!!', array('onfocus'=>"start_date") );
    }else if( !Helper::validDate($_POST['start_date']) ){
        Status::error( 'วันที่ไม่ถูกต้อง !!!', array('onfocus'=>"start_date") );
    }else if( !isset($_POST['start_time'])||!$_POST['start_time'] ){
        Status::error( 'กรุณากรอกเวลา !!!', array('onfocus'=>"start_time") );
    }else if( !Helper::validTime($_POST['start_time']) ){
        Status::error( 'เวลาไม่ถูกต้อง !!!', array('onfocus'=>"start_time") );
    }else if( !isset($_POST['end_date'])||!$_POST['end_date'] ){
        Status::error( 'กรุณากรอกวันที่ !!!', array('onfocus'=>"end_date") );
    }else if( !Helper::validDate($_POST['end_date']) ){
        Status::error( 'วันที่ไม่ถูกต้อง !!!', array('onfocus'=>"end_date") );
    }else if( !isset($_POST['end_time'])||!$_POST['end_time'] ){
        Status::error( 'กรุณากรอกเวลา !!!', array('onfocus'=>"end_time") );
    }else if( !Helper::validTime($_POST['end_time']) ){
        Status::error( 'เวลาไม่ถูกต้อง !!!', array('onfocus'=>"end_time") );
    }
    // Begin
    $today = new datetime();
    $parameters = array();
    $fields = "`events_id`";
    $values = ":events_id";
    $parameters['events_id'] = "EVT-".$today->format("YmdHis").'-'.substr(md5(uniqid(rand(), true)), 0, 8);
    $fields .= ',`events_name`';
    $values .= ",:events_name";
    $parameters['events_name'] = Helper::stringSave($_POST['events_name']);
    $fields .= ',`participant_type`';
    $values .= ",:participant_type";
    $parameters['participant_type'] = $_POST['participant_type'];
    $fields .= ',`start_date`';
    $values .= ",:start_date";
    $parameters['start_date'] = Helper::dateSave($_POST['start_date']).' '.$_POST['start_time'].':00';
    $fields .= ',`end_date`';
    $values .= ",:end_date";
    $parameters['end_date'] = Helper::dateSave($_POST['end_date']).' '.$_POST['end_time'].':00';
    if( (new datetime($parameters['start_date']))>=(new datetime($parameters['end_date'])) ){
        Status::error( 'เริ่มต้นกิจกรรม <= สิ้นสุดกิจกรรม !!!', array('title'=>"วันที่-เวลาไม่ถูกต้อง", 'onfocus'=>"end_date") );
    }
    $fields .= ',`date_create`';
    $values .= ",NOW()";
    $fields .= ',`user_create`';
    $values .= ",:user_create";
    $parameters['user_create'] = User::get('email');
    if( DB::create("INSERT INTO `events` ($fields) VALUES ($values)", $parameters) ){
        Status::success( "บันทึกข้อมูลกิจกรรมเรียบร้อยแล้ว", array('title'=>"เพิ่มกิจกรรมแล้ว", 'events_id'=>$parameters['events_id'], 'participant_type'=>$parameters['participant_type']) );
    }
    Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถเพิ่มได้") );
?>