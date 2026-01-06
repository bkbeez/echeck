<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/registration'); ?>
<?php
    if( !isset($_POST['events_id']) || !$_POST['events_id'] ){
        Status::error( 'ไม่พบรหัสกิจกรรม !!!' );
    }
    if( !isset($_POST['type']) || !$_POST['type'] ){
        Status::error( 'ไม่พบประเภท !!!' );
    }
    // Create list
    $data = array(
        'events_id' => $_POST['events_id'],
        'type' => $_POST['type'],
        'prefix' => $_POST['prefix'],
        'firstname' => $_POST['first_name'],
        'lastname' => $_POST['last_name'],
        'email' => $_POST['email'],
        'organization' => isset($_POST['organization']) ? $_POST['organization'] : '',
        'status' => 1, // active
        'note' => ''
    );
    if( Participant::createParticipant($data) ){
        Status::success( "ลงทะเบียนเรียบร้อยแล้ว", array('title'=>"บันทึกเรียบร้อยแล้ว") );
    }else{
        Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถบันทึกได้") );
    }
?>