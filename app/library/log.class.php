<?php
/**
 * Log Class
 */
class Log {

    /**
     * User
     * @param  $logs
     * @return void
     */
    static function user($logs){
        if( User::get('email') ){
            $logs['email'] = User::get('email');
            $logs['device'] = User::get('device');
            $logs['platform'] = User::get('platform');
            $logs['browser'] = User::get('browser');
            $logs['ip_client'] = User::get('ip_client');
            $logs['ip_server'] = User::get('ip_server');
            return DB::create("INSERT INTO `xlg_login` (`date_at`,`email`,`device`,`platform`,`browser`,`ip_client`,`ip_server`,`action`,`status`,`message`) VALUES (NOW(),:email,:device,:platform,:browser,:ip_client,:ip_server,:action,:status,:message);", $logs);
        }

        return false;
    }

}
?>