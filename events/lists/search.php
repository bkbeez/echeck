<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    // Init
    $result = array('status'=>'success', 'title'=>Lang::get('Success') );
    $page = ((isset($_POST['page'])&&$_POST['page'])?intval($_POST['page']):1);
    $limit = ((isset($_POST['limit'])&&$_POST['limit'])?intval($_POST['limit']):100);
    $keyword = ((isset($_POST['keyword'])&&$_POST['keyword'])?$_POST['keyword']:null);
    $filter_as = ((isset($_POST['filter_as'])&&$_POST['filter_as'])?$_POST['filter_as']:'search_as');
    if( !isset($_SESSION['login']['filter'][$filter_as]['limit'])||$_SESSION['login']['filter'][$filter_as]['limit']!=$limit ){
        $_SESSION['login']['filter'][$filter_as]['limit'] = $limit;
        $page = 1;
    }else if( isset($_SESSION['login']['filter'][$filter_as]['keyword'])&&$_SESSION['login']['filter'][$filter_as]['keyword']!=$keyword ){
        $page = 1;
    }
    // Check
    $condition = "";
    $parameters = array();
    if( isset($_POST['events_id'])&&$_POST['events_id'] ){
        $parameters['events_id'] = $_POST['events_id'];
        $condition = " AND events_lists.events_id=:events_id";
        // Search
        $_SESSION['login']['filter'][$filter_as]['keyword'] = null;
        if( $keyword ){
            $_SESSION['login']['filter'][$filter_as]['keyword'] = $keyword;
            $parameters['keynum'] = $keyword."%";
            $parameters['keyword'] = "%".$keyword."%";
            $condition .= " AND ( events_lists.student_id LIKE :keynum";
                $condition .= " OR TRIM(CONCAT(COALESCE(events_lists.prefix,''),events_lists.firstname,' ',COALESCE(events_lists.lastname,''))) LIKE :keyword";
                $condition .= " OR events_lists.organization LIKE :keyword";
                $condition .= " OR events_lists.department LIKE :keyword";
                $condition .= " OR events_lists.remark LIKE :keyword";
            $condition .= " )";
        }
        // Condition
        $_SESSION['login']['filter'][$filter_as]['condition'] = array();
        if( isset($_POST['condition']) ){
            foreach($_POST['condition'] as $key => $value ){
                if($value){
                    if($key=="type"){
                        $_SESSION['login']['filter'][$filter_as]['condition'][$key] = $value;
                        if($value=='EMPLOYEE'){
                            $condition .= " AND events_lists.type='EMPLOYEE'";
                        }else if($value=='STUDENT'){
                            $condition .= " AND events_lists.type='STUDENT'";
                        }else if($value=='OTHER'){
                            $condition .= " AND events_lists.type='OTHER'";
                        }
                    }else if($key=="status"){
                        $_SESSION['login']['filter'][$filter_as]['condition'][$key] = $value;
                        if($value=='CHECKED'){
                            $condition .= " AND events_lists.status=0";
                        }else if($value=='UNCHECK'){
                            $condition .= " AND events_lists.status=1";
                        }else if($value=='CANCELLED'){
                            $condition .= " AND events_lists.status=2";
                        }
                    }else{
                        $_SESSION['login']['filter'][$filter_as]['condition'][$key] = $value;
                        $parameters[$key] = $value;
                        $condition .= " AND events_lists.".$key."=:".$key;
                    }
                }
            }
        }
    }else{
        $condition = " AND events_lists.events_id='NONE'";
    }
    // Total and Pages
    $sql = "SELECT COUNT(events_lists.id) AS total
            FROM events_lists
            WHERE events_lists.id IS NOT NULL";
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
    $sql = "SELECT events_lists.*
            , IF(events_lists.type='EMPLOYEE', '<span class=\"badge badge-sm bg-pale-green text-green rounded me-1 align-self-start\"><i class=\"uil uil-user\"></i>EMPLOYEE</span>'
                ,IF(events_lists.type='STUDENT', '<span class=\"badge badge-sm bg-pale-orange text-orange rounded me-1 align-self-start\"><i class=\"uil uil-user\"></i>STUDENT</span>'
                    ,'<span class=\"badge badge-sm bg-pale-dark text-dark rounded me-1 align-self-start\"><i class=\"uil uil-user\"></i>OTHER</span>'
                )
            ) AS type_icon
            , TRIM(CONCAT(COALESCE(events_lists.prefix,''),events_lists.firstname,' ',COALESCE(events_lists.lastname,''))) AS fullname
            FROM events_lists
            WHERE events_lists.id IS NOT NULL";
    $sql .= $condition;
    $sql .= " ORDER BY events_lists.date_create";
    $sql .= " LIMIT $start, $limit;";
    $htmls = '';
    $lists = DB::sql($sql, $parameters);
    if( isset($lists)&&count($lists)>0 ){
        $lang = App::lang();
        foreach($lists as $no => $row){
            $row_no = (($start+1)+$no);
            $status = '<span class="badge badge-status badge-sm bg-pale-dark text-dark rounded me-1 align-self-start" onclick="manage_events(\'status\', { \'events_id\':\''.$row['events_id'].'\' });"><i class="uil uil-circle"></i>ยังไมไ่ด้ลงทะเบียน</span>';
            if( $row['status']==1 ){
                $status = '<span class="badge badge-status badge-sm bg-pale-green text-green rounded me-1 align-self-start" onclick="manage_events(\'status\', { \'events_id\':\''.$row['events_id'].'\' });"><i class="uil uil-check-circle"></i>ลงทะเบียนแล้ว</span>';
            }else if( $row['status']==2 ){
                $status = '<span class="badge badge-status badge-sm bg-pale-red text-red rounded me-1 align-self-start" onclick="manage_events(\'status\', { \'events_id\':\''.$row['events_id'].'\' });"><i class="uil uil-times-circle"></i>ยกเลิกการลงทะเบียน</span>';
            }
            $htmls .= '<tr class="'.$row['events_id'].'">';
                $htmls .= '<td class="no" scope="row">'.$row_no.'</td>';
                $htmls .= '<td class="type">'.$row['type_icon'].'</td>';
                $htmls .= '<td class="name autoline">';
                    $htmls .= '<mark class="doc row-no">'.$row_no.'</mark>';
                    $htmls .= '<font>'.$row['fullname'].'</font>';
                    $htmls .= '<div class="organize-o">';
                        $htmls .= '<i class="uil uil-university"></i> '.$row['organization'];
                        $htmls .= ( $row['department'] ? ' &rang; '.$row['department'] : null );
                    $htmls.= '</div>';
                    $htmls .= '<div class="status-o">'.$status.'</div>';
                $htmls .= '</td>';
                $htmls .= '<td class="organize">';
                    $htmls .= $row['organization'];
                    $htmls .= ( $row['department'] ? ' &rang; '.$row['department'] : null );
                $htmls .= '</td>';
                $htmls .= '<td class="status">';
                    $htmls .= $status;
                $htmls .= '</td>';
                $htmls .= '<td class="actions act-2">';
                    $htmls .= '<div class="btn-box"><button onclick="manage_events(\'edit\', { \'id\':\''.$row['id'].'\', \'events_id\':\''.$row['events_id'].'\' });" type="button" class="btn btn-sm btn-circle btn-outline-primary"><i class="uil uil-edit-alt"></i></button><small class=b-tip>แก้ไข</small></div>';
                    $htmls .= '<div class="btn-box delete"><button type="button" onclick="manage_events(\'delete\', { \'id\':\''.$row['id'].'\', \'events_id\':\''.$row['events_id'].'\', \'fullname\':\''.$row['fullname'].'\' });" class="btn btn-sm btn-circle btn-outline-danger"><i class="uil uil-trash-alt"></i></button><small class=b-tip>ลบ</small></div>';
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