<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/admin/?users'); ?>
<?php
    if( !isset($_POST['role'])||!$_POST['role'] ){
        Status::error( 'กรุณาเลือกประเภทผู้ใช้ !!!', array('onselect'=>"role") );
    }else if( !isset($_POST['email'])||!$_POST['email'] ){
        Status::error( 'กรุณากรอกอีเมล !!!', array('onfocus'=>"email") );
    }else if( !isset($_POST['name'])||!$_POST['name'] ){
        Status::error( 'กรุณากรอกชื่อ !!!', array('onfocus'=>"name") );
    }else if( (isset($_POST['is_cmu'])&&$_POST['is_cmu']=='Y')&&(!isset($_POST['email_cmu'])||!$_POST['email_cmu']) ){
        Status::error( 'กรุณากรอก CMU Mail !!!', array('onfocus'=>"email_cmu") );
    }
    // Check
    $email = Helper::stringSave($_POST['email']);
    $check = User::one("SELECT id FROM member WHERE email=:email LIMIT 1;", array('email'=>$email));
    if( isset($check['id'])&&$check['id'] ){
        Status::error( 'อีเมลนี้มีอยู่แล้ว !!!', array('onfocus'=>"email") );
    }
    // Begin
    $parameters = array();
    $fields = "`id`";
    $values = ":id";
    $parameters['id'] = (new datetime())->format("YmdHis").Helper::randomNumber(7);
    $fields .= ',`role`';
    $values .= ",:role";
    $parameters['role'] = $_POST['role'];
    $fields .= ',`email`';
    $values .= ",:email";
    $parameters['email'] = $email;
    $fields .= ',`title`';
    $values .= ",:title";
    $parameters['title'] = ( (isset($_POST['title'])&&$_POST['title']) ? Helper::stringSave($_POST['title']) : null );
    $fields .= ',`name`';
    $values .= ",:name";
    $parameters['name'] = ( (isset($_POST['name'])&&$_POST['name']) ? Helper::stringSave($_POST['name']) : null );
    $fields .= ',`surname`';
    $values .= ",:surname";
    $parameters['surname'] = ( (isset($_POST['surname'])&&$_POST['surname']) ? Helper::stringSave($_POST['surname']) : null );
    $fields .= ',`is_cmu`';
    $values .= ",:is_cmu";
    $parameters['is_cmu'] = ( (isset($_POST['is_cmu'])&&$_POST['is_cmu']) ? $_POST['is_cmu'] : 'N' );
    $fields .= ',`email_cmu`';
    $values .= ",:email_cmu";
    $parameters['email_cmu'] = null;
    if( $parameters['is_cmu']=='Y' ){
        $parameters['email_cmu'] = ( (isset($_POST['email_cmu'])&&$_POST['email_cmu']) ? Helper::stringSave($_POST['email_cmu']) : null );
    }
    $fields .= ',`date_create`';
    $values .= ",NOW()";
    $fields .= ',`user_create`';
    $values .= ",:user_create";
    $parameters['user_create'] = User::get('email');
    if( User::create("INSERT INTO `member` ($fields) VALUES ($values)", $parameters) ){
        $logs = array();
        $logs['member_id'] = $parameters['id'];
        $logs['mode'] = "CREATE";
        $logs['title'] = "Create user";
        $logs['remark'] = $parameters['email'].( $parameters['email_cmu'] ? '|'.$parameters['email_cmu']: null );
        User::log($logs);
        Status::success( $parameters['email']." ถูกเพิ่มเข้าสู่ระบบแล้ว", array('title'=>"เพิ่มผู้ใช้เรียบร้อยแล้ว") );
    }
    Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถเพิ่มได้") );
?>