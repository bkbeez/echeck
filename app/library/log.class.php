<?php
/**
 * Log Class
 */
class Log {

    /**
     * Mail
     * @param  $logs
     * @return void
     */
    static function mail($logs){
        if( User::get('email') ){
            $logs['email'] = User::get('email');
            return DB::create("INSERT INTO `xlg_mail` (`date_at`,`email`,`subject`,`message`,`status`,`remark`) VALUES (NOW(),:email,:subject,:message,:status,:remark);", $logs);
        }

        return false;
    }

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
            return DB::create("INSERT INTO `xlg_user` (`date_at`,`email`,`device`,`platform`,`browser`,`ip_client`,`ip_server`,`action`,`status`,`remark`) VALUES (NOW(),:email,:device,:platform,:browser,:ip_client,:ip_server,:action,:status,:remark);", $logs);
        }

        return false;
    }


    /**
     * Change
     * @param  $logs
     * @return void
     */
    static function change($logs){
        if( User::get('email') ){
            $logs['email'] = User::get('email');
            return DB::create("INSERT INTO `xlg_change` (`date_at`,`email`,`title`,`remark`) VALUES (NOW(),:email,:title,:remark);", $logs);
        }

        return false;
    }

    /**
     * Participant
     * @param  $logs
     * @return void
     */
    static function participant($logs){
        if( User::get('email') ){
            $logs['status_by'] = User::get('email');
            return DB::create("INSERT INTO `xlg_participant` (`date_at`,`meeting_id`,`member_id`,`status`,`status_by`) VALUES (NOW(),:meeting_id,:member_id,:status,:status_by);", $logs);
        }

        return false;
    }

}
?>