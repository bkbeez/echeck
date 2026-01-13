<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    if( !isset($_POST['id'])||!$_POST['id'] ){
        Status::error( 'ไม่พบรหัส !!!' );
    }else if( !isset($_POST['events_id'])||!$_POST['events_id'] ){
        Status::error( 'ไม่พบรหัสกิจกรรม !!!' );
    }else if( !isset($_POST['type'])||!$_POST['type'] ){
        Status::error( 'กรุณาเลือกประเภทผู้เข้าร่วม !!!', array('onselect'=>"type") );
    }else if( $_POST['type']=='EMPLOYEE'&&(!isset($_POST['email'])||!$_POST['email']) ){
        Status::error( 'กรุณากรอกอีเมล !!!', array('onfocus'=>"email") );
    }else if( $_POST['type']=='STUDENT'&&(!isset($_POST['email'])||!$_POST['email']) ){
        Status::error( 'กรุณากรอกอีเมล !!!', array('onfocus'=>"email") );
    }else if( $_POST['type']=='STUDENT'&&(!isset($_POST['student_id'])||!$_POST['student_id']) ){
        Status::error( 'กรุณากรอกรหัสนักศึกษา !!!', array('onfocus'=>"student_id") );
    }else if( !isset($_POST['firstname'])||!$_POST['firstname'] ){
        Status::error( 'กรุณากรอกชื่อ !!!', array('onfocus'=>"firstname") );
    }else if( !isset($_POST['organization'])||!$_POST['organization'] ){
        Status::error( 'กรุณาระบุสังกัด !!!', array('onfocus'=>"organization") );
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
    $parameters['email'] = null;
    $datas .= ',`student_id`';
    $datas .= "=:student_id";
    $parameters['student_id'] = null;
    // Check exist
    if( $parameters['type']=='EMPLOYEE' ){
        $parameters['email'] = Helper::stringSave($_POST['email']);
        $check = User::one("SELECT id FROM events_lists WHERE events_id=:events_id AND id<>:id AND email=:email LIMIT 1;", array('id'=>$parameters['id'], 'events_id'=>$parameters['events_id'], 'email'=>$parameters['email']));
        if( isset($check['id'])&&$check['id'] ){
            Status::error( 'บุคลากรนี้มีอยู่แล้ว !!!', array('onfocus'=>"email") );
        }
    }else if( $parameters['type']=='STUDENT' ){
        $parameters['student_id'] = Helper::stringSave($_POST['student_id']);
        $check = User::one("SELECT id FROM events_lists WHERE events_id=:events_id AND id<>:id AND student_id=:student_id LIMIT 1;", array('id'=>$parameters['id'], 'events_id'=>$parameters['events_id'], 'student_id'=>$parameters['student_id']));
        if( isset($check['id'])&&$check['id'] ){
            Status::error( 'นักศึกษานี้มีอยู่แล้ว !!!', array('onfocus'=>"student_id") );
        }
    }
    // Name exist
    $checks = array();
    $checks['id'] = $parameters['id'];
    $checks['events_id'] = $parameters['events_id'];
    $checksql = "SELECT id FROM events_lists WHERE events_id=:events_id AND id<>:id";
    $datas .= ',`prefix`';
    $datas .= "=:prefix";
    $parameters['prefix'] = ( (isset($_POST['prefix'])&&$_POST['prefix']) ? Helper::stringSave($_POST['prefix']) : null );
    $datas .= ',`firstname`';
    $datas .= "=:firstname";
    $parameters['firstname'] = null;
    if( isset($_POST['firstname'])&&$_POST['firstname'] ){
        $parameters['firstname'] = Helper::stringSave($_POST['firstname']);
        $checks['firstname'] = $parameters['firstname'];
        $checksql .= " AND firstname=:firstname";
    }else{
        $checksql .= " AND firstname IS NULL";
    }
    $datas .= ',`lastname`';
    $datas .= "=:lastname";
    $parameters['lastname'] = null;
    if( isset($_POST['lastname'])&&$_POST['lastname'] ){
        $parameters['lastname'] = Helper::stringSave($_POST['lastname']);
        $checks['lastname'] = $parameters['lastname'];
        $checksql .= " AND lastname=:lastname";
    }else{
        $checksql .= " AND lastname IS NULL";
    }
    $namecheck = User::one($checksql, $checks);
    if( isset($namecheck['id'])&&$namecheck['id'] ){
        Status::error( 'ชื่อนี้มีอยู่แล้ว !!!', array('onfocus'=>"firstname") );
    }
    $datas .= ',`organization`';
    $datas .= "=:organization";
    $parameters['organization'] = null;
    $datas .= ',`department`';
    $datas .= "=:department";
    $parameters['department'] = null;
    if( $_POST['organization']=='EMPTY' ){
        $parameters['organization'] = null;
        $parameters['department'] = null;
    }else if( $_POST['organization']=='OTHER' ){
        if( !isset($_POST['organization_other'])||!$_POST['organization_other'] ){
            Status::error( 'กรุณากรอกชื่อสังกัด !!!', array('onfocus'=>"organization_other") );
        }
        $parameters['organization'] = Helper::stringSave($_POST['organization_other']);
    }else{
        $parameters['organization'] = ( (isset($_POST['organization'])&&$_POST['organization']) ? Helper::stringSave($_POST['organization']) : null );
        if( $parameters['organization']=='คณะศึกษาศาสตร์' ){
            $parameters['department'] = ( (isset($_POST['department'])&&$_POST['department']) ? Helper::stringSave($_POST['department']) : null );
            if( $parameters['department']=='OTHER' ){
                if( !isset($_POST['department_other'])||!$_POST['department_other'] ){
                    Status::error( 'กรุณากรอกชื่อหน่วยงาน/แผนก !!!', array('onfocus'=>"department_other") );
                }
                $parameters['department'] = Helper::stringSave($_POST['department_other']);
            }
        }else{
            $parameters['department'] = ( (isset($_POST['department'])&&$_POST['department']&&$_POST['department']!='OTHER') ? Helper::stringSave($_POST['department']) : null );
        }
    }
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