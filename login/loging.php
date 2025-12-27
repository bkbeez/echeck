<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    if( !isset($_POST['login_as'])||!$_POST['login_as'] ){
        Status::error( 'Not found mode !!!' );
    }else if( !isset($_POST['admin_email'])||!$_POST['admin_email'] ){
        Status::error( 'Not found admin email !!!' );
    }else if( $_POST['login_as']=='member'&&(!isset($_POST['member_email'])||!$_POST['member_email']) ){
        Status::error( 'Not found member email !!!' );
    }
    // Begin
    $admin_email = Helper::stringSave($_POST['admin_email']);
    $check = DB::one("SELECT id,email FROM member WHERE role='ADMIN' AND member.email=:email LIMIT 1;", array('email'=>$admin_email));
    if( $_POST['login_as']=='admin' ){
        if( isset($check['id'])&&$check['id'] ){
            if( Auth::login($check['email']) ){
                $redirect = APP_HOME;
                if( isset($_SESSION['login_redirect']) ){
                    $redirect = $_SESSION['login_redirect'];
                    unset($_SESSION['login_redirect']);
                }
                Status::success( 'เข้าสู่ระบบเรียบร้อยแล้ว' , array('url'=>$redirect) );
            }
        }else if( Helper::isLocal()&&$admin_email=='admin@mail.com' ){
            $member = array();
            $member['id'] = '102030405060708090100';
            $member['role'] = 'ADMIN';
            $member['email'] = $admin_email;
            $member['name'] = "ผู้ดูแลระบบ";
            if( DB::create("INSERT INTO `member` (`id`,`role`,`email`,`name`,`date_create`,`date_update`) VALUES (:id,:role,:email,:name,NOW(),NOW());", $member) ){
                if( Auth::login($member['email']) ){
                    $redirect = APP_HOME;
                    if( isset($_SESSION['login_redirect']) ){
                        $redirect = $_SESSION['login_redirect'];
                        unset($_SESSION['login_redirect']);
                    }
                    Status::success( 'เข้าสู่ระบบเรียบร้อยแล้ว' , array('url'=>$redirect) );
                }
            }
        }
    }else if( $_POST['login_as']=='member' ){
        if( isset($check['id'])&&$check['id'] ){
            if( Auth::login(Helper::stringSave($_POST['member_email'])) ){
                $redirect = APP_HOME;
                if( isset($_SESSION['login_redirect']) ){
                    $redirect = $_SESSION['login_redirect'];
                    unset($_SESSION['login_redirect']);
                }
                Status::success( 'เข้าสู่ระบบเรียบร้อยแล้ว' , array('url'=>$redirect) );
            }
        }
    }
    // Done
    Status::error( "กรุณาตรวจสอบและลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถเข้าสู่ระบบได้") );
?>