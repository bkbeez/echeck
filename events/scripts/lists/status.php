<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    if( !isset($_POST['id'])||!$_POST['id'] ){
        Status::error( 'ไม่พบรหัส !!!' );
    }else if( !isset($_POST['events_id'])||!$_POST['events_id'] ){
        Status::error( 'ไม่พบรหัสกิจกรรม !!!' );
    }
    // Begin
    $today = new datetime();
    $parameters = array();
    $parameters['id'] = $_POST['id'];
    $parameters['events_id'] = $_POST['events_id'];
    if( isset($_POST['reset'])&&$_POST['reset']=='Y' ){
        if( DB::update("UPDATE `events_lists` SET `status`=0, `date_checkin`=NULL, `user_checkin`=NULL, `date_cancel`=NULL, `user_cancel`=NULL WHERE id=:id AND events_id=:events_id;", $parameters) ){
            $htmls = '<span class="badge badge-status badge-sm bg-pale-dark text-dark rounded me-1 align-self-start"><i class="uil uil-circle"></i>ไม่ได้ลงทะเบียน</span>';
            Status::success( "รีเซตการลงทะเบียนเรียบร้อยแล้ว", array('title'=>"รีเซตเรียบร้อยแล้ว", 'id'=>$parameters['id'], 'events_id'=>$parameters['events_id'], 'htmls'=>$htmls) );
        }
        Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถเปลี่ยนได้") );
    }else if( isset($_POST['cancel'])&&$_POST['cancel']=='Y' ){
        $parameters['user_cancel'] = User::get('email');
        if( DB::update("UPDATE `events_lists` SET `status`=2, `date_checkin`=NULL, `user_checkin`=NULL, `date_cancel`=NOW(), `user_cancel`=:user_cancel WHERE id=:id AND events_id=:events_id;", $parameters) ){
            $htmls = '<span class="badge badge-status badge-sm bg-red text-white rounded me-1 align-self-start lift" style="cursor:pointer;" onclick="manage_events(\'status\', { \'id\':\''.$parameters['id'].'\', \'events_id\':\''.$parameters['events_id'].'\' });"><i class="uil uil-times-circle"></i>ยกเลิกแล้ว</span>';
            Status::success( "ยกเลิกการลงทะเบียนเรียบร้อยแล้ว", array('title'=>"ยกเลิกเรียบร้อยแล้ว", 'id'=>$parameters['id'], 'events_id'=>$parameters['events_id'], 'htmls'=>$htmls) );
        }
        Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถยกเลิกได้") );
    }
    Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถเปลี่ยนสถานะได้") );
?>