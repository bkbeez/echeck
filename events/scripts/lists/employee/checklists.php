<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    // Init
    $result = array('status'=>'success', 'title'=>'สำเร็จ' );
    $form = ( (isset($_POST['form_as'])&&$_POST['form_as']) ? $_POST['form_as'] : null );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.edu.cmu.ac.th/v1/employee/workstatus');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, EDU_API_USER.":".EDU_API_PASS);
    $checklist = curl_exec($ch);
    curl_close($ch);
    $lists = json_decode($checklist, true);
    if( isset($lists)&&count($lists)>0 ){
        $htmls  = '<div class="alert alert-info alert-icon mb-1">';
            $htmls .= '<div class="form-check">';
                $htmls .= '<input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" onchange="record_events(\'checklist\', { \'self\':this });">';
                $htmls .= '<label class="form-check-label" for="flexCheckDefault"> เลือกทั้งหมด</label>';
            $htmls .= '</div>';
        $htmls .= '</div>';
        $htmls .= '<div class="alert alert-info alert-icon mb-0" style="padding: 1px 10px;">';
            $htmls .= '<table border="0" class="table table-hover">';
            foreach($lists as $seq => $item){
                $htmls .= '<tr>';
                    $htmls .= '<td class="choose"><input class="form-check-input" type="checkbox" value="'.$item['email'].'"></td>';
                    $htmls .= '<td class="no">'.($seq+1).'</td>';
                    $htmls .= '<td class="mail">'.$item['email'].'</td>';
                    $htmls .= '<td class="name">'.$item['title'].$item['firstname'].' '.$item['lastname'].'</td>';
                    $htmls .= '<td class="organize">'.$item['organize_name'].'</td>';
                $htmls .= '</tr>';
            }
            $htmls .= '</table>';
        $htmls .= '</div>';
        $result['htmls'] = $htmls;
    }
    // Returns
    echo json_encode($result);
    exit();
?>