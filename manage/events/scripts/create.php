<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH); ?>
<?php //Helper::debug($_POST, 1);
    if( !isset($_POST['events_name'])||!$_POST['events_name'] ){
        Status::error( 'กรุณากรอกชื่อกิจกรรม !!!', array('title'=>"ไม่พบชื่อกิจกรรม", 'onfocus'=>"events_name") );
    }else if( !isset($_POST['events_type'])||!$_POST['events_type'] ){
        Status::error( 'กรุณาเลือกประเภทกิจกรรม !!!', array('title'=>"ไม่พบประเภทกิจกรรม", 'onselect'=>"events_type") );
    }else if( !isset($_POST['start_date'])||!$_POST['start_date'] ){
        Status::error( 'กรุณากรอกวันที่เริ่มต้นกิจกรรม !!!', array('title'=>"วันที่เริ่มต้นกิจกรรม", 'onfocus'=>"start_date") );
    }else if( !Helper::validDate($_POST['start_date']) ){
        Status::error( 'กรุณาเช็ควันที่เริ่มต้นกิจกรรม !!!', array('title'=>"วันที่ไม่ถูกต้อง", 'onfocus'=>"start_date") );
    }else if( !isset($_POST['start_time'])||!$_POST['start_time'] ){
        Status::error( 'กรุณากรอกเวลาเริ่มต้นกิจกรรม !!!', array('title'=>"เวลาเริ่มต้นกิจกรรม", 'onfocus'=>"start_time") );
    }else if( !Helper::validTime($_POST['start_time']) ){
        Status::error( 'กรุณาเช็คเวลาเริ่มต้นกิจกรรม !!!', array('title'=>"เวลาไม่ถูกต้อง", 'onfocus'=>"start_time") );
    }else if( !isset($_POST['end_date'])||!$_POST['end_date'] ){
        Status::error( 'กรุณากรอกวันที่สิ้นสุดกิจกรรม !!!', array('title'=>"วันที่สิ้นสุดกิจกรรม", 'onfocus'=>"end_date") );
    }else if( !Helper::validDate($_POST['end_date']) ){
        Status::error( 'กรุณาเช็ควันที่สิ้นสุดกิจกรรม !!!', array('title'=>"วันที่ไม่ถูกต้อง", 'onfocus'=>"end_date") );
    }else if( !isset($_POST['end_time'])||!$_POST['end_time'] ){
        Status::error( 'กรุณากรอกเวลาสิ้นสุดกิจกรรม !!!', array('title'=>"เวลาสิ้นสุดกิจกรรม", 'onfocus'=>"end_time") );
    }else if( !Helper::validTime($_POST['end_time']) ){
        Status::error( 'กรุณาเช็คเวลาเริ่มต้นกิจกรรม !!!', array('title'=>"เวลาไม่ถูกต้อง", 'onfocus'=>"end_time") );
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
    $fields .= ',`events_type`';
    $values .= ",:events_type";
    $parameters['events_type'] = $_POST['events_type'];
    $fields .= ',`start_date`';
    $values .= ",:start_date";
    $parameters['start_date'] = Helper::dateSave($_POST['start_date']).' '.$_POST['start_time'].':00';
    $fields .= ',`end_date`';
    $values .= ",:end_date";
    $parameters['end_date'] = Helper::dateSave($_POST['end_date']).' '.$_POST['end_time'].':00';
    if( (new datetime($parameters['start_date']))>=(new datetime($parameters['end_date'])) ){
        Status::error( 'เริ่มต้นกิจกรรม <= สิ้นสุดกิจกรรม !!!', array('title'=>"วันที่-เวลาไม่ถูกต้อง", 'onfocus'=>"end_date") );
    }
    if( DB::create("INSERT INTO `events` ($fields) VALUES ($values)", $parameters) ){
        Status::success( "เพิ่มกิจกรรมเรียบร้อยแล้ว", $parameters );
    }
    Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถเพิ่มได้") );
?>