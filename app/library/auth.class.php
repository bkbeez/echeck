<?php
/**
 * Auth Class
 */
class Auth {

    /**
     * Login
     * @param  email
     * @return boolean
     */
    static function login($email, $account=array())
    {
        $member = DB::one("SELECT member.*
                            , TRIM(CONCAT(COALESCE(member.title,''),member.name,' ',COALESCE(member.surname,''))) AS fullname
                            , member_permission.role AS staff_role
                            FROM member
                            LEFT JOIN member_permission ON member.email=member_permission.email
                            WHERE member.email=:email
                            LIMIT 1;"
                            , array('email'=>$email)
        );
        if( isset($member['id'])&&$member['id'] ){
            if( isset($member['staff_role'])&&$member['staff_role'] ){
                if( $member['staff_role']=='ADMIN' ){
                    $_SESSION['login']['admin'] = 1;
                }else{
                    $_SESSION['login']['staff'] = $member['staff_role'];
                }
            }
            $_SESSION['login']['user'] = $member;
            unset($_SESSION['login']['user']['date_create']);
            unset($_SESSION['login']['user']['date_update']);
            if( isset($account['picture_default'])&&$account['picture_default']!=$member['picture_default'] ){
                $_SESSION['login']['user']['picture_default'] = $account['picture_default'];
                DB::update("UPDATE `member` SET `picture_default`=:picture_default,`date_update`=NOW() WHERE id=:id AND email=:email;", array('id'=>$member['id'],'email'=>$member['email'],'picture_default'=>$account['picture_default']));
            }
            // Agent
            $agent = $_SERVER['HTTP_USER_AGENT'];
            $_SESSION['login']['user']['device'] = null;
            $_SESSION['login']['user']['platform'] = null;
            if (preg_match('/Android/i', $agent)||preg_match('/android/i', $agent)) {
                $_SESSION['login']['user']['device'] = "Mobile";
                $_SESSION['login']['user']['platform'] = 'Android';
            }else if (preg_match('/webOS/i', $agent)||preg_match('/webos/i', $agent)) {
                $_SESSION['login']['user']['device'] = "Mobile";
                $_SESSION['login']['user']['platform'] = 'Web OS';
            }else if (preg_match('/iPhone/i', $agent)||preg_match('/iphone/i', $agent)) {
                $_SESSION['login']['user']['device'] = "Mobile";
                $_SESSION['login']['user']['platform'] = 'iOS';
            }else if (preg_match('/iPad/i', $agent)||preg_match('/ipad/i', $agent)) {
                $_SESSION['login']['user']['device'] = "Mobile";
                $_SESSION['login']['user']['platform'] = 'iOS';
            }else if (preg_match('/iPod/i', $agent)||preg_match('/ipod/i', $agent)) {
                $_SESSION['login']['user']['device'] = "Mobile";
                $_SESSION['login']['user']['platform'] = 'iOS';
            }else if (preg_match('/BlackBerry/i', $agent)||preg_match('/blackberry/i', $agent)) {
                $_SESSION['login']['user']['device'] = "Mobile";
                $_SESSION['login']['user']['platform'] = 'Black Berry';
            }else if (preg_match('/Windows Phone/i', $agent)||preg_match('/windows phone/i', $agent)) {
                $_SESSION['login']['user']['device'] = "Mobile";
            }else if (preg_match('/IEMobile/i', $agent)||preg_match('/iemobile/i', $agent)) {
                $_SESSION['login']['user']['device'] = "Mobile";
            }else if (preg_match('/Opera Mini/i', $agent)||preg_match('/opera mini/i', $agent)) {
                $_SESSION['login']['user']['device'] = "Mobile";
            }else if (preg_match('/linux/i', $agent)) {
                $_SESSION['login']['user']['device'] = "Desktop";
                $_SESSION['login']['user']['platform'] = 'Linux';
            }else if (preg_match('/macintosh|mac os x/i', $agent)) {
                $_SESSION['login']['user']['device'] = "Desktop";
                $_SESSION['login']['user']['platform'] = 'Mac OS';
            }else if (preg_match('/windows|win32/i', $agent)) {
                $_SESSION['login']['user']['device'] = "Desktop";
                $_SESSION['login']['user']['platform'] = 'Windows';
            }
            $_SESSION['login']['user']['browser'] = null;
            if(preg_match('/Opera/i',$agent) || preg_match('/OPR/i',$agent)) {
                $_SESSION['login']['user']['browser'] = 'Opera'; 
            }else if((preg_match('/MSIE/i',$agent) || preg_match('/.NET/i',$agent) || preg_match('/Trident/i',$agent))){ 
                $_SESSION['login']['user']['browser'] = 'Internet Explorer';
            }else if(preg_match('/Firefox/i',$agent)) { 
                $_SESSION['login']['user']['browser'] = 'Mozilla Firefox'; 
            }else if(preg_match('/Chrome/i',$agent)) { 
                $_SESSION['login']['user']['browser'] = 'Google Chrome'; 
            }else if(preg_match('/Safari/i',$agent)) { 
                $_SESSION['login']['user']['browser'] = 'Apple Safari'; 
            }else if(preg_match('/Netscape/i',$agent)) { 
                $_SESSION['login']['user']['browser'] = 'Netscape'; 
            }else if(preg_match('/Baidu/i',$agent)) { 
                $_SESSION['login']['user']['browser'] = 'Baidu'; 
            }
            $_SESSION['login']['user']['ip_server'] = null;
            if( isset($_SERVER['SERVER_ADDR'])&&$_SERVER['SERVER_ADDR'] ){
                $_SESSION['login']['user']['ip_server'] = $_SERVER['SERVER_ADDR'];
            }
            $_SESSION['login']['user']['ip_client'] = null;
            if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $_SESSION['login']['user']['ip_client'] = $_SERVER['HTTP_CLIENT_IP'];
            }else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $_SESSION['login']['user']['ip_client'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }else{
                $_SESSION['login']['user']['ip_client'] = $_SERVER['REMOTE_ADDR'];
            }
            // Log
            Log::user( array('action'=>'login', 'status'=>200, 'message'=>'success') );

            return true;
        }else if( isset($account['id'])&&$account['id'] ){
            $member = array();
            $member['id'] = $account['id'];
            $member['email'] = $email;
            $member['name'] = ( (isset($account['name'])&&$account['name']) ? $account['name'] : null );
            $member['surname'] = ( (isset($account['surname'])&&$account['surname']) ? $account['surname'] : null );
            $member['picture_default'] = ( (isset($account['picture_default'])&&$account['picture_default']) ? $account['picture_default'] : null );
            if( DB::create("INSERT INTO `member` (`id`,`email`,`name`,`surname`,`picture_default`,`date_create`,`date_update`) VALUES (:id,:email,:name,:surname,:picture_default,NOW(),NOW());", $member) ){
                if( $account['id']=='102030405060708090100' ){
                    DB::create("INSERT INTO `member_permission` (`email`,`role`,`date_modify`) VALUES (:email,'ADMIN', NOW());", array('email'=>$member['email']));
                }
                return Auth::login($member['email']);
            }
        }

        return false;
    }

    /**
     * Admin
     * @param  void
     * @return true/false
     */
    static function admin()
    {
        if( isset($_SESSION['login'])&&isset($_SESSION['login']['admin'])&&isset($_SESSION['login']['admin']) ){
            return true;
        }

        return false;
    }

    /**
     * Ajax
     * @param  void
     * @return json
     */
    static function ajax($redirect='/')
    {
        if( !isset($_SESSION['login']) ){
            $_SESSION['login_redirect'] = $redirect;
            echo json_encode(array('login'=>true, 'title'=>"Login time out !!!", 'text'=>"Please login again !!!", 'url'=>$redirect));
            exit();
        }
    }

    /**
     * Check
     * @param  redirect
     * @return redirect or boolean
     */
    static function check($redirect=null)
    {
        if( isset($_SESSION['login']) ){
            return true;
        }else if( $redirect ){
            header("location:".(($_SERVER['SERVER_PORT']==443)?'https://':'http://').$_SERVER["HTTP_HOST"]).$redirect;
            exit();
        }

        return false;
    }

}
?>