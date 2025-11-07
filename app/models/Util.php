<?php
/**
 * Util Class
 */
class Util extends DB {

    /**
     * One
     * @param  sql, parameters
     * @return array
     */
    static function one($sql, $parameters=array(), $init=null){
        $result = DB::query($sql, $parameters);
        return ( isset($result[0]) ? $result[0] : null );
    }

    /**
     * Sql
     * @param  sql, parameters
     * @return array
     */
    static function sql($sql, $parameters=array(), $init=null){
        return DB::query($sql, $parameters);
    }

    /**
     *  Meeting
     *  @param  key
     *  @return name
     */
    static function meeting($key='id', $default=null)
    {
        if( $key=='meeting_date' ){
            $check = Util::one("SELECT date_start,date_end FROM `meeting` WHERE status_id=1 ORDER BY id DESC LIMIT 1;");
            if(isset($check['date_start'])&&$check['date_start']){
                $meeting_date = '<i class="uil uil-calendar-alt"></i> '.Helper::dateDisplay($check['date_start'], App::lang());
                if(isset($check['date_end'])&&$check['date_end']!=$check['date_start']){
                    $meeting_date .= ' - '.Helper::dateDisplay($check['date_end'], App::lang());
                }
                return $meeting_date;
            }
        }else{
            $check = Util::one("SELECT `".$key."` FROM `meeting` WHERE status_id=1 ORDER BY id DESC LIMIT 1;");
            return ( (isset($check[$key])&&$check[$key]) ? $check[$key] : $default );
        }
        return $default;
    }

    /**
     *  Country
     *  @param  id
     *  @return name
     */
    static function country($id)
    {
        $lang = App::lang();
        $check = Util::one("SELECT * FROM tbl_country WHERE id=:id LIMIT 1;", array('id'=>$id));
        return ( (isset($check['name_'.$lang])&&$check['name_'.$lang]) ? $check['name_'.$lang] : null );
    }

    /**
     *  Institute
     *  @param  id
     *  @return name
     */
    static function institute($id, $default=null)
    {
        $lang = App::lang();
        $check = Util::one("SELECT * FROM tbl_institute WHERE id=:id LIMIT 1;", array('id'=>$id));
        return ( (isset($check['name_'.$lang])&&$check['name_'.$lang]) ? $check['name_'.$lang] : $default );
    }

    /**
     *  Participant
     *  @param  id
     *  @return name
     */
    static function participant($id)
    {
        $lang = App::lang();
        $check = Util::one("SELECT * FROM tbl_participant WHERE id=:id LIMIT 1;", array('id'=>$id));
        return ( (isset($check['name_'.$lang])&&$check['name_'.$lang]) ? $check['name_'.$lang] : null );
    }

    /**
     *  Billing
     *  @param  $parameters, $year
     *  @return boolean
     */
    static function billing($parameters, $year)
    {
        $parameters['prefix'] = BILL_PREFIX;
        $parameters['date_at'] = (new datetime())->format('Y-m-d H:i:s');
        $parameters['user_by'] = User::get('email');
        $parameters['billing_id'] = DB::createLastInsertId("INSERT INTO `".$year."_billing` (`prefix`,`date_at`,`user_by`,`meeting_id`,`member_id`,`amount`) VALUES (:prefix,:date_at,:user_by,:meeting_id,:member_id,:amount);", $parameters);
        if( $parameters['billing_id'] ){
            return DB::update("UPDATE `meeting_participant` SET `billing_id`=:billing_id, `billing_status`=NULL WHERE meeting_id=:meeting_id AND member_id=:member_id;", array('billing_id'=>$parameters['billing_id'], 'meeting_id'=>$parameters['meeting_id'], 'member_id'=>$parameters['member_id']));
        }

        return false;
    }

    /**
     *  Cancel Billing
     *  @param  $parameters, $year
     *  @return boolean
     */
    static function cancelBilling($parameters, $year)
    {
        $parameters['date_cancel'] = (new datetime())->format('Y-m-d H:i:s');
        $parameters['user_cancel'] = User::get('email');
        if( DB::update("UPDATE `".$year."_billing` SET `status_id`=0,`date_cancel`=:date_cancel,`user_cancel`=:user_cancel,`note_cancel`=:note_cancel WHERE id=:id AND meeting_id=:meeting_id AND member_id=:member_id;", $parameters) ){
            return DB::update("UPDATE `meeting_participant` SET `billing_id`=NULL, `billing_status`='C' WHERE billing_id=:billing_id AND meeting_id=:meeting_id AND member_id=:member_id;", array('billing_id'=>$parameters['id'], 'meeting_id'=>$parameters['meeting_id'], 'member_id'=>$parameters['member_id']));
        }

        return false;
    }

