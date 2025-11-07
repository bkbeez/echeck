<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    if( !isset($_POST['login_as'])||!$_POST['login_as'] ){
        Status::error( Lang::get('NotFound').Lang::get('Type').' !!!' );
    }else if( $_POST['login_as']=='member'&&(!isset($_POST['email'])||!$_POST['email']) ){
        Status::error( Lang::get('NotFound').Lang::get('Email').' !!!' );
    }
    if( $_POST['login_as']=='member' ){
        if( Auth::login(Helper::stringSave($_POST['email'])) ){
            $redirect = APP_HOME;
            if( isset($_SESSION['login_redirect']) ){
                $redirect = $_SESSION['login_redirect'];
                unset($_SESSION['login_redirect']);
            }
            Status::success( ( (App::lang()=='en') ? 'Login successfully.' : 'เข้าสู่ระบบเรียบร้อยแล้ว' ) , array('url'=>$redirect) );
        }
    }else{
        if( Auth::login('admin@mail.com', array('id'=>'102030405060708090100', 'name'=>'ผู้ดูแลระบบ')) ){
            $redirect = APP_HOME;
            if( isset($_SESSION['login_redirect']) ){
                $redirect = $_SESSION['login_redirect'];
                unset($_SESSION['login_redirect']);
            }
            Status::success( ( (App::lang()=='en') ? 'Login successfully.' : 'เข้าสู่ระบบเรียบร้อยแล้ว' ) , array('url'=>$redirect) );
        }
    }
    Status::error( Lang::get('PleaseTryAgain'), array('title'=>( (App::lang()=='en') ? 'Can not Login.' : 'ไม่สามารถเข้าสู่ระบบได้' )) );
?>