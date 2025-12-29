<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    if( !isset($_POST['events_id'])||!$_POST['events_id'] ){
        Status::error( 'ไม่พบรหัสกิจกรรม !!!' );
    }else if(!isset($_POST['email'])||!$_POST['email']){
        Status::error( 'กรุณากรอกอีเมล !!!', array('onfocus'=>"email") );
    }
    // Begin
    $list = array();
    $list['events_id'] = $_POST['events_id'];
    $list['email'] = Helper::stringSave($_POST['email']);
    $list['user_create'] = User::get('email');
    $checkexist = DB::one("SELECT events_id FROM events_shared WHERE email=:email;", array('email'=>$list['email']));
    if( isset($checkexist['events_id'])&&$checkexist['events_id'] ){
        Status::error('อีเมลนี้ถูกแชร์แล้ว !!!', array('onfocus'=>"email") );
    }else{
        $check = DB::one("SELECT id, TRIM(CONCAT(COALESCE(title,''),name,' ',COALESCE(surname,''))) AS fullname, COALESCE(picture, picture_default) AS picture FROM member WHERE email=:email OR email_cmu=:email;", array('email'=>$list['email']));
        if( !isset($check['id'])||!$check['id'] ){
            Status::error('ไม่พบอีเมลนี้, ต้องเข้าสู่ระบบอย่างน้อย 1 ครั้ง !!!', array('onfocus'=>"email") );
        }
        if( DB::create("INSERT INTO `events_shared` (`events_id`,`email`,`date_create`,`user_create`) VALUES (:events_id,:email,NOW(),:user_create);", $list) ){
            // Summary
            DB::update("UPDATE `events` SET `shares`=(SELECT COUNT(events_shared.email) FROM events_shared WHERE events_shared.events_id=:events_id) WHERE events_id=:events_id;", array('events_id'=>$list['events_id']));
            // Htmls
            $htmls = '<div class="card card-'.md5($list['email']).' mb-1">';
                $htmls .= '<div class="card-body text-dark">';
                    $htmls .= '<div class="delete">';
                        $htmls .= '<div class="delete-box">';
                            $htmls .= 'ยืนยันยกเลิกแชร์<br>';
                            $htmls .= '<span class="btn btn-success btn-sm" onclick="record_events(\'unshare\', { \'self\':this, \'on\':\'Y\', \'events_id\':\''.$list['events_id'].'\', \'email\':\''.$list['email'].'\' });">ใช่</span>';
                            $htmls .= '<span class="btn btn-outline-danger btn-sm" onclick="record_events(\'unshare\', { \'self\':this, \'on\':\'N\' });">ไม่</span>';
                        $htmls .= '</div>';
                        $htmls .= '<button type="button" class="btn btn-outline-danger" onclick="record_events(\'unshare\', { \'self\':this });"><spam class="uil uil-user-minus"></spam></button>';
                    $htmls .= '</div>';
                    $htmls .= '<div class="picture">';
                        $htmls .= '<img src="'.( (isset($check['picture'])&&$check['picture']) ? $check['picture'] : THEME_IMG.'/avatar.png' ).'" onerror="this.onerror=null;this.src=\''.THEME_IMG.'/avatar.png\';"/>';
                    $htmls .= '</div>';
                    $htmls .= '<div class="info">';
                        $htmls .= '<font class="name">'.( (isset($check['fullname'])&&$check['fullname']) ? $check['fullname'] : '<span class="text-ash on-text-i">Unknown... .. .</span>' ).'</font>';
                        $htmls .= '<br><span class="uil uil-envelopes"></span> '.$list['email'];
                    $htmls .= '</div>';
                $htmls .= '</div>';
            $htmls .= '</div>';
            Status::success( "แชร์กิจกรรมให้ ".$list['email']." แล้ว", array('title'=>"แชร์เรียบร้อยแล้ว", 'events_id'=>$list['events_id'], 'htmls'=>$htmls) );
        }
    }
    Status::error( "กรุณาลองใหม่อีกครั้ง !!!", array('title'=>"ไม่สามารถแชร์ได้", 'onfocus'=>"email") );
?>