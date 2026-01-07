<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/registration'); ?>
<?php
    if( !isset($_POST['id']) || !$_POST['id'] ){
        Status::error( 'ไม่พบข้อมูลผู้เข้าร่วมกิจกรรม !!!' );
    }
    if( !isset($_POST['events_id']) || !$_POST['events_id'] ){
        Status::error( 'ไม่พบรหัสกิจกรรม !!!' );
    }

    $parameters = array();
    $parameters['id'] = $_POST['id'];
    $parameters['events_id'] = $_POST['events_id'];
    $parameters['user_checkin'] = User::get('email');
    $parameters['date_checkin'] = date('Y-m-d H:i:s');
    $parameters['status'] = 1;

    $check = DB::one("SELECT id FROM events_lists WHERE id=:id AND events_id=:events_id", [
        'id' => $parameters['id'],
        'events_id' => $parameters['events_id']
    ]);

    if(!$check){
        Status::error( "ไม่พบรายชื่อนี้ในกิจกรรมดังกล่าว !!!" );
    }

    $sql = "UPDATE `events_lists` SET
                status = :status,
                date_checkin = :date_checkin,
                user_checkin = :user_checkin,
                date_update = :date_checkin,
                user_update = :user_checkin
            WHERE id = :id AND events_id = :events_id";

    if( DB::update($sql, $parameters) ){
        Status::success( "เช็คอินเรียบร้อยแล้ว", array('title'=>"สำเร็จ") );
    } else {
        Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถบันทึกได้") );
    }
?>