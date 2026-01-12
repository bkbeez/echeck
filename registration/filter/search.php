<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/registration'); ?>
<?php
    // Init
    $result = array('status'=>'success', 'title'=>Lang::get('ระบบลงทะเบียนกิจกรรม') );
    $page = ((isset($_POST['page'])&&$_POST['page'])?intval($_POST['page']):1);
    $limit = ((isset($_POST['limit'])&&$_POST['limit'])?intval($_POST['limit']):50);
    $keyword = ((isset($_POST['keyword'])&&$_POST['keyword'])?$_POST['keyword']:null);
    $filter_as = 'registration_event_as';
    if( !isset($_SESSION['login']['filter'][$filter_as]['limit'])||$_SESSION['login']['filter'][$filter_as]['limit']!=$limit ){
        $_SESSION['login']['filter'][$filter_as]['limit'] = $limit;
        $page = 1;
    }
    // --- Query Condition ---
    $parameters = array();
    $parameters['user_by'] = User::get('email');
    $condition = " AND ( events.user_create=:user_by";
    $condition .= " OR events.events_id IN (SELECT events_shared.events_id FROM events_shared WHERE events_shared.email=:user_by)";
    $condition .= " )";
    if( isset($_POST['condition']['status']) && $_POST['condition']['status'] != '' ){
        $status_map = ['DRAFT' => 0, 'OPEN' => 1, 'CLOSE' => 2];
        $val = $_POST['condition']['status'];
        if(isset($status_map[$val])){
            $condition .= " AND events.status = ".$status_map[$val];
        }
    } else {
        $condition .= " AND events.status = 1";
    }

    if( $keyword ){
        $parameters['keyword'] = "%".$keyword."%";
        $condition .= " AND ( events.events_name LIKE :keyword )";
    }

    // --- การนับจำนวนทั้งหมด ---
    $sql_count = "SELECT COUNT(events.events_id) AS total FROM events WHERE events.events_id IS NOT NULL";
    $count = DB::one($sql_count.$condition, $parameters);
    $result['total'] = (int)($count['total'] ?? 0);
    $result['pages'] = ceil($result['total'] / $limit) ?: 1;
    $result['page'] = ($page > $result['pages']) ? $result['pages'] : $page;

    // --- ดึงข้อมูลกิจกรรม ---
    $start = (($result['page']-1)*$limit);
    $sql = "SELECT events.*,
            (SELECT COUNT(*) FROM events_lists WHERE events_id = events.events_id) as registered_count,
            (SELECT COUNT(*) FROM events_lists WHERE events_id = events.events_id) as total_registered,
            CONCAT(DATE_FORMAT(events.start_date,'%d/%m/'), (YEAR(events.start_date)+543)) AS start_date_display,
            DATE_FORMAT(events.start_date, '%H:%i') AS start_time_display
            FROM events
            WHERE events.events_id IS NOT NULL $condition
            ORDER BY events.status ASC, events.start_date DESC
            LIMIT $start, $limit";

    $htmls = '';
    $lists = DB::sql($sql, $parameters);

    if( count($lists) > 0 ){
    foreach($lists as $no => $row){
        $row_no = (($start+1)+$no);
        $btns = '';
        
        if($row['status'] == 1) {
        $status_btn = '<span class="badge bg-green text-white rounded"><i class="uil uil-play"></i> เปิดให้ลงทะเบียน</span>';
        $btns .= '<button onclick="manage_events(\'register\', {events_id:\''.$row['events_id'].'\'});" 
            class="btn btn-primary rounded-3 d-flex align-items-center justify-content-center shadow-sm px-3" 
            style="height: 38px; min-width: 120px; border: none; transition: all 0.3s ease; gap: 8px;" 
            title="ลงทะเบียนกิจกรรม">
            <i class="uil uil-qrcode-scan" style="font-size: 1.2rem;"></i>
            <span style="font-size: 0.9rem; font-weight: 600; margin-bottom: 0;">ลงทะเบียน</span>
            </button>';
        } else if($row['status'] == 2) {
            $status_btn = '<span class="badge bg-red text-white rounded">ปิดกิจกรรม</span>';
            $btns = '<button disabled class="btn btn-sm btn-secondary w-100 rounded-pill">ปิดระบบแล้ว</button>';
        } else {
            $status_btn = '<span class="badge bg-orange text-white rounded">แบบร่าง</span>';
            $btns = '<button disabled class="btn btn-sm btn-secondary w-100 rounded-pill">ยังไม่เปิด</button>';
        }
        $htmls .= '<tr>';
        $htmls .= '<td class="text-center">'.$row_no.'</td>';
        $htmls .= '<td>';
        $htmls .= ' <div class="fw-bold text-dark">'.$row['events_name'].'</div>';
        $htmls .= ' <div class="small text-muted"><i class="uil uil-clock"></i> เริ่ม: '.$row['start_date_display'].' '.$row['start_time_display'].'</div>';
        $htmls .= ' <div class="mt-1">'.$status_btn.'</div>';
        $htmls .= '</td>';
        $htmls .= '<td class="text-center">';
        $htmls .= ' <div class="fs-14 fw-bold text-primary">'.$row['registered_count'].'</div>';
        $htmls .= ' <div class="small text-muted">คนลงทะเบียน</div>';
        $htmls .= '</td>';
        $htmls .= '<td class="text-center">';
        $htmls .= $btns;
        $htmls .= '</td>';
        $htmls .= '<td class="text-center">';
        $htmls .= '<button type="button" class="btn btn-soft-orange rounded-circle me-1 d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" onclick="manage_events(\'edit\', {events_id: \''.$row['events_id'].'\'}) " title="แก้ไขผู้ลงทะเบียน">
                    <i class="uil uil-edit"></i></button>';
        $htmls .= '<button type="button" class="btn btn-danger text-white rounded-circle me-1 d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" onclick="manage_events(\'pdf\', {events_id: \''.$row['events_id'].'\'})" title="พิมพ์รายงาน PDF">
                    <i class="uil uil-print"></i></button>';
        $htmls .= '<button type="button" class="btn btn-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" onclick="manage_events(\'excel\', {events_id: \''.$row['events_id'].'\'})" title="พิมพ์รายงาน Excel">
                    <i class="uil uil-file-share-alt"></i></button>';
        $htmls .= '</td>';
        $htmls .= '</tr>';

    }
} else {
    $htmls = '<tr><td colspan="4" class="text-center p-4">ไม่พบกิจกรรมที่เปิดให้ลงทะเบียน</td></tr>';
}

    $result['htmls'] = $htmls;
    echo json_encode($result);
    exit();
?>