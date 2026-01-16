<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    $form = ( (isset($_POST['form_as'])&&$_POST['form_as']) ? $_POST['form_as'] : null );
    $url = 'https://api.edu.cmu.ac.th/v1/employee/'.( (isset($_POST['status'])&&$_POST['status']) ? $_POST['status'] : 'workstatus' );
    if( isset($_POST['organize_id'])&&$_POST['organize_id'] ){
        $url .= '/'.$_POST['organize_id'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, EDU_API_USER.":".EDU_API_PASS);
        $checklist = curl_exec($ch);
        curl_close($ch);
        $htmls = '';
        if( !empty($checklist) ){
            $lists = json_decode($checklist, true);
            if( isset($lists)&&count($lists)>0 ){
                $htmls = '';
                $department = $_POST['organize'][$_POST['organize_id']];
                foreach($lists as $seq => $item){
                    $fullname = ( isset($item['title']) ? $item['title'] : null ).( isset($item['firstname']) ? $item['firstname'] : null ).( isset($item['lastname']) ? ' '.$item['lastname'] : null );
                    $htmls .= '<form id="'.$item['employee_id'].'" class="form-manage AT-'.$item['employee_id'].'" name="saving" action="'.$form.'/scripts/lists/employee/create.php" method="POST" enctype="multipart/form-data" target="_blank">';
                        $htmls .= '<input type="hidden" name="employee_id" value="'.( isset($item['employee_id']) ? $item['employee_id'] : null ).'"/>';
                        $htmls .= '<input type="hidden" name="events_id" value="'.( isset($_POST['events_id']) ? $_POST['events_id'] : null ).'"/>';
                        $htmls .= '<input type="hidden" name="type" value="EMPLOYEE"/>';
                        $htmls .= '<input type="hidden" name="email" value="'.( isset($item['email']) ? $item['email'] : null ).'"/>';
                        $htmls .= '<input type="hidden" name="prefix" value="'.( isset($item['title']) ? $item['title'] : null ).'"/>';
                        $htmls .= '<input type="hidden" name="firstname" value="'.( isset($item['firstname']) ? $item['firstname'] : null ).'"/>';
                        $htmls .= '<input type="hidden" name="lastname" value="'.( isset($item['lastname']) ? $item['lastname'] : null ).'"/>';
                        $htmls .= '<input type="hidden" name="organization" value="คณะศึกษาศาสตร์"/>';
                        $htmls .= '<input type="hidden" name="department" value="'.$department.'"/>';
                        $htmls .= '<div class="card mb-1 lift">';
                            $htmls .= '<div class="card-body" style="padding:5px 0 5px 10px;">';
                                $htmls .= '<div style="right:0;float:right;position:absolute;margin:-8px 8px 0 0;"><sup class="on-status"></sup></div>';
                                $htmls .= '<span class="row gx-1 justify-content-between align-items-center">';
                                    $htmls .= '<span class="col-6 text-body d-flex align-items-center">';
                                        $htmls .= '<div style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">';
                                            $htmls .= '<input id="AT-'.$item['employee_id'].'" type="checkbox" name="lists[]" value="'.$seq.'" class="form-check-input" onchange="record_events(\'check\', { \'self\':this, \'at\':\''.$item['employee_id'].'\' });"/>';
                                            $htmls .= '&nbsp;'.$fullname;
                                            $htmls .= '&nbsp;<sup>('.( isset($item['email']) ? '<em class="fs-10">'.$item['email'].'</em>' : '<em class="fs-10 text-red">สแกนเข้าร่วมด้วย QR Code ไม่ได้ !!!</em>' ).')</sup>';
                                        $htmls .= '</div>';
                                    $htmls .= '</span>';
                                    $htmls .= '<span class="col-6 text-body d-flex align-items-center">';
                                        $htmls .= '<div style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">';
                                            $htmls .= ( isset($item['organize_name']) ? $item['organize_name'] : '<em class="fs-10 text-red">ไม่ทราบหน่วย/สาขา !!!</em>' );
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
        Status::success( "สำเร็จ", array('htmls'=>$htmls) );
    }else{
        Status::error( '<div class="alert alert-light alert-icon text-center text-red mb-0" style="min-height:50px;padding:8px 8px 1px 8px;">โปรดเลือกหน่วยงานก่อน !!!</div>' );
    }
?>