<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/admin/?users'); ?>
<?php
    if(!isset($_POST['id'])||!$_POST['id']){
        Status::error( Lang::get('NotFound').Lang::get('Id').' !!!' );
    }else if(!isset($_POST['email'])||!$_POST['email']){
        Status::error( Lang::get('NotFound').Lang::get('Email').' !!!' );
    }else if( !isset($_POST['name'])||!$_POST['name'] ){
        Status::error( Lang::get('MustFillIn').'...!!!', array('onfocus'=>'name') );
    /*}else if( !isset($_POST['surname'])||!$_POST['surname'] ){
        Status::error( Lang::get('MustFillIn').'...!!!', array('onfocus'=>'surname') );*/
    }
    // Begin
    $log = "";
    $logi = 0;
    $logs = array();
    $member = '';
    $members = array();
    $parameters = array();
    $parameters['id'] = $_POST['id'];
    $parameters['email'] = $_POST['email'];
    $check = DB::one("SELECT * FROM member WHERE id=:id AND email=:email LIMIT 1;", $parameters);
    $datas  = '`date_update`';
    $datas .= "=NOW()";
    $datas .= ',`title`';
    $datas .= "=:title";
    $parameters['title'] = ( (isset($_POST['title'])&&$_POST['title']) ? Helper::stringSave($_POST['title']) : null );
    $datas .= ',`name`';
    $datas .= "=:name";
    $parameters['name'] = ( (isset($_POST['name'])&&$_POST['name']) ? Helper::stringSave($_POST['name']) : null );
    $datas .= ',`surname`';
    $datas .= "=:surname";
    $parameters['surname'] = ( (isset($_POST['surname'])&&$_POST['surname']) ? Helper::stringSave($_POST['surname']) : null );
    if( (isset($check['title'])&&$check['title']!=$parameters['title'])||(isset($check['name'])&&$check['name']!=$parameters['name'])||(isset($check['surname'])&&$check['surname']!=$parameters['surname']) ){
        $log .= ", (NOW(),:email,:title_".$logi.",:remark_".$logi.",:user_by)";
        $logs['title_'.$logi]  = 'Change Name';
        $logs['remark_'.$logi]  = trim($check['title'].$check['name'].' '.$check['surname']);
        $logs['remark_'.$logi] .= ' &rang; '.trim($parameters['title'].$parameters['name'].' '.$parameters['surname']);
        $logi++;
    }
    if( DB::update("UPDATE `member` SET $datas WHERE id=:id AND email=:email;", $parameters) ){
        if( $logi>0 ){
            $logs['email'] = $parameters['email'];
            $logs['user_by'] = User::get('email');
            DB::create("INSERT INTO `xlg_member` (`date_at`,`email`,`title`,`remark`,`user_by`) VALUES ".substr($log,1).";", $logs);
        }
        if( $parameters['email']==User::get('email') ){
            Auth::login(User::get('email'));
        }
        Status::success( Lang::get('SuccessChange') );
    }
    Status::error( Lang::get('PleaseTryAgain'), array('title'=>Lang::get('CanNotChange')) );
?>