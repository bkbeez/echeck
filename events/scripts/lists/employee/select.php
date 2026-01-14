<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    // Init
    $result = array('status'=>'success', 'title'=>'สำเร็จ', 'htmls'=>'&nbsp;' );
    $form = ( (isset($_POST['form_as'])&&$_POST['form_as']) ? $_POST['form_as'] : null );
    $url = 'http://api.edu.cmu/v1/employee/'.( (isset($_POST['status'])&&$_POST['status']) ? $_POST['status'] : 'workstatus' );
    if( isset($_POST['organize_id'])&&$_POST['organize_id'] ){
        $url .= '/'.$_POST['organize_id'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, EDU_API_USER.":".EDU_API_PASS);
        $checklist = curl_exec($ch);
        curl_close($ch);
        $lists = json_decode($checklist, true);
        if( isset($lists)&&count($lists)>0 ){
            $htmls = '';
            $department = $_POST['organize'][$_POST['organize_id']];
            foreach($lists as $seq => $item){
                $fullname = ( isset($item['title']) ? $item['title'] : null ).( isset($item['firstname']) ? $item['firstname'] : null ).( isset($item['lastname']) ? ' '.$item['lastname'] : null );
                $htmls .= '<form class="form-manage AT-'.$item['employee_id'].'" name="saving" action="'.$form.'/scripts/lists/employee/create.php" method="POST" enctype="multipart/form-data" target="_blank">';
                    $htmls .= '<input type="hidden" name="employee_id" value="'.( isset($item['employee_id']) ? $item['employee_id'] : null ).'"/>';
                    $htmls .= '<input type="hidden" name="datas['.$seq.'][email]" value="'.( isset($item['email']) ? $item['email'] : null ).'"/>';
                    $htmls .= '<input type="hidden" name="datas['.$seq.'][prefix]" value="'.( isset($item['title']) ? $item['title'] : null ).'"/>';
                    $htmls .= '<input type="hidden" name="datas['.$seq.'][firstname]" value="'.( isset($item['firstname']) ? $item['firstname'] : null ).'"/>';
                    $htmls .= '<input type="hidden" name="datas['.$seq.'][lastname]" value="'.( isset($item['lastname']) ? $item['lastname'] : null ).'"/>';
                    $htmls .= '<input type="hidden" name="datas['.$seq.'][organization]" value="คณะศึกษาศาสตร์"/>';
                    $htmls .= '<input type="hidden" name="datas['.$seq.'][department]" value="'.$department.'"/>';
                    $htmls .= '<div class="card mb-1 lift">';
                        $htmls .= '<div class="card-body" style="padding:5px 0 5px 10px;">';
                            $htmls .= '<span class="row gx-1 justify-content-between align-items-center">';
                                $htmls .= '<span class="col-md-6 col-lg-6 mb-2 mb-md-0 d-flex align-items-center text-body">';
                                    $htmls .= '<div style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">';
                                        $htmls .= '<input type="checkbox" name="lists[]" value="'.$seq.'" class="form-check-input" onchange="record_events(\'check\', { \'self\':this });"/>';
                                        $htmls .= '&nbsp;'.( isset($item['email']) ? $item['email'] : '<em class="fs-12 text-red">ไม่รองรับลงทะเบียนด้วย QR Code !!!</em>' );
                                    $htmls .= '</div>';
                                $htmls .= '</span>';
                                $htmls .= '<span class="col-md-6 col-lg-6 text-body d-flex align-items-center">';
                                    $htmls .= '<div style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">'.$fullname.'</div>';
                                $htmls .= '</span>';
                            $htmls .= '</span>';
                        $htmls .= '</div>';
                    $htmls .= '</div>';
                    $htmls .= '<input type="submit" style="display:none;">';
                $htmls .= '</form>';
            }
            $result['htmls'] = $htmls;
        }
    }
    // Returns
    echo json_encode($result);
    exit();
?>