    /**
     *  Refund Billing
     *  @param  $parameters, $year
     *  @return boolean
     */
    static function refundBilling($parameters, $year)
    {
        $check = Util::one("SELECT id FROM `".$year."_billing` WHERE meeting_id=:meeting_id AND member_id=:member_id ORDER BY id DESC LIMIT 1;", array('meeting_id'=>$parameters['meeting_id'], 'member_id'=>$parameters['member_id']));
        if( isset($check['id'])&&$check['id'] ){
            $parameters['id'] = $check['id'];
            if( DB::update("UPDATE `".$year."_billing` SET `status_id`=2,`refund_cancel`=:refund_cancel WHERE id=:id AND meeting_id=:meeting_id AND member_id=:member_id;", $parameters) ){
                return DB::update("UPDATE `meeting_participant` SET `billing_id`=:billing_id, `billing_status`='R' WHERE meeting_id=:meeting_id AND member_id=:member_id;", array('billing_id'=>$parameters['id'], 'meeting_id'=>$parameters['meeting_id'], 'member_id'=>$parameters['member_id']));
            }
        }

        return false;
    }

    /**
     *  Get Billing
     *  @param  $parameters, $year
     *  @return array
     */
    static function getBilling($parameters, $year, $refund=false)
    {
        if( $refund ){
            $check = Util::one("SELECT billing.*
                                , meeting_participant.payslip_amount AS amount
                                FROM `".$year."_billing` AS billing
                                LEFT JOIN meeting_participant ON billing.meeting_id=meeting_participant.meeting_id AND billing.member_id=meeting_participant.member_id AND billing.id=meeting_participant.billing_id
                                WHERE billing.meeting_id=:meeting_id
                                AND billing.member_id=:member_id
                                AND billing.status_id=2
                                LIMIT 1;"
                                , $parameters
            );
        }else{
            $check = Util::one("SELECT billing.*
                                , meeting_participant.payslip_amount AS amount
                                FROM `".$year."_billing` AS billing
                                LEFT JOIN meeting_participant ON billing.meeting_id=meeting_participant.meeting_id AND billing.member_id=meeting_participant.member_id AND billing.id=meeting_participant.billing_id
                                WHERE billing.meeting_id=:meeting_id
                                AND billing.member_id=:member_id
                                AND billing.date_cancel IS NULL
                                AND billing.status_id=1
                                LIMIT 1;"
                                , $parameters
            );
        }
        if( isset($check['id'])&&$check['id'] ){
            $result = $check;
            $result['fullnumber'] = $check['prefix'].'-'.sprintf("%1$05d",$check['id']);
            return $result;
        }

        return null;
    }

    /**
     *  Get Billings
     *  @param  $parameters, $year
     *  @return array
     */
    static function getBillings($parameters, $year)
    {
        $checks = Util::sql("SELECT billing.*
                            FROM `".$year."_billing` AS billing
                            WHERE billing.meeting_id=:meeting_id
                            AND billing.member_id=:member_id
                            AND billing.date_cancel IS NOT NULL
                            ORDER BY billing.id;"
                            , $parameters
        );
        if( isset($checks)&&count($checks)>0 ){
            $htmls = '<div class="form-floating mb-1">';
                $htmls .= '<div class="form-control on-text-display" style="border-color:#e2636c;padding:5px 5px 0 5px;">';
                    foreach($checks as $check){
                        $fullnumber = $check['prefix'].'-'.sprintf("%1$05d",$check['id']);
                        $htmls .= '<div class="card card-border-start bg-soft-red border-red mb-1">';
                            $htmls .= '<div class="card-body" style="padding:5px 10px;">';
                                $htmls .= '<span class="btn btn-sm btn-outline-red" style="padding:0 5px;" onclick="billing_events(\'preview\', { \'self\':this });"><img src="'.THEME_IMG.'/filetype/pdf.png" bill-no="'.$fullnumber.'" bill-id="'.$check['id'].'" style="height:16px;"/>&nbsp;'.$fullnumber.'</span>';
                                $htmls .= ' <font class="text-red">';
                                    $htmls .= ( (App::lang()=='en') ? 'Cancelled' : 'ยกเลิกแล้ว' );
                                    if( $check['status_id']==2 ){
                                        $htmls .= ( (App::lang()=='en') ? '/Refuned' : '/คืนเงินแล้ว' );
                                    }
                                $htmls .= '</font>';
                            $htmls .= '</div>';
                        $htmls .= '</div>';
                    }
                $htmls .= '</div>';
            $htmls .= '</div>';
            return $htmls;
        }

        return null;
    }

