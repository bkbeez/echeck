<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/admin/?users'); ?>
<?php
    if(!isset($_POST['id'])||!$_POST['id']){
        Status::error( 'ไม่พบรหัสผู้ใช้ !!!' );
    }else if( !isset($_POST['role'])||!$_POST['role'] ){
        Status::error( 'กรุณาเลือกประเภทผู้ใช้ !!!', array('onselect'=>"role") );
    }else if( !isset($_POST['email'])||!$_POST['email'] ){
        Status::error( 'กรุณากรอกอีเมล !!!', array('onfocus'=>"email") );
    }else if( !isset($_POST['name'])||!$_POST['name'] ){
        Status::error( 'กรุณากรอกชื่อ !!!', array('onfocus'=>"name") );
    }else if( (isset($_POST['is_cmu'])&&$_POST['is_cmu']=='Y')&&(!isset($_POST['email_cmu'])||!$_POST['email_cmu']) ){
        Status::error( 'กรุณากรอก CMU Mail !!!', array('onfocus'=>"email_cmu") );
    }
    // Begin
    $parameters = array();
    $parameters['id'] = $_POST['id'];
    // Check
    $check = User::one("SELECT * FROM member WHERE id=:id LIMIT 1;", $parameters);
    $datas  = '`role`';
    $datas .= "=:role";
    $parameters['role'] = $_POST['role'];
    $datas .= ',`email`';
    $datas .= "=:email";
    $parameters['email'] = $_POST['email'];
    $datas .= ',`title`';
    $datas .= "=:title";
    $parameters['title'] = ( (isset($_POST['title'])&&$_POST['title']) ? Helper::stringSave($_POST['title']) : null );
    $datas .= ',`name`';
    $datas .= "=:name";
    $parameters['name'] = ( (isset($_POST['name'])&&$_POST['name']) ? Helper::stringSave($_POST['name']) : null );
    $datas .= ',`surname`';
    $datas .= "=:surname";
    $parameters['surname'] = ( (isset($_POST['surname'])&&$_POST['surname']) ? Helper::stringSave($_POST['surname']) : null );
    $datas .= ',`is_cmu`';
    $datas .= "=:is_cmu";
    $parameters['is_cmu'] = ( (isset($_POST['is_cmu'])&&$_POST['is_cmu']) ? $_POST['is_cmu'] : 'N' );
    $datas .= ',`email_cmu`';
    $datas .= "=:email_cmu";
    $parameters['email_cmu'] = null;
    if( $parameters['is_cmu']=='Y' ){
        $parameters['email_cmu'] = ( (isset($_POST['email_cmu'])&&$_POST['email_cmu']) ? Helper::stringSave($_POST['email_cmu']) : null );
        $checkcmu = User::one("SELECT id FROM member WHERE email_cmu=:email_cmu LIMIT 1;", array('email_cmu'=>$parameters['email_cmu']));
        if( isset($checkcmu['id'])&&$checkcmu['id'] ){
            Status::error( 'CMU Mail นี้มีอยู่แล้ว !!!', array('onfocus'=>"email_cmu") );
        }
    }
    $datas .= ',`status`';
    $datas .= "=:status";
    $parameters['status'] = ( (isset($_POST['status'])&&$_POST['status']) ? $_POST['status'] : 1 );
    $datas .= ',`date_update`';
    $datas .= "=NOW()";
    $datas .= ',`user_update`';
    $datas .= "=:user_update";
    $parameters['user_update'] = User::get('email');
    if( User::update("UPDATE `member` SET $datas WHERE id=:id;", $parameters) ){
        if( $check['role']!=$parameters['role'] ){
            $logs = array();
            $logs['member_id'] = $parameters['id'];
            $logs['mode'] = "CHROLE";
            $logs['title'] = "Change role";
            $logs['remark'] = $check['role'].' &rang; '.$parameters['role'];
            User::log($logs);
        }
        if( $check['email_cmu']!=$parameters['email_cmu'] ){
            $logs = array();
            $logs['member_id'] = $parameters['id'];
            $logs['mode'] = "CHMAIL";
            $logs['title'] = "Change CMU Mail";
            $logs['remark'] = ( $check['email_cmu'] ? $check['email_cmu'] : 'EMPTY' );
            $logs['remark'] .= ' &rang; '.( $parameters['email_cmu'] ? $parameters['email_cmu'] : 'EMPTY' );
            User::log($logs);
        }
        if( $check['status']!=$parameters['status'] ){
            $logs = array();
            $logs['member_id'] = $parameters['id'];
            $logs['mode'] = "STATUS";
            $logs['title'] = "Change status";
            $logs['remark'] = ( $check['status']>1 ? 'ระงับใช้งาน' : 'พร้อมใช้งาน' );
            $logs['remark'] .= ' &rang; '.( $check['parameters']>1 ? 'ระงับใช้งาน' : 'พร้อมใช้งาน' );
            User::log($logs);
        }
        if( $parameters['email']==User::get('email') ){
            Auth::login(User::get('email'));
        }
        Status::success( "ข้อมูลถูกเปลี่ยนแปลงเรียบร้อยแล้ว", array('title'=>"บันทึกเรียบร้อยแล้ว") );
    }
    Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถบันทึกได้") );
?>