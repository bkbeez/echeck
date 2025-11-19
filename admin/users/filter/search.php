<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/admin/?users'); ?>
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
    if( isset($_POST['condition']) ){
        foreach($_POST['condition'] as $key => $value ){
            if($value){
                if($key=="is_cmu"){
                    $_SESSION['login']['filter'][$filter_as]['condition'][$key] = $value;
                    if($value=='Y'){
                        $condition .= " AND member.is_cmu='Y'";
                    }else if($value=='N'){
                        $condition .= " AND member.is_cmu='N'";
                    }
                }else{
                    $_SESSION['login']['filter'][$filter_as]['condition'][$key] = $value;
                    $parameters[$key] = $value;
                    $condition .= " AND member.".$key."=:".$key;
                }
            }
        }
    }
    // Total and Pages
    $sql = "SELECT COUNT(member.id) AS total
            FROM member
            LEFT JOIN member_permission ON member.email=member_permission.email
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
            , member_permission.role AS user_role
            , 'NORM' AS status
            FROM member
            LEFT JOIN member_permission ON member.email=member_permission.email
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
                $htmls .= '<td class="mail">'.$row['email'].'</td>';
                $htmls .= '<td class="name">';
                    $htmls .= '<mark class="doc row-no">'.$row_no.'</mark>';
                    $htmls .= '<font class="mail-o">'.$row['email'].'</font>';
                    $htmls .= '<font>'.Helper::stringTitleShort($row['fullname']).'</font>';
                    $htmls .= '<span class="name-o"><i class="uil uil-user"></i> '.Helper::stringTitleShort($row['fullname']).'</span>';
                    $htmls .= '<span class="date-o"><i class="uil uil-calendar-alt"></i> '.Helper::datetimeDisplay($row['date_create'], $lang).'</span>';
                    $htmls .= '<span class="remark-o">';
                        if($row['is_cmu']=='Y'){
                            $htmls .= '<span class="cmu text-green"><i class="uil uil-check-circle"></i> <b class="underline-3 style-3 green">CMU</b> Account</span>';
                        }else{
                            $htmls .= '<span class="cmu text-ash"><i class="uil uil-circle"></i> CMU Account</span>';
                        }
                    $htmls.= '</span>';
                $htmls .= '</td>';
                $htmls .= '<td class="remark">';
                    $htmls .= '<font><i class="uil uil-calendar-alt"></i> '.Helper::datetimeDisplay($row['date_create'], $lang).'</font>';
                    if($row['is_cmu']=='Y'){
                        $htmls .= ' <span class="badge badge-lg bg-pale-green text-green rounded me-2 align-self-start"><i class="uil uil-check-circle"></i>CMU</span>';
                    }else{
                        $htmls .= ' <span class="badge badge-lg bg-pale-ash text-ash rounded me-2 align-self-start"><i class="uil uil-circle"></i>CMU</span>';
                    }
                $htmls .= '</td>';
                $htmls .= '<td class="actions">';
                    $htmls .= '<div class="btn-box"><button onclick="manage_events(\'edit\', { \'id\':\''.$row['id'].'\' });" type="button" class="btn btn-circle btn-outline-primary"><i class="uil uil-edit-alt"></i></button><small class=b-tip>'.(($lang=='en')?'Edit':'แก้ไข').'</small></div>';
                    if( $row['user_role']=='ADMIN' ){
                        $htmls .= '<div class="btn-box disabled"><button type="button" class="btn btn-circle btn-soft-ash text-ash" style="cursor:default;"><i class="uil uil-trash-alt"></i></button><small class=b-tip>'.Lang::get('Del').'</small></div>';
                    }else{
                        $htmls .= '<div class="btn-box delete"><button type="button" onclick="manage_events(\'delete\', { \'id\':\''.$row['id'].'\', \'email\':\''.$row['email'].'\' });" class="btn btn-circle btn-outline-danger"><i class="uil uil-trash-alt"></i></button><small class=b-tip>'.Lang::get('Del').'</small></div>';
                    }
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