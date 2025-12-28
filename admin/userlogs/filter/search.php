<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/admin/?logs'); ?>
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
        $condition .= " AND ( xlg_login.email LIKE :keyword";
            $condition .= " OR TRIM(CONCAT(member.name,' ',COALESCE(member.surname,''))) LIKE :keyword";
        $condition .= " )";
    }
    $_SESSION['login']['filter'][$filter_as]['condition'] = array();
    if( isset($_POST['condition']) ){
        foreach($_POST['condition'] as $key => $value ){
            if($value){
                if($key=="start_date"){
                    $_SESSION['login']['filter'][$filter_as]['condition'][$key] = $value;
                    $parameters[$key] = $value;
                    $condition .= " AND xlg_login.date_at>=:".$key;
                }else if($key=="end_date"){
                    $_SESSION['login']['filter'][$filter_as]['condition'][$key] = $value;
                    $parameters[$key] = $value;
                    $condition .= " AND xlg_login.date_at<=:".$key;
                }else{
                    $_SESSION['login']['filter'][$filter_as]['condition'][$key] = $value;
                    $parameters[$key] = $value;
                    $condition .= " AND xlg_login.".$key."=:".$key;
                }
            }
        }
    }
    // Total and Pages
    $sql = "SELECT COUNT(xlg_login.email) AS total
            FROM xlg_login
            LEFT JOIN member ON xlg_login.email=member.email
            WHERE xlg_login.email IS NOT NULL";
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
    $sql = "SELECT xlg_login.*
            , CONCAT(DATE_FORMAT(xlg_login.date_at,'%d/%m/'), (YEAR(xlg_login.date_at)+543),' ',DATE_FORMAT(xlg_login.date_at, '%H:%i:%s')) AS date_display
            , TRIM(CONCAT(COALESCE(member.title,''),member.name,' ',COALESCE(member.surname,''))) AS fullname
            , CONCAT(IF(xlg_login.ip_client IS NOT NULL,xlg_login.ip_client,'')
                , IF(xlg_login.device IS NOT NULL,CONCAT(' &rang; ',xlg_login.device),'')
                , IF(xlg_login.platform IS NOT NULL,CONCAT(' &rang; ',xlg_login.platform),'')
                , IF(xlg_login.browser IS NOT NULL,CONCAT(' &rang; ',xlg_login.browser),'')
            ) AS remark
            FROM xlg_login
            LEFT JOIN member ON xlg_login.email=member.email
            WHERE xlg_login.email IS NOT NULL";
    $sql .= $condition;
    $sql .= " ORDER BY xlg_login.date_at DESC";
    $sql .= " LIMIT $start, $limit;";
    $htmls = '';
    $lists = DB::sql($sql, $parameters);
    if( isset($lists)&&count($lists)>0 ){
        $lang = App::lang();
        foreach($lists as $no => $row){
            $htmls .= '<tr class="'.$row['status'].'">';
                $htmls .= '<td class="date">'.$row['date_display'].'</td>';
                $htmls .= '<td class="mail">'.$row['email'].'</td>';
                $htmls .= '<td class="name">';
                    $htmls .= '<font class="date-o"><i class="uil uil-calendar-alt"></i> '.$row['date_display'].'</font>';
                    $htmls .= '<font class="mail-o"><i class="uil uil-envelopes"></i> '.$row['email'].'</font>';
                    $htmls .= ( $row['fullname'] ? '<font>'.$row['fullname'].'</font>' : '<font class=text-muted><em>Unknown...</em></font>' );
                    $htmls .= '<span class="name-o"><i class="uil uil-user"></i> '.( $row['fullname'] ? $row['fullname'] : '<em>Unknown...</em>' ).'</span>';
                    $htmls .= ( $row['remark'] ? '<span class="remark-o">'.$row['remark'].'</span>' : null );
                $htmls .= '</td>';
                $htmls .= '<td class="remark">'.$row['remark'].'</td>';
                $htmls .= '<td class="actions">';
                    $htmls .= '<div class="btn-box"><button onclick="manage_events(\'detail\', { \'date_at\':\''.$row['date_at'].'\', \'email\':\''.$row['email'].'\' });" type="button" class="btn btn-sm btn-circle btn-outline-primary"><i class="uil uil-user"></i></button><small class=b-tip>ข้อมูล</small></div>';
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