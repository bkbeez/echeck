<?php // Auto load Config, Library and Models
    ob_start();
    session_start();
    // Environment
    if( file_exists($_SERVER["DOCUMENT_ROOT"].'/app/install/env.conf') ){
        $config = explode("\n", file_get_contents($_SERVER["DOCUMENT_ROOT"].'/app/install/env.conf'));
        if( isset($config)&&count($config)>0 ){
            foreach($config as $env){
                if( $env ){
                    $envs = explode('=', $env);
                    define(trim($envs[0]), trim($envs[1]));
                }
            }
        }
    }
    if( (defined('IS_LOCAL')&&IS_LOCAL)||(in_array($_SERVER["HTTP_HOST"], array('127.0.0.1','localhost'))) ){
        if( intval(phpversion())>=8 ){
            error_reporting(E_ALL);
            ini_set('display_errors',1);
        }
        define('APP_HOST', 'http://'.$_SERVER["HTTP_HOST"]);
    }else{
        error_reporting(0);
        define('APP_HOST', 'https://'.$_SERVER["HTTP_HOST"]);
    }
    // Application
    define('APP_PATH', '');
    define('APP_ROOT', $_SERVER["DOCUMENT_ROOT"].APP_PATH);
    define('APP_HOME', APP_HOST.APP_PATH);
    define('APP_CODE', 'CHECKIN');
    define('APP_NAME', 'ระบบลงทะเบียนกิจกรรม');
    define('APP_FACT_TH', 'คณะศึกษาศาสตร์ มหาวิทยาลัยเชียงใหม่');
    define('APP_FACT_EN', 'Faculty of Education, Chiang Mai University');
    define('APP_ADDR_TH', '239 ถ.ห้วยแก้ว ต.สุเทพ อ.เมืองเชียงใหม่ จ.เชียงใหม่ 50200');
    define('APP_ADDR_EN', '239, Huay Kaew Road, Muang District,Chiang Mai Thailand, 50200');
    define('APP_EMAIL', 'it.edu@cmu.ac.th');
    define('APP_PHONE', '053-941215');
    define('APP_HEADER', APP_ROOT.'/app/assets/header.php');
    define('APP_FOOTER', APP_ROOT.'/app/assets/footer.php');
    define('THEME_JS', APP_PATH.'/app/assets/js');
    define('THEME_CSS', APP_PATH.'/app/assets/css');
    define('THEME_IMG', APP_PATH.'/app/assets/img');
    // Library
    require_once(APP_ROOT.'/app/library/db.class.php');
    include_once(APP_ROOT."/app/library/api.class.php");
    require_once(APP_ROOT.'/app/library/log.class.php');
    require_once(APP_ROOT.'/app/library/auth.class.php');
    require_once(APP_ROOT.'/app/library/lang.class.php');
    require_once(APP_ROOT.'/app/library/mail.class.php');
    require_once(APP_ROOT.'/app/library/helper.class.php');
    require_once(APP_ROOT.'/app/library/status.class.php');
    require_once(APP_ROOT.'/app/library/checkin.class.php');
    // Model
    $models = opendir(APP_ROOT."/app/models/");
    while (($inclass=readdir($models))!==false) {
        if( preg_match("/.php/i", $inclass) ){
            require_once(APP_ROOT."/app/models/".$inclass);
        }
    }
    $index = array();
 ?>