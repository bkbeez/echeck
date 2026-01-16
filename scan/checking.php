<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/scan'); ?>
<?php
    if( !isset($_POST['participant_type'])||!$_POST['participant_type'] ){
        Status::error( 'ไม่พบประเภทกิจกรรม !!!' );
    }else if(!isset($_POST['events_id'])||!$_POST['events_id']){
        Status::error( 'ไม่พบรหัสกิจกรรม !!!' );
    }
    // Begin
    $today = new datetime();
    if( $_POST['participant_type']=='LIST' ){
        if( !isset($_POST['id'])||!$_POST['id'] ){
            Status::error( 'ไม่พบรหัสผู้เข้าร่วม !!!' );
        }
        $parameters = array();
        $parameters['id'] = $_POST['id'];
        $parameters['events_id'] = $_POST['events_id'];
        $datas  = '`status`';
        $datas .= "=:status";
        $parameters['status'] = 1;
        $datas .= ',`date_checkin`';
        $datas .= "=:date_checkin";
        $parameters['date_checkin'] = $today->format("Y-m-d H:i:s");
        $datas .= ',`user_checkin`';
        $datas .= "=:user_checkin";
        $parameters['user_checkin'] = ( (isset($_POST['email'])&&$_POST['email']) ? $_POST['email'] : null );
        if( DB::update("UPDATE `events_lists` SET $datas WHERE id=:id AND events_id=:events_id;", $parameters) ){
            $htmls = '<div class="d-flex flex-row on-success">';
                $htmls .= '<div style="display:none;"><div class="icon text-success me-2 mt-n3" style="font-size:52px;line-height:75px;"><i class="uil uil-calendar-alt"></i></div></div>';
                $htmls .= '<div style="display:none;">';
                    $htmls .= '<h5 class="mb-0 text-success on-font-primary">ลงทะเบียนเข้าร่วมแล้ว</h5>';
                    $htmls .= '<p class="on-text-normal text-success m-0" style="margin-top:-2px;line-height:18px;">';
                        $htmls .= '&rang; '.Helper::date($parameters['date_checkin']).' '.$today->format("H:i:s");
                    $htmls .= '</p>';
                $htmls .= '</div>';
            $htmls .= '</div>';
            Status::success( "ลงทะเบียนเข้าร่วมเรียบร้อยแล้ว", array('title'=>"เข้าร่วมแล้ว", 'htmls'=>$htmls) );
        }
    }else{
        $parameters = array();
        $fields = "`events_id`";
        $values = ":events_id";
        $parameters['events_id'] = $_POST['events_id'];
        $fields .= ',`id`';
        $values .= ",:id";
        $parameters['id'] = $today->format("YmdHis").'000';
        $fields .= ',`type`';
        $values .= ",:type";
        $parameters['type'] = ( (isset($_POST['type'])&&$_POST['type']) ? $_POST['type'] : 'OTHER' );
        $fields .= ',`email`';
        $values .= ",:email";
        $parameters['email'] = ( (isset($_POST['email'])&&$_POST['email']) ? $_POST['email'] : null );
        $fields .= ',`prefix`';
        $values .= ",:prefix";
        $parameters['prefix'] = ( (isset($_POST['prefix'])&&$_POST['prefix']) ? $_POST['prefix'] : null );
        $fields .= ',`firstname`';
        $values .= ",:firstname";
        $parameters['firstname'] = ( (isset($_POST['firstname'])&&$_POST['firstname']) ? $_POST['firstname'] : null );
        $fields .= ',`lastname`';
        $values .= ",:lastname";
        $parameters['lastname'] = ( (isset($_POST['lastname'])&&$_POST['lastname']) ? $_POST['lastname'] : null );
        $fields .= ',`organization`';
        $values .= ",:organization";
        $parameters['organization'] = ( (isset($_POST['organization'])&&$_POST['organization']) ? $_POST['organization'] : null );
        $fields .= ',`department`';
        $values .= ",:department";
        $parameters['department'] = ( (isset($_POST['department'])&&$_POST['department']) ? $_POST['department'] : null );
        $fields .= ',`status`';
        $values .= ",:status";
        $parameters['status'] = 1;
        $fields .= ',`channel`';
        $values .= ",:channel";
        $parameters['channel'] = "ONSITE";
        $fields .= ',`date_checkin`';
        $values .= ",:date_checkin";
        $parameters['date_checkin'] = $today->format("Y-m-d H:i:s");
        $fields .= ',`user_checkin`';
        $values .= ",:user_checkin";
        $parameters['user_checkin'] = $parameters['email'];
        $fields .= ',`date_create`';
        $values .= ",:date_checkin";
        $fields .= ',`user_create`';
        $values .= ",:user_checkin";
        if( DB::create("INSERT INTO `events_lists` ($fields) VALUES ($values)", $parameters) ){
            // Summary
            DB::update("UPDATE `events` SET `participants`=(SELECT COUNT(events_lists.id) FROM events_lists WHERE events_lists.events_id=:events_id) WHERE events_id=:events_id;", array('events_id'=>$parameters['events_id']));
            $htmls = '<div class="d-flex flex-row on-success">';
                $htmls .= '<div style="display:none;"><div class="icon text-success me-2 mt-n3" style="font-size:52px;line-height:75px;"><i class="uil uil-calendar-alt"></i></div></div>';
                $htmls .= '<div style="display:none;">';
                    $htmls .= '<h5 class="mb-0 text-success on-font-primary">ลงทะเบียนเข้าร่วมแล้ว</h5>';
                    $htmls .= '<p class="on-text-normal text-success m-0" style="margin-top:-2px;line-height:18px;">';
                        $htmls .= '&rang; '.Helper::date($parameters['date_checkin']).' '.$today->format("H:i:s");
                    $htmls .= '</p>';
                $htmls .= '</div>';
            $htmls .= '</div>';
            Status::success( "ลงทะเบียนเข้าร่วมเรียบร้อยแล้ว", array('title'=>"เข้าร่วมแล้ว", 'htmls'=>$htmls) );
        }
    }
    Status::error( "ตรวจสอบข้อมูลของท่าน จากนั้นลองใหม่อีกครั้ง <i>!!!</i>", array('title'=>"ลงทะเบียนไม่ได้") );
?>