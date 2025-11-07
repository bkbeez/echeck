<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php require_once($_SERVER["DOCUMENT_ROOT"].'/app/library/google.class.php'); ?>
<?php require_once($_SERVER["DOCUMENT_ROOT"].'/app/library/googleinfo.class.php'); ?>
<?php
    $signin = new Google();
    $signin->setCallbackUri(APP_HOME.'/login/signingoogle.php');
    $signin->setScope('https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile');
    if( isset($_GET['code'])&&!empty($_GET['code']) ){
        $code = $_GET['code'];
        $codecheck = $signin->getAccessTokenAuthCode($code);
        if( isset($codecheck->access_token)&&!empty($codecheck->access_token) ){
            $info = new Googleinfo();
            $infocheck = $info->getBasicinfo($codecheck->access_token);
            if( isset($infocheck->email)&&$infocheck->email ){
                $account = array();
                $account['id'] = $infocheck->sub;
                $account['name'] = (isset($infocheck->given_name) ? preg_replace('/[^a-zA-Z0-9]/s', '', $infocheck->given_name) : '');
                $account['surname'] = (isset($infocheck->family_name) ? preg_replace('/[^a-zA-Z0-9]/s', '', $infocheck->family_name) : '');
                $account['picture_default'] = ( (isset($infocheck->picture)&&$infocheck->picture) ? $infocheck->picture : null );
                if( Auth::login($infocheck->email, $account) ){
                    $redirect = APP_HOME;
                    if( isset($_SESSION['login_redirect']) ){
                        $redirect = $_SESSION['login_redirect'];
                        unset($_SESSION['login_redirect']);
                    }
                    header('Location: '.$redirect);
                    exit;
                }
            }
        }
        $_SESSION['deny'] = array();
        $_SESSION['deny']['title'] = ( (App::lang()=='en') ? 'Oops! Google Account Not Found' : 'ขออภัย! ไม่พบบัญชี Google นี้' );
        $_SESSION['deny']['message'] = ( (App::lang()=='en') ? 'Please check your Google Account, And Try login again !!!' : 'โปรดตรวจสอบบัญชี Google ของท่าน และลองใหม่อีกครั้ง !!!' );
        header('Location: '.APP_HOME.'/deny');
        exit;
    }else{
        $signin->initGoogle();
    }
?>