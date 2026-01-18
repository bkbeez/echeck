<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(); ?>
<?php 

$result = array('status'=>'success', 'title'=>Lang::get('ระบบลงทะเบียนกิจกรรม') );
$page = ((isset($_POST['page'])&&$_POST['page'])?intval($_POST['page']):1);
$limit = ((isset($_POST['limit'])&&$_POST['limit'])?intval($_POST['limit']):50);
$keyword = ((isset($_POST['keyword'])&&$_POST['keyword'])?$_POST['keyword']:null);
$filter_as = 'registration_event_as';

if( !isset($_SESSION['login']['filter'][$filter_as]['limit'])||$_SESSION['login']['filter'][$filter_as]['limit']!=$limit ){
    $_SESSION['login']['filter'][$filter_as]['limit'] = $limit;
    $page = 1;
}

$parameters = array();
$parameters['user_by'] = User::get('email');
$condition = " AND ( events.user_create=:user_by OR events.events_id IN (SELECT events_shared.events_id FROM events_shared WHERE events_shared.email=:user_by) )";
if( isset($_POST['condition']['status']) && $_POST['condition']['status'] != 'ALL' && $_POST['condition']['status'] != '' ){
    $status_map = ['DRAFT' => 0, 'OPEN' => 1, 'CLOSE' => 2];
    $val = $_POST['condition']['status'];
    if(isset($status_map[$val])){
        $condition .= " AND events.status = ".$status_map[$val];
    }
}

if( $keyword ){
    $parameters['keyword'] = "%".$keyword."%";
    $condition .= " AND ( events.events_name LIKE :keyword )";
}

$sql_count = "SELECT COUNT(events.events_id) AS total FROM events WHERE events.events_id IS NOT NULL";
$count = DB::one($sql_count.$condition, $parameters);
$result['total'] = (int)($count['total'] ?? 0);
$result['pages'] = ceil($result['total'] / $limit) ?: 1;
$result['page'] = ($page > $result['pages']) ? $result['pages'] : $page;
$start = (($result['page']-1)*$limit);

$sql = "SELECT events.*,
        (SELECT COUNT(*) FROM events_lists WHERE events_id = events.events_id) as registered_count,
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
            $btns .= '<button onclick="window.location.href=\''.APP_PATH.'/registration/check/?events_id='.$row['events_id'].'\';" 
                class="btn btn-primary rounded-3 d-flex align-items-center justify-content-center shadow-sm px-3" 
                style="height: 38px; min-width: 120px; border: none;" title="ลงทะเบียนกิจกรรม">
                <i class="uil uil-qrcode-scan me-1"></i>ลงทะเบียน</button>';
        } else if($row['status'] == 2) {
            $status_btn = '<span class="badge bg-red text-white rounded">ปิดกิจกรรม</span>';
            $btns = '<button disabled class="btn btn-sm btn-secondary w-100 rounded-pill">ปิดระบบแล้ว</button>';
        } else {
            $status_btn = '<span class="badge bg-orange text-white rounded">แบบร่าง</span>';
            $btns = '<button disabled class="btn btn-sm btn-secondary w-100 rounded-pill">ยังไม่เปิด</button>';
        }

        $htmls .= '<tr>
            <td class="text-center">'.$row_no.'</td>
            <td>
                <div class="fw-bold text-dark">'.$row['events_name'].'</div>
                <div class="small text-muted"><i class="uil uil-clock"></i> เริ่ม: '.$row['start_date_display'].' '.$row['start_time_display'].'</div>
                <div class="mt-1">'.$status_btn.'</div>
            </td>
            <td class="text-center">
                <div class="fs-14 fw-bold text-primary">'.number_format($row['registered_count']).'</div>
                <div class="small text-muted">คนลงทะเบียน</div>
            </td>
            <td class="text-center">'.$btns.'</td>
            <td class="text-center">
                <button type="button" class="btn btn-soft-orange rounded-circle me-1" style="width: 38px; height: 38px;" onclick="manage_events(\'edit\', {events_id: \''.$row['events_id'].'\'})"><i class="uil uil-edit"></i></button>
                <button type="button" class="btn btn-danger text-white rounded-circle me-1" style="width: 38px; height: 38px;" onclick="manage_events(\'pdf\', {events_id: \''.$row['events_id'].'\'})"><i class="uil uil-print"></i></button>
                <button type="button" class="btn btn-success text-white rounded-circle" style="width: 38px; height: 38px;" onclick="manage_events(\'excel\', {events_id: \''.$row['events_id'].'\'})"><i class="uil uil-file-share-alt"></i></button>
            </td>
        </tr>';
    }
} else {
    $htmls = '<tr><td colspan="5" class="text-center p-4">ไม่พบกิจกรรมที่ค้นหา</td></tr>';
}

$result['htmls'] = $htmls;
echo json_encode($result);
exit();