    /**
     *  Counter
     *  @param  void
     *  @return void
     */
    static function counter($page='home')
    {
        $visits = array();
        $visits['page'] = $page;
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $visits['device'] = null;
        $visits['platform'] = null;
        if (preg_match('/Android/i', $agent)||preg_match('/android/i', $agent)) {
            $visits['device'] = "Mobile";
            $visits['platform'] = 'Android';
        }else if (preg_match('/webOS/i', $agent)||preg_match('/webos/i', $agent)) {
            $visits['device'] = "Mobile";
            $visits['platform'] = 'Web OS';
        }else if (preg_match('/iPhone/i', $agent)||preg_match('/iphone/i', $agent)) {
            $visits['device'] = "Mobile";
            $visits['platform'] = 'iOS';
        }else if (preg_match('/iPad/i', $agent)||preg_match('/ipad/i', $agent)) {
            $visits['device'] = "Mobile";
            $visits['platform'] = 'iOS';
        }else if (preg_match('/iPod/i', $agent)||preg_match('/ipod/i', $agent)) {
            $visits['device'] = "Mobile";
            $visits['platform'] = 'iOS';
        }else if (preg_match('/BlackBerry/i', $agent)||preg_match('/blackberry/i', $agent)) {
            $visits['device'] = "Mobile";
            $visits['platform'] = 'Black Berry';
        }else if (preg_match('/Windows Phone/i', $agent)||preg_match('/windows phone/i', $agent)) {
            $visits['device'] = "Mobile";
        }else if (preg_match('/IEMobile/i', $agent)||preg_match('/iemobile/i', $agent)) {
            $visits['device'] = "Mobile";
        }else if (preg_match('/Opera Mini/i', $agent)||preg_match('/opera mini/i', $agent)) {
            $visits['device'] = "Mobile";
        }else if (preg_match('/linux/i', $agent)) {
            $visits['device'] = "Desktop";
            $visits['platform'] = 'Linux';
        }else if (preg_match('/macintosh|mac os x/i', $agent)) {
            $visits['device'] = "Desktop";
            $visits['platform'] = 'Mac OS';
        }else if (preg_match('/windows|win32/i', $agent)) {
            $visits['device'] = "Desktop";
            $visits['platform'] = 'Windows';
        }
        $visits['browser'] = null;
        if(preg_match('/Opera/i',$agent) || preg_match('/OPR/i',$agent)) {
            $visits['browser'] = 'Opera'; 
        }else if((preg_match('/MSIE/i',$agent) || preg_match('/.NET/i',$agent) || preg_match('/Trident/i',$agent))){ 
            $visits['browser'] = 'Internet Explorer';
        }else if(preg_match('/Firefox/i',$agent)) { 
            $visits['browser'] = 'Mozilla Firefox'; 
        }else if(preg_match('/Chrome/i',$agent)) { 
            $visits['browser'] = 'Google Chrome'; 
        }else if(preg_match('/Safari/i',$agent)) { 
            $visits['browser'] = 'Apple Safari'; 
        }else if(preg_match('/Netscape/i',$agent)) { 
            $visits['browser'] = 'Netscape'; 
        }else if(preg_match('/Baidu/i',$agent)) { 
            $visits['browser'] = 'Baidu'; 
        }
        if( isset($_SERVER['SERVER_ADDR'])&&$_SERVER['SERVER_ADDR'] ){
            $visits['ip_server'] = $_SERVER['SERVER_ADDR'];
        }
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $visits['ip_client'] = $_SERVER['HTTP_CLIENT_IP'];
        }else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $visits['ip_client'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $visits['ip_client'] = $_SERVER['REMOTE_ADDR'];
        }
        if( (isset($visits['device'])&&$visits['device'])&&
            (isset($visits['platform'])&&$visits['platform'])&&
            (isset($visits['browser'])&&$visits['browser'])&&
            (isset($visits['ip_client'])&&$visits['ip_client'])&&
            (isset($visits['ip_server'])&&$visits['ip_server']) ){
            DB::create("REPLACE INTO `xlg_visits` (`date_at`,`device`,`platform`,`browser`,`ip_client`,`ip_server`,`page`,`time_at`) VALUES (DATE(NOW()),:device,:platform,:browser,:ip_client,:ip_server,:page,NOW());", $visits);
        }
    }

}
?>