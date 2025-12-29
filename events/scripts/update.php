<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    if( !isset($_POST['events_id'])||!$_POST['events_id'] ){
        Status::error( 'ไม่พบรหัสกิจกรรม !!!' );
    }else if( !isset($_POST['events_name'])||!$_POST['events_name'] ){
        Status::error( 'กรุณากรอกชื่อ !!!', array('onfocus'=>"events_name") );
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
    $parameters = array();
    $parameters['events_id'] = $_POST['events_id'];
    $datas  = '`events_name`';
    $datas .= "=:events_name";
    $parameters['events_name'] = Helper::stringSave($_POST['events_name']);
    $datas .= ',`start_date`';
    $datas .= "=:start_date";
    $parameters['start_date'] = Helper::dateSave($_POST['start_date']).' '.$_POST['start_time'].':00';
    $datas .= ',`end_date`';
    $datas .= "=:end_date";
    $parameters['end_date'] = Helper::dateSave($_POST['end_date']).' '.$_POST['end_time'].':00';
    if( (new datetime($parameters['start_date']))>=(new datetime($parameters['end_date'])) ){
        Status::error( 'เริ่มต้นกิจกรรม <= สิ้นสุดกิจกรรม !!!', array('title'=>"วันที่-เวลาไม่ถูกต้อง", 'onfocus'=>"end_date") );
    }
    $datas .= ',`status`';
    $datas .= "=:status";
    $parameters['status'] = ( (isset($_POST['status'])&&$_POST['status']) ? $_POST['status'] : 0 );
    $datas .= ',`date_update`';
    $datas .= "=NOW()";
    $datas .= ',`user_update`';
    $datas .= "=:user_update";
    $parameters['user_update'] = User::get('email');
    if( DB::update("UPDATE `events` SET $datas WHERE events_id=:events_id;", $parameters) ){
        Status::success( "ข้อมูลถูกเปลี่ยนแปลงเรียบร้อยแล้ว", array('title'=>"บันทึกเรียบร้อยแล้ว") );
    }
    Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถบันทึกได้") );
?>