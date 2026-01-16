<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    $form = ( (isset($_POST['form_as'])&&$_POST['form_as']) ? $_POST['form_as'] : null );
    $url = 'https://api.edu.cmu.ac.th/v1/student/filter';
    if( (isset($_POST['education_id'])&&$_POST['education_id'])&&(isset($_POST['year'])&&$_POST['year']) ){
        $posts  = "year=".$_POST['year'];
        $posts .= "&education_id=".$_POST['education_id'];
        if(isset($_POST['status'])&&$_POST['status']){
            if($_POST['status']=='normstatus'){
                $posts .= "&status_id=01,02";
            }else if($_POST['status']=='gradstatus'){
                $posts .= "&status_id=03";
            }else if($_POST['status']=='outofstatus'){
                $posts .= "&status_id=04,05";
            }
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.edu.cmu.ac.th/v1/student/majors/'.$_POST['education_id']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, EDU_API_USER.":".EDU_API_PASS);
        $result = curl_exec($ch);
        curl_close($ch);
        $checkmajor = json_decode($result, true);
        $majors = '<option value="">ทั้งหมด</option>';
        if( isset($checkmajor)&&count($checkmajor)>0 ){
            foreach($checkmajor as $item){
                $majors .= '<option value="'.$item['major'].'">'.$item['major'].'</option>';
            }
        }
        if(isset($_POST['major'])&&$_POST['major']){
            $posts .= "&major=".$_POST['major'];
            $majors = str_replace('value="'.$_POST['major'].'"', 'value="'.$_POST['major'].'" selected', $majors);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $posts);
        curl_setopt($ch, CURLOPT_USERPWD, EDU_API_USER.":".EDU_API_PASS);
        $checklist = curl_exec($ch);
        curl_close($ch);
        $htmls = '';
        if( !empty($checklist) ){
            $lists = json_decode($checklist, true);
            if( isset($lists)&&count($lists)>0 ){
                foreach($lists as $seq => $item){
                    $fullname = ( isset($item['title']) ? $item['title'] : null ).( isset($item['firstname']) ? $item['firstname'] : null ).( isset($item['lastname']) ? ' '.$item['lastname'] : null );
                    $htmls .= '<form id="'.$item['student_id'].'" class="form-manage AT-'.$item['student_id'].'" name="saving" action="'.$form.'/scripts/lists/student/create.php" method="POST" enctype="multipart/form-data" target="_blank">';
                        $htmls .= '<input type="hidden" name="student_id" value="'.( isset($item['student_id']) ? $item['student_id'] : null ).'"/>';
                        $htmls .= '<input type="hidden" name="events_id" value="'.( isset($_POST['events_id']) ? $_POST['events_id'] : null ).'"/>';
                        $htmls .= '<input type="hidden" name="type" value="STUDENT"/>';
                        $htmls .= '<input type="hidden" name="email" value="'.( isset($item['email']) ? $item['email'] : null ).'"/>';
                        $htmls .= '<input type="hidden" name="prefix" value="'.( isset($item['title']) ? $item['title'] : null ).'"/>';
                        $htmls .= '<input type="hidden" name="firstname" value="'.( isset($item['firstname']) ? $item['firstname'] : null ).'"/>';
                        $htmls .= '<input type="hidden" name="lastname" value="'.( isset($item['lastname']) ? $item['lastname'] : null ).'"/>';
                        $htmls .= '<input type="hidden" name="organization" value="คณะศึกษาศาสตร์"/>';
                        $htmls .= '<input type="hidden" name="department" value="'.( isset($item['major']) ? $item['major'] : null ).'"/>';
                        $htmls .= '<div class="card mb-1 lift">';
                            $htmls .= '<div class="card-body" style="padding:5px 0 5px 10px;">';
                                $htmls .= '<div style="right:0;float:right;position:absolute;margin:-8px 8px 0 0;"><sup class="on-status"></sup></div>';
                                $htmls .= '<span class="row gx-1 justify-content-between align-items-center">';
                                    $htmls .= '<span class="col-6 text-body d-flex align-items-center">';
                                        $htmls .= '<div style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">';
                                            $htmls .= '<input id="AT-'.$item['student_id'].'" type="checkbox" name="lists[]" value="'.$seq.'" class="form-check-input" onchange="record_events(\'check\', { \'self\':this, \'at\':\''.$item['student_id'].'\' });"/>';
                                            $htmls .= '&nbsp;'.$fullname;
                                            $htmls .= '&nbsp;<sup>('.( isset($item['email']) ? '<em class="fs-10">'.$item['email'].'</em>' : '<em class="fs-10 text-red">สแกนเข้าร่วมด้วย QR Code ไม่ได้ !!!</em>' ).')</sup>';
                                        $htmls .= '</div>';
                                    $htmls .= '</span>';
                                    $htmls .= '<span class="col-6 text-body d-flex align-items-center">';
                                        $htmls .= '<div style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">';
                                            $htmls .= ( isset($item['major']) ? $item['major'] : '<em class="fs-10 text-red">ไม่ทราบสาขาวิชา !!!</em>' );
                                            if( isset($item['status_name']) ){
                                                if(intval($item['status_id'])>=4){
                                                    $htmls .= '&nbsp;<sup>(<em class="fs-10 text-red">'.$item['status_name'].'</em>)</sup>';
                                                }else if(intval($item['status_id'])==3){
                                                    $htmls .= '&nbsp;<sup>(<em class="fs-10 text-green">'.$item['status_name'].'</em>)</sup>';
                                                }
                                            }
                                        $htmls .= '</div>';
                                    $htmls .= '</span>';
                                $htmls .= '</span>';
                            $htmls .= '</div>';
                        $htmls .= '</div>';
                        $htmls .= '<input type="submit" style="display:none;">';
                    $htmls .= '</form>';
                }
            }
        }
        Status::success( "สำเร็จ", array('majors'=>$majors, 'htmls'=>$htmls) );
    }else{
        Status::error( '<div class="alert alert-light alert-icon text-center text-red mb-0" style="min-height:50px;padding:8px 8px 1px 8px;">โปรดเลือกระดับก่อน !!!</div>' );
    }
?>