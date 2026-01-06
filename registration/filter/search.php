<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/registration'); ?>
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
    // Check
    $parameters = array();
    // Owner and Shared
    $parameters['user_by'] = User::get('email');
    $condition = " AND ( events.user_create=:user_by";
        $condition .= " OR events.events_id IN (SELECT events_shared.events_id FROM events_shared WHERE events_shared.email=:user_by)";
    $condition .= " )";
    // Search
    $_SESSION['login']['filter'][$filter_as]['keyword'] = null;
    if( $keyword ){
        $_SESSION['login']['filter'][$filter_as]['keyword'] = $keyword;
        $parameters['keyword'] = "%".$keyword."%";
        $condition .= " AND ( events.events_name LIKE :keyword";
            $condition .= " OR events.participant_type LIKE :keyword";
        $condition .= " )";
    }
    // Condition
    $_SESSION['login']['filter'][$filter_as]['condition'] = array();
    if( isset($_POST['condition']) ){
        foreach($_POST['condition'] as $key => $value ){
            if($value){
                if($key=="participant_type"){
                    $_SESSION['login']['filter'][$filter_as]['condition'][$key] = $value;
                    if($value=='ALLS'){
                        $condition .= " AND events.participant_type='ALL'";
                    }else if($value=='LIST'){
                        $condition .= " AND events.participant_type='LIST'";
                    }
                }else if($key=="status"){
                    $_SESSION['login']['filter'][$filter_as]['condition'][$key] = $value;
                    if($value=='DRAFT'){
                        $condition .= " AND events.status=0";
                    }else if($value=='OPEN'){
                        $condition .= " AND events.status=1";
                    }else if($value=='CLOSE'){
                        $condition .= " AND events.status=2";
                    }
                }else if($key=="start_date"){
                    $_SESSION['login']['filter'][$filter_as]['condition'][$key] = $value;
                    $parameters[$key] = Helper::dateSave($value).' 00:00:00';
                    $condition .= " AND events.start_date>=:".$key;
                }else if($key=="end_date"){
                    $_SESSION['login']['filter'][$filter_as]['condition'][$key] = $value;
                    $parameters[$key] = Helper::dateSave($value).' 23:59:59';
                    $condition .= " AND events.end_date<=:".$key;
                }else{
                    $_SESSION['login']['filter'][$filter_as]['condition'][$key] = $value;
                    $parameters[$key] = $value;
                    $condition .= " AND events.".$key."=:".$key;
                }
            }
        }
    }
    // Total and Pages
    $sql = "SELECT COUNT(events.events_id) AS total
            FROM events
            WHERE events.events_id IS NOT NULL";
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
    $sql = "SELECT events.*
            , CONCAT(DATE_FORMAT(events.start_date,'%d/%m/'), (YEAR(events.start_date)+543)) AS start_date_display
            , DATE_FORMAT(events.start_date, '%H:%i') AS start_time_display
            , CONCAT(DATE_FORMAT(events.end_date,'%d/%m/'), (YEAR(events.end_date)+543)) AS end_date_display
            , DATE_FORMAT(events.end_date, '%H:%i') AS end_time_display
            , IF(events.participant_type='LIST'
                ,'<span class=\"badge badge-sm bg-pale-orange text-orange rounded me-1 align-self-start\"><i class=\"uil uil-clipboard-alt\"></i>LIST</span>'
                ,'<span class=\"badge badge-sm bg-pale-blue text-blue rounded me-1 align-self-start\"><i class=\"uil uil-clipboard\"></i>ALL</span>'
            ) AS events_icon
            FROM events
            WHERE events.events_id IS NOT NULL";
    $sql .= $condition;
    $sql .= " ORDER BY events.date_create DESC";
    $sql .= " LIMIT $start, $limit;";
    $htmls = '';
    $lists = DB::sql($sql, $parameters);
    if( isset($lists)&&count($lists)>0 ){
        $lang = App::lang();
        if($filter_as == 'registration_event_as'){
            foreach($lists as $no => $row){
                $row_no = (($start+1)+$no);
                $htmls .= '<tr class="'.$row['events_id'].'">';
                    $htmls .= '<td class="no" scope="row">'.$row_no.'</td>';
                    $htmls .= '<td class="name autoline">';
                        $htmls .= '<mark class="doc row-no">'.$row_no.'</mark>';
                        $htmls .= '<font>'.$row['events_name'].'</font>';
                        $htmls .= '<div class="date-o">';
                            $htmls .= '<i class="uil uil-calendar-alt"></i> ';
                            $htmls .= $row['start_date_display'].' '.$row['start_time_display'];
                            $htmls .= ' - '.$row['end_date_display'].' '.$row['end_time_display'];
                        $htmls .= '</div>';
                    $htmls .= '</td>';
                    $htmls .= '<td class="status text-center">';
                        if($row['status'] == 1){
                            $htmls .= '<button class="btn btn-register" onclick="register_event(\'choosetype.php/index.php'.$row['events_id'].'\')">ลงทะเบียน</button>';
                        } else {
                            $htmls .= '<span class="badge bg-secondary">ปิดลงทะเบียน</span>';
                        }
                    $htmls .= '</td>';
                    $htmls .= '<td class="actions act-3">';
                    $htmls .= '</td>';
                $htmls .= '</tr>';
            }
        } else {
            foreach($lists as $no => $row){
                $row_no = (($start+1)+$no);
                $status = '<span class="badge badge-status badge-sm bg-orange text-white rounded me-1 align-self-start" onclick="manage_events(\'status\', { \'events_id\':\''.$row['events_id'].'\' });"><i class="uil uil-circle"></i>DRAFT</span>';
                if( $row['status']==1 ){
                    $status = '<span class="badge badge-status badge-sm bg-green text-white rounded me-1 align-self-start" onclick="manage_events(\'status\', { \'events_id\':\''.$row['events_id'].'\' });"><i class="uil uil-check-circle"></i>OPEN</span>';
                }else if( $row['status']==2 ){
                    $status = '<span class="badge badge-status badge-sm bg-red text-white rounded me-1 align-self-start" onclick="manage_events(\'status\', { \'events_id\':\''.$row['events_id'].'\' });"><i class="uil uil-times-circle"></i>CLOSE</span>';
                }
                $manage = '<span class="badge badge-shared badge-sm bg-grape text-white rounded me-1 align-self-start" onclick="manage_events(\'share\', { \'events_id\':\''.$row['events_id'].'\' });"><i class="uil uil-share-alt"></i><sup><b class="fs-13">'.$row['shares'].'</b> แชร์</sup></span>';
                $htmls .= '<tr class="'.$row['events_id'].'">';
                    $htmls .= '<td class="no" scope="row">'.$row_no.'</td>';
                    $htmls .= '<td class="type">'.$row['events_icon'].'</td>';
                    $htmls .= '<td class="name autoline">';
                        $htmls .= '<mark class="doc row-no">'.$row_no.'</mark>';
                        $htmls .= '<div class="type-o">';
                            $htmls .= $row['events_icon'];
                            $htmls .= '<span class="icon-o">'.$status.$manage.'</span>';
                        $htmls.= '</div>';
                        $htmls .= '<font>'.$row['events_name'].'</font>';
                        if( $row['list']>0||$row['participant_type']=='LIST' ){
                            $htmls .= ' <span class="badge badge-list badge-sm bg-pale-grape text-grape rounded me-1 align-self-start" onclick="manage_events(\'list\', { \'events_id\':\''.$row['events_id'].'\' });"><i class="uil uil-users-alt"></i><sup><b class="fs-13">'.$row['list'].'</b> รายชื่อ</sup></span>';
                        }
                        $htmls .= '<div class="date-o">';
                            $htmls .= '<i class="uil uil-calendar-alt"></i> ';
                            $htmls .= $row['start_date_display'].' '.$row['start_time_display'];
                            $htmls .= ' - '.$row['end_date_display'].' '.$row['end_time_display'];
                        $htmls.= '</div>';
                        //$htmls .= '<div class="status-o">'.$manage.'</div>';
                    $htmls .= '</td>';
                    $htmls .= '<td class="date">';
                        $htmls .= $row['start_date_display'];
                        $htmls .= '<br>&rang; เวลา '.$row['start_time_display'];
                    $htmls .= '</td>';
                    $htmls .= '<td class="date">';
                        $htmls .= $row['end_date_display'];
                        $htmls .= '<br>&rang; เวลา '.$row['end_time_display'];
                    $htmls .= '</td>';
                    $htmls .= '<td class="status">';
                        $htmls .= $status;
                        $htmls .= '<div>'.$manage.'</div>';
                    $htmls .= '</td>';
                    $htmls .= '<td class="actions act-3">';
                        $htmls .= '<div class="btn-box"><button onclick="manage_events(\'list\', { \'events_id\':\''.$row['events_id'].'\' });" type="button" class="btn btn-sm btn-circle btn-outline-primary"><i class="uil uil-users-alt"></i></button><small class=b-tip>รายชื่อ</small></div>';
                        $htmls .= '<div class="btn-box"><button onclick="manage_events(\'edit\', { \'events_id\':\''.$row['events_id'].'\' });" type="button" class="btn btn-sm btn-circle btn-outline-primary"><i class="uil uil-edit-alt"></i></button><small class=b-tip>แก้ไข</small></div>';
                        $htmls .= '<div class="btn-box delete"><button type="button" onclick="manage_events(\'delete\', { \'events_id\':\''.$row['events_id'].'\', \'events_name\':\''.$row['events_name'].'\' });" class="btn btn-sm btn-circle btn-outline-danger"><i class="uil uil-trash-alt"></i></button><small class=b-tip>ลบ</small></div>';
                    $htmls .= '</td>';
                $htmls .= '</tr>';
            }
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