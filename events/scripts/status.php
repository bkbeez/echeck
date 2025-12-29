<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    if( !isset($_POST['events_id'])||!$_POST['events_id'] ){
        Status::error( 'ไม่พบรหัสกิจกรรม !!!' );
    }
    // Begin
    $today = new datetime();
    $parameters = array();
    $parameters['events_id'] = $_POST['events_id'];
    $datas  = '`status`';
    $datas .= "=:status";
    $parameters['status'] = ( (isset($_POST['status'])&&$_POST['status']) ? intval($_POST['status']) : 0 );
    $datas .= ',`date_update`';
    $datas .= "=NOW()";
    $datas .= ',`user_update`';
    $datas .= "=:user_update";
    $parameters['user_update'] = User::get('email');
    if( DB::update("UPDATE `events` SET $datas WHERE events_id=:events_id;", $parameters) ){
        $badge = 'badge badge-status badge-sm bg-pale-dark text-dark rounded me-1 align-self-start';
        $htmls = '<i class="uil uil-circle"></i>DRAFT';
        if( $parameters['status']==1 ){
            $badge = 'badge badge-status badge-sm bg-pale-green text-green rounded me-1 align-self-start';
            $htmls = '<i class="uil uil-play-circle"></i>OPEN';
        }else if( $parameters['status']==2 ){
            $badge = 'badge badge-status badge-sm bg-pale-red text-red rounded me-1 align-self-start';
            $htmls = '<i class="uil uil-times-circle"></i>CLOSE';
        }
        Status::success( "เปลี่ยนสถานะกิจกรรมเรียบร้อยแล้ว", array('title'=>"บันทึกเรียบร้อยแล้ว", 'events_id'=>$parameters['events_id'], 'badge'=>$badge, 'htmls'=>$htmls) );
    }
    Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถเปลี่ยนได้") );
?>