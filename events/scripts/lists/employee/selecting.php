<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    if( !isset($_POST['events_id'])||!$_POST['events_id'] ){
        Status::error( 'ไม่พบรหัสกิจกรรม !!!' );
    }else if( !isset($_POST['datas'])||count($_POST['datas'])<=0 ){
        Status::error( 'ไม่พบข้อมูลรายชื่อ !!!' );
    }else if( !isset($_POST['lists'])||count($_POST['lists'])<=0 ){
        Status::error( 'กรุณาเลือกอย่างน้อย 1 รายชื่อ !!!' );
    }
    // Begin
    $today = new datetime();
    $parameters = array();
    $parameters['events_id'] = $_POST['events_id'];
    $parameters['user_create'] = User::get('email');
    $datas = $_POST['datas'];
    $sql = "INSERT INTO `events_lists` (`id`,`events_id`,`type`,`student_id`,`email`,`prefix`,`firstname`,`lastname`,`organization`,`department`,`date_create`,`user_create`) VALUES ";
    foreach($_POST['lists'] as $seq => $list){
        if( isset($datas[$list]) ){
            if( $seq>0 ){ $sql .= ","; }
            $sql .= " (:id_".$seq.",:events_id,'EMPLOYEE',NULL,:email_".$seq.",:prefix_".$seq.",:firstname_".$seq.",:lastname_".$seq.",:organization_".$seq.",:department_".$seq.",NOW(),:user_create)";
            $parameters['id_'.$seq] = $today->format("YmdHis").sprintf("%1$03d", $seq);
            $parameters['email_'.$seq] = $datas[$list]['email'];
            $parameters['prefix_'.$seq] = $datas[$list]['prefix'];
            $parameters['firstname_'.$seq] = $datas[$list]['firstname'];
            $parameters['lastname_'.$seq] = $datas[$list]['lastname'];
            $parameters['organization_'.$seq] = $datas[$list]['organization'];
            $parameters['department_'.$seq] = $datas[$list]['department'];
        }else{
            Status::error( 'ไม่พบข้อมูลรายชื่อ['.$list.'] !!!' );
        }
    }
    if( User::create($sql, $parameters) ){
        // Summary
        DB::update("UPDATE `events` SET `participants`=(SELECT COUNT(events_lists.id) FROM events_lists WHERE events_lists.events_id=:events_id) WHERE events_id=:events_id;", array('events_id'=>$parameters['events_id']));
        Status::success( "บันทึกรายชื่อเข้าสู่กิจกรรมแล้ว", array('title'=>"เพิ่มรายชื่อแล้ว") );
    }
    Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถเพิ่มได้") );
?>