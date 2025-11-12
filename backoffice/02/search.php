<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/backoffice/?participants'); ?>
<?php
    // Init
    $result = array('status'=>'success', 'title'=>Lang::get('Success') );
    $page = ((isset($_POST['page'])&&$_POST['page'])?intval($_POST['page']):1);
    $limit = ((isset($_POST['limit'])&&$_POST['limit'])?intval($_POST['limit']):50);
    $keyword = ((isset($_POST['keyword'])&&$_POST['keyword'])?$_POST['keyword']:null);
    $filter_as = ((isset($_POST['filter_as'])&&$_POST['filter_as'])?$_POST['filter_as']:'search_as');
    if( !isset($_SESSION['login']['filter'][$filter_as]['limit'])||$_SESSION['login']['filter'][$filter_as]['limit']!=$limit ){
        $_SESSION['login']['filter'][$filter_as]['limit'] = $limit;
        $page = 1;
    }else if( isset($_SESSION['login']['filter'][$filter_as]['keyword'])&&$_SESSION['login']['filter'][$filter_as]['keyword']!=$keyword ){
        $page = 1;
    }
    $admin_as = Auth::admin();
    // Condition
    $parameters = array();
    $condition = "";
    $_SESSION['login']['filter'][$filter_as]['keyword'] = null;
    if( $keyword ){
        $_SESSION['login']['filter'][$filter_as]['keyword'] = $keyword;
        $parameters['keyword'] = "%".$keyword."%";
        $condition .= " AND ( member.email LIKE :keyword";
            $condition .= " OR TRIM(CONCAT(member.name,' ',COALESCE(member.surname,''))) LIKE :keyword";
        $condition .= " )";
    }
    $_SESSION['login']['filter'][$filter_as]['condition'] = array();
    /*if( isset($_POST['condition']) ){
        foreach($_POST['condition'] as $key => $value ){
            if($value){
                if($key=="institute"){
                    $_SESSION['login']['filter'][$filter_as]['condition'][$key] = $value;
                    if($value=='OTHER'){
                        $condition .= " AND meeting_participant.institute_id IS NULL";
                    }else if($value!='ALL'){
                        $parameters['institute_id'] = $value;
                        $condition .= " AND meeting_participant.institute_id=:institute_id";
                    }
                }else if($key=="participant"){
                    $_SESSION['login']['filter'][$filter_as]['condition'][$key] = $value;
                    if($value=='AAPN'){
                        $condition .= " AND meeting_participant.participant_id='AAPN'";
                    }else if($value=='AAPI'){
                        $condition .= " AND meeting_participant.participant_id='AAPI'";
                    }
                }else if($key=="status"){
                    $_SESSION['login']['filter'][$filter_as]['condition'][$key] = $value;
                    if($value=='NOTP'){
                        $condition .= " AND meeting_participant.regist_amount>0 AND meeting_participant.payslip_status IS NULL";
                    }else if($value=='WAIT'){
                        $condition .= " AND meeting_participant.regist_amount>0 AND meeting_participant.payslip_status='W'";
                    }else if($value=='BACK'){
                        $condition .= " AND meeting_participant.regist_amount>0 AND meeting_participant.payslip_status='R'";
                    }
                }
            }
        }
    }*/
    // Total and Pages
    $sql = "SELECT COUNT(member.id) AS total
            FROM member
            WHERE member.id IS NOT NULL";
    $count = DB::one($sql.$condition, $parameters);
    $result['total'] = ( (isset($count['total'])&&$count['total']) ? intval($count['total']) : 0 );
    $result['pages'] = 1;
    if($result['total']>0){
        if( ($result['total']%$limit)==0 ){
            $result['pages'] = intval($result['total']/$limit);
        }else{
            $result['pages'] = intval($result['total']/$limit)+1;
        }
    }
    $_SESSION['login']['filter'][$filter_as]['pages'] = $result['pages'];
    $result['display'] = number_format($result['pages'],0);
    // Page
    if($page>1&&$page>$result['pages']){
        $page = ($page-1);
    }
    $result['page'] = $page;
    $_SESSION['login']['filter'][$filter_as]['page'] = $result['page'];
    if( !isset($_POST['pages'])||(intval($_POST['pages'])!=$result['pages']) ){
        $result['pagination'] = '';
        for($sel=1;$sel<=intval($result['pages']);$sel++){
            $result['pagination'] .= '<option value="'.$sel.'" '.( ($page==$sel) ? 'selected':null ).'>'.number_format($sel,0).'</option>';
        }
    }
    // Run
    $start = (($page-1)*$limit);
    $sql = "SELECT member.*
            , TRIM(CONCAT(COALESCE(member.title,''),member.name,' ',COALESCE(member.surname,''))) AS fullname
            , 'NORM' AS status
            FROM member
            WHERE member.id IS NOT NULL";
    $sql .= $condition;
    $sql .= " ORDER BY member.date_create";
    $sql .= " LIMIT $start, $limit;";
    $htmls = '';
    $lists = DB::sql($sql, $parameters);
    if( isset($lists)&&count($lists)>0 ){
        $lang = App::lang();
        foreach($lists as $no => $row){
            $row_no = (($start+1)+$no);
            $htmls .= '<tr class="'.$row['status'].'">';
                $htmls .= '<td class="no" scope="row">'.$row_no.'</td>';
                $htmls .= '<td class="date">'.Helper::datetimeDisplay($row['date_create'], $lang).'</td>';
                $htmls .= '<td class="name">';
                    $htmls .= '<mark class="doc row-no">'.$row_no.'</mark>';
                    $htmls.= '<span class="date-o"><i class="uil uil-calendar-alt"></i> '.Helper::datetimeDisplay($row['date_create'], $lang).'</span>';
                    $htmls .= '<font><i class="uil uil-user-circle"></i> '.Helper::stringTitleShort($row['fullname']).'</font>';
                    $htmls.= '<span class="email">';
                        $htmls.= '<i class="uil uil-envelopes"></i> '.$row['email'];
                        if($row['is_cmu']=='Y'){
                            $htmls .= '<span class="cmu text-violet"><i class="uil uil-check-circle"></i> CMU</span>';
                        }else{
                            $htmls .= '<span class="cmu text-ash"><i class="uil uil-times-circle"></i> CMU</span>';
                        }
                    $htmls.= '</span>';
                $htmls .= '</td>';
                $htmls .= '<td class="status">';
                    $htmls .= '<font>'.$row['email'].'</font>';
                    if($row['is_cmu']=='Y'){
                        $htmls.= ' <mark class="cmu text-white bg-violet"><i class="uil uil-check-circle"></i>CMU</mark>';
                    }
                $htmls .= '</td>';
                $htmls .= '<td class="actions">';
                    $htmls .= '<div class="btn-box"><button onclick="manage_events(\'manage\', { \'meeting_id\':\''.$row['id'].'\' });" type="button" class="btn btn-circle btn-soft-primary"><i class="uil uil-edit-alt"></i></button><small class=b-tip>'.(($lang=='en')?'Edit':'แก้ไข').'</small></div>';
                    $htmls .= '<div class="btn-box"><button onclick="manage_events(\'display\', { \'meeting_id\':\''.$row['id'].'\' });" type="button" class="btn btn-circle btn-primary"><i class="uil uil-user"></i></button><small class=b-tip>'.Lang::get('Data').'</small></div>';
                $htmls .= '</td>';
            $htmls .= '</tr>';
        }
    }
    $result['htmls'] = $htmls;
    if( $result['total']<=0 ){
        $result['text'] = '0 - 0 / 0';
    }else{
        $result['text'] = number_format(($start+1),0).' - '.( (($start+$limit)>$result['total']) ? number_format($result['total'],0) : number_format(($start+$limit),0) ).' / '.number_format($result['total'],0);
    }
    // Returns
    echo json_encode($result);
    exit();
?>