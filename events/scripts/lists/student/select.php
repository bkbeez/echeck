<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    $form = ( (isset($_POST['form_as'])&&$_POST['form_as']) ? $_POST['form_as'] : null );
    $url = 'https://api.edu.cmu.ac.th/v1/student/filter';
    if( isset($_POST['year'])&&$_POST['year'] ){
        $posts = array();
        $posts['year'] = $_POST['year'];
        if(isset($_POST['major'])&&$_POST['major']){
            $posts['major'] = $_POST['major'];
        }
        if(isset($_POST['status'])&&$_POST['status']){
            //$posts['year'] = $_POST['year'];
        }
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'http://api.edu.cmu/v1/student/filter',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $posts,
            CURLOPT_USERPWD => EDU_API_USER.":".EDU_API_PASS,
        ));
        $checklist = curl_exec($ch);
        curl_close($ch);
        $lists = json_decode($checklist, true);
        if( isset($lists)&&count($lists)>0 ){
            $seq = 0;
            $htmls = '';
            foreach($lists as $item){
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
                                    $htmls .= '</div>';
                                $htmls .= '</span>';
                                $htmls .= '<span class="col-6 text-body d-flex align-items-center">';
                                    $htmls .= '<div style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">';
                                        $htmls .= ( isset($item['email']) ? $item['email'] : '<em class="fs-12 text-red">ไม่รองรับลงทะเบียนด้วย QR Code !!!</em>' );
                                    $htmls .= '</div>';
                                $htmls .= '</span>';
                            $htmls .= '</span>';
                        $htmls .= '</div>';
                    $htmls .= '</div>';
                    $htmls .= '<input type="submit" style="display:none;">';
                $htmls .= '</form>';
                $seq++;
            }
            Status::success( "พบข้อมูลจำนวน ".$seq." คน", array('records'=>$seq, 'htmls'=>$htmls) );
        }
    }else{
        Status::error( '<div class="alert alert-light alert-icon text-center text-red mb-0" style="min-height:50px;padding:8px 8px 1px 8px;">โปรดเลือกรหัสปีก่อน !!!</div>' );
    }
?>