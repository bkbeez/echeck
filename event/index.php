<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    $index['page'] = 'manage';
    $index['view'] = 'events';
    if( !Auth::check() ){
        $_SESSION['login_redirect'] = APP_HOME.'/'.$index['page']; 
        header('Location: '.APP_HOME.'/login');
        exit;
    }
    $form = (APP_PATH ? APP_PATH.'/' : '') . $index['page'];
    $formby = $form;
    // For relative paths from event/index.php
    $filter_path = 'filter';
    
    #https:://checkin.edu.cmu.ac.th/?events_id=EVENT_ID

    $user_id = '';
    if (isset($_SESSION['login']) && isset($_SESSION['login']['user'])) {
        $user_id = isset($_SESSION['login']['user']['email']) ? $_SESSION['login']['user']['email'] : 
    (isset($_SESSION['login']['user']['id']) ? $_SESSION['login']['user']['id'] : '');
    }

    $success = '';
    $error = '';
    
    if (isset($_SESSION['event_create_success'])) {
        $success = $_SESSION['event_create_success'];
        unset($_SESSION['event_create_success']);
    }
    if (isset($_SESSION['event_update_success'])) {
        $success = $_SESSION['event_update_success'];
        unset($_SESSION['event_update_success']);
    }
    if (isset($_SESSION['event_delete_success'])) {
        $success = $_SESSION['event_delete_success'];
        unset($_SESSION['event_delete_success']);
    }
    if (isset($_SESSION['event_delete_error'])) {
        $error = $_SESSION['event_delete_error'];
        unset($_SESSION['event_delete_error']);
    }
    // รับ error จาก GET parameter (เช่น จาก report redirect)
    if (isset($_GET['error']) && !empty($_GET['error'])) {
        $error = urldecode($_GET['error']);
    }
    

    if (isset($_GET['delete'])) {
        header('Location: delete.php?delete=' . intval($_GET['delete']));
        exit();
    }

    if (!function_exists('formatEventDate')) {
        function formatEventDate(?string $date): string
        {
            if (empty($date) || $date === '0000-00-00') {
                return '-';
            }
            return Helper::dateDisplay($date, 'th');
        }
    }

    if (!function_exists('statusBadgeClass')) {
        function statusBadgeClass(int $status): array
        {
            return match ($status) {
                1 => ['badge-open', 'เปิดเข้าร่วม'],
                2 => ['badge-closed', 'ปิดเข้าร่วม'],
                3 => ['badge-cancelled', 'ยกเลิก'],
                default => ['badge-draft', 'ร่าง'],
            };
        }
    }


    try {
        if (isset($_SESSION['login']) && isset($_SESSION['login']['user'])) {
            $events = Event::listForUser($user_id);
        } else {
            $events = Event::listOpenEvents();
        }
        
        if (!is_array($events)) {
            $events = [];
        }
    } catch (Exception $e) {
        $error = 'เกิดข้อผิดพลาดในการดึงข้อมูลกิจกรรม: ' . $e->getMessage();
        $events = [];
    }
?>
<?php include(APP_HEADER);?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<style type="text/css">
    body {
        min-height: 100vh;
        font-family: "Prompt", "Segoe UI", sans-serif;
        background:url('<?=THEME_IMG?>/map.png') top center;
    }
    .page-header {
        border-radius: 1.5rem;
        background: linear-gradient(135deg, rgba(13, 110, 253, 0.95), rgba(111, 66, 193, 0.92));
        color: #fff;
        padding: 2.5rem;
        box-shadow: 0 20px 45px rgba(13, 110, 253, 0.2);
        position: relative;
        overflow: hidden;
    }
    .page-header::after {
        content: "";
        position: absolute;
        top: -30%;
        right: -10%;
        width: 45%;
        height: 160%;
        background: rgba(255, 255, 255, 0.15);
        transform: rotate(15deg);
        pointer-events: none;
    }
    .page-header h1 {
        font-weight: 600;
        letter-spacing: 0.02em;
    }
    .page-header p {
        margin-bottom: 0;
        opacity: 0.85;
    }
    .btn-create {
        background: #fff;
        color: #0d6efd;
        border: none;
        font-weight: 600;
        border-radius: 999px;
        padding: 0.75rem 1.5rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .btn-create:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(255, 255, 255, 0.3);
    }
    .content-card {
        margin-top: -4rem;
        border-radius: 1.5rem;
        border: none;
        box-shadow: 0 16px 45px rgba(15, 23, 42, 0.1);
        overflow: hidden;
    }
    .content-card .card-body {
        padding: 0;
    }
    .table {
        margin-bottom: 0;
    }
    .table thead th {
        border-bottom: 2px solid #e2e8f0;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.75rem;
        color: #64748b;
        background-color: #f8fafc;
        padding: 1.25rem 1rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .table thead th:first-child {
        padding-left: 1.5rem;
    }
    .table thead th:last-child {
        padding-right: 1.5rem;
    }
    .table tbody tr {
        transition: all 0.15s ease;
        border-bottom: 1px solid #f1f5f9;
    }
    .table tbody tr:last-child {
        border-bottom: none;
    }
    .table tbody tr:hover {
        background-color: #f8fbff;
        box-shadow: 0 2px 8px rgba(13, 110, 253, 0.05);
    }
    .table tbody td {
        vertical-align: middle;
        padding: 1.25rem 1rem;
        color: #334155;
    }
    .table tbody td:first-child {
        padding-left: 1.5rem;
    }
    .table tbody td:last-child {
        padding-right: 1.5rem;
    }
    .table tbody td:nth-child(1) {
        min-width: 250px;
        max-width: 400px;
    }
    .table tbody td:nth-child(2),
    .table tbody td:nth-child(3) {
        white-space: nowrap;
        min-width: 140px;
    }
    .table tbody td:nth-child(4),
    .table tbody td:nth-child(5) {
        white-space: nowrap;
        min-width: 120px;
    }
    .table .badge-status {
        font-size: 0.7rem;
        letter-spacing: 0.05em;
        padding: 0.4rem 0.85rem;
        border-radius: 999px;
        text-transform: uppercase;
        font-weight: 600;
        display: inline-block;
    }
    .table .badge-draft {
        background: rgba(100, 116, 139, 0.15);
        color: #475569;
    }
    .table .badge-open {
        background: rgba(16, 185, 129, 0.15);
        color: #059669;
    }
    .table .badge-closed {
        background: rgba(245, 158, 11, 0.15);
        color: #d97706;
    }
    .table .badge-cancelled {
        background: rgba(239, 68, 68, 0.15);
        color: #dc2626;
    }
    .table .action-buttons .btn {
        border-radius: 999px;
        font-weight: 600;
        padding: 0.4rem 0.9rem;
    }
    .table .action-buttons .btn i {
        margin-right: 0.35rem;
    }
    .table .dropdown-toggle {
        border: none;
        background: transparent;
        color: #64748b;
        font-size: 1.25rem;
        padding: 0.5rem;
        border-radius: 50%;
        width: 2.5rem;
        height: 2.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    .table .dropdown-toggle:hover {
        background-color: #f1f5f9;
        color: #0d6efd;
    }
    .table .dropdown-toggle::after {
        display: none;
    }
    .table .dropdown-menu {
        border-radius: 0.75rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        padding: 0.5rem;
        min-width: 200px;
    }
    .table .dropdown-item {
        border-radius: 0.5rem;
        padding: 0.65rem 1rem;
        margin-bottom: 0.25rem;
        font-weight: 500;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .table .dropdown-item:last-child {
        margin-bottom: 0;
    }
    .table .dropdown-item:hover {
        background-color: #f8fafc;
    }
    .table .dropdown-item i {
        width: 1.25rem;
        text-align: center;
    }
    .table .dropdown-item.text-success:hover {
        background-color: rgba(16, 185, 129, 0.1);
        color: #059669;
    }
    .table .dropdown-item.text-primary:hover {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
    }
    .table .dropdown-item.text-secondary:hover {
        background-color: rgba(108, 117, 125, 0.1);
        color: #6c757d;
    }
    .table .dropdown-item.text-info:hover {
        background-color: rgba(13, 202, 240, 0.1);
        color: #0dcaf0;
    }
    .table .dropdown-item.text-danger:hover {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
    .table .empty-state {
        padding: 3rem 1rem;
        text-align: center;
        color: #94a3b8;
    }
    @media (max-width: 992px) {
        .page-header {
            padding: 2rem;
        }
        .page-header h1 {
            font-size: 1.9rem;
        }
        .content-card {
            margin-top: -3rem;
        }
    }
</style>
<div class="container py-5 position-relative">
    <div class="page-header mb-5">
        <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-4">
            <div>
                <h1 class="display-6 mb-2 text-white">หน้ารายการกิจกรรมทั้งหมด</h1>
            </div>
            <div class="text-lg-end d-flex gap-2 flex-wrap">
                <a href="../dashboard/" class="btn btn-create shadow-sm">
                    <i class="bi bi-graph-up-arrow me-2"></i>Dashboard
                </a>
                <button type="button" class="btn btn-create shadow-sm" onclick="manage_events('create');">
                    <i class="bi bi-plus-circle-fill me-2"></i>เพิ่มกิจกรรมใหม่
                </button>
            </div>
        </div>
    </div>
    
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="card content-card mt-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col" class="text-start">ชื่อกิจกรรม</th>
                            <th scope="col" class="text-center">วันที่เริ่มต้น</th>
                            <th scope="col" class="text-center">วันที่สิ้นสุด</th>
                            <th scope="col" class="text-center">ประเภทผู้เข้าร่วม</th>
                            <th scope="col" class="text-center">สถานะ</th>
                            <th scope="col" class="text-center" style="width: 80px;">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (count($events) > 0): ?>
                    <?php foreach ($events as $row): ?>
                        <?php
                            $updatedAt = $row['updated_at'] ?? ($row['created_at'] ?? null);
                            [$statusClass, $statusLabel] = statusBadgeClass((int)($row['status'] ?? 0));
                        ?>
                        <tr>
                            <td class="text-start">
                                <div class="fw-semibold text-dark" style="font-size: 0.95rem;"><?= htmlspecialchars($row['events_name'] ?? '-') ?></div>
                            </td>
                            <td class="text-center">
                                <span style="font-size: 0.9rem; color: #64748b;"><?= formatEventDate($row['start_date'] ?? null) ?></span>
                            </td>
                            <td class="text-center">
                                <span style="font-size: 0.9rem; color: #64748b;"><?= formatEventDate($row['end_date'] ?? null) ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-info-subtle text-info-emphasis px-3 py-2" style="font-size: 0.8rem;">
                                    <?= (($row['participant_type'] ?? '') === 'ALL') ? 'ทุกคน' : 'เฉพาะรายชื่อ' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge-status <?= $statusClass ?>"><?= $statusLabel ?></span>
                            </td>
                            <td class="text-center">
                                <div class="dropdown d-flex justify-content-center">
                                    <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="เมนูการจัดการ">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item text-success" href="../checkin/index.php?id=<?= urlencode($row['events_id'])?>">
                                                <i class="bi bi-file-earmark-text-fill"></i>
                                                <span>ลงทะเบียนเช็คอิน</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-success" href="../participants/index.php?id<?= urlencode($row['events_id'])?>">
                                                <i class="bi bi-people"></i>
                                                <span>รายชื่อผู้เข้าร่วม</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-primary" href="javascript:void(0);" onclick="manage_events('edit', { 'events_id': '<?= urlencode($row['events_id']) ?>' });">
                                                <i class="bi bi-pencil-fill"></i>
                                                <span>แก้ไข</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-secondary" href="javascript:void(0);" onclick="manage_events('share', { 'events_id': '<?= urlencode($row['events_id']) ?>' });">
                                                <i class="bi bi-share-fill"></i>
                                                <span>แชร์</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-info" href="../report/event_summary_report.php?id=<?= urlencode($row['events_id']) ?>">
                                                <i class="bi bi-file-earmark-text-fill"></i>
                                                <span>รายงาน</span>
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="delete.php?delete=<?= urlencode($row['events_id']) ?>" onclick="return confirm('ยืนยันการลบกิจกรรมนี้?')">
                                                <i class="bi bi-trash-fill"></i>
                                                <span>ลบ</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
                                    <h5 class="fw-semibold mb-1">ยังไม่มีกิจกรรม</h5>
                                    <p class="mb-0">เริ่มต้นสร้างกิจกรรมแรกของคุณ เพื่อให้ผู้เข้าร่วมพร้อมรับข้อมูล</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="ManageDialog" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="false" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered"></div>
</div>
<script type="text/javascript">
    var manageModal = null;
    $(document).ready(function(){
        // Initialize Bootstrap Modal
        var modalElement = document.getElementById('ManageDialog');
        if(modalElement){
            manageModal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false
            });
        }
    });
    function manage_events(action, params){
        if(action=='create'){
            params = params || {};
            params['form_as'] = '<?=$formby?>';
            var url = "<?=$filter_path?>/create.php";
            console.log('Loading URL:', url, 'Params:', params);
            $("#ManageDialog").load(url, params, function(response, status, xhr){
                if(status=="error"){
                    console.error('Error loading:', xhr.status, xhr.statusText, url);
                    $("#ManageDialog").html('<div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content text-center"><div class="modal-body">'+xhr.status + "<br>" + xhr.statusText+'</div></div></div>');
                    if(manageModal){
                        manageModal.show();
                    }
                }else{
                    if(manageModal){
                        manageModal.show();
                    }else{
                        var modalElement = document.getElementById('ManageDialog');
                        if(modalElement){
                            manageModal = new bootstrap.Modal(modalElement, {
                                backdrop: 'static',
                                keyboard: false
                            });
                            manageModal.show();
                        }
                    }
                }
            });
        }else if(action=='edit'){
            params = params || {};
            params['form_as'] = '<?=$formby?>';
            var url = "<?=$filter_path?>/edit.php";
            console.log('Loading URL:', url, 'Params:', params);
            $("#ManageDialog").load(url, params, function(response, status, xhr){
                if(status=="error"){
                    console.error('Error loading:', xhr.status, xhr.statusText, url);
                    $("#ManageDialog").html('<div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content text-center"><div class="modal-body">'+xhr.status + "<br>" + xhr.statusText+'</div></div></div>');
                    if(manageModal){
                        manageModal.show();
                    }
                }else{
                    if(manageModal){
                        manageModal.show();
                    }else{
                        var modalElement = document.getElementById('ManageDialog');
                        if(modalElement){
                            manageModal = new bootstrap.Modal(modalElement, {
                                backdrop: 'static',
                                keyboard: false
                            });
                            manageModal.show();
                        }
                    }
                }
            });
        }else if(action=='delete'){
            swal({
                'title':'<b class="text-red" style="font-size:100px;"><i class="uil uil-trash-alt"></i></b>',
                'html' :'<?=( (App::lang()=='en') ? 'Confirm to delete ' : 'ยืนยันลบ ' )?><span class="underline red">'+params.email+'</span> ?',
                'showCloseButton': false,
                'showConfirmButton': true,
                'showCancelButton': true,
                'focusConfirm': false,
                'allowEscapeKey': false,
                'allowOutsideClick': false,
                'confirmButtonClass': 'btn btn-icon btn-icon-start btn-success rounded-pill',
                'confirmButtonText':'<font class="fs-16"><i class="uil uil-check-circle"></i> <?=Lang::get('Yes')?></font>',
                'cancelButtonClass': 'btn btn-icon btn-icon-start btn-outline-danger rounded-pill',
                'cancelButtonText':'<font class="fs-16"><i class="uil uil-times-circle"></i> <?=Lang::get('No')?></font>',
                'buttonsStyling': false
            }).then(
                function () {
                    $.ajax({
                        url : "<?=$formby?>/scripts/delete.php",
                        type: 'POST',
                        data: params,
                        dataType: "json",
                        beforeSend: function( xhr ) {
                            runStart();
                        }
                    }).done(function(data) {
                        runStop();
                        if(data.status=='success'){
                            swal({
                                'type': data.status,
                                'title': data.title,
                                'html': data.text,
                                'showConfirmButton': false,
                                'timer': 1500
                            }).then(
                                function () {},
                                function (dismiss) {
                                    if (dismiss === 'timer') {
                                        $("form[name='filter'] button[type='submit']").click();
                                    }
                                }
                            );
                        }else{
                            swal({
                                'type' : data.status,
                                'title': data.title,
                                'html' : data.text,
                                'showCloseButton': false,
                                'showCancelButton': false,
                                'focusConfirm': false,
                                'allowEscapeKey': false,
                                'allowOutsideClick': false,
                                'confirmButtonClass': 'btn btn-outline-danger',
                                'confirmButtonText':'<span><?=Lang::get('Understand')?></span>',
                                'buttonsStyling': false
                            }).then(
                                function () {
                                    swal.close();
                                },
                                function (dismiss) {
                                    if (dismiss === 'cancel') {
                                        swal.close();
                                    }
                                }
                            );
                        }
                    });
                },
                function (dismiss) {
                    if (dismiss === 'cancel') {
                        swal.close();
                    }
                }
            );
        }else if(action=='share'){
            params = params || {};
            params['form_as'] = '<?=$formby?>';
            var url = "<?=$filter_path?>/share.php";
            console.log('Loading URL:', url, 'Params:', params);
            $("#ManageDialog").load(url, params, function(response, status, xhr){
                if(status=="error"){
                    console.error('Error loading:', xhr.status, xhr.statusText, url);
                    $("#ManageDialog").html('<div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content text-center"><div class="modal-body">'+xhr.status + "<br>" + xhr.statusText+'</div></div></div>');
                    if(manageModal){
                        manageModal.show();
                    }
                }else{
                    if(manageModal){
                        manageModal.show();
                    }else{
                        var modalElement = document.getElementById('ManageDialog');
                        if(modalElement){
                            manageModal = new bootstrap.Modal(modalElement, {
                                backdrop: 'static',
                                keyboard: false
                            });
                            manageModal.show();
                        }
                    }
                }
            });
        }
    }
    $(document).ready(function(){
        $("form[name='filter'] .filter-search select").change(function(){
            $("form[name='filter'] button[type='submit']").click();
        });
        $("form[name='filter'] .filter-search .btn-clear").click(function(){
            $("form[name='filter'] input[name='pages']").val(0);
            $("form[name='filter'] input[name='keyword']").val(null);
            $("form[name='filter'] .filter-search select").val('ALL');
            $("form[name='filter'] .filter-search .form-control").val(null);
            $("form[name='filter'] .filter-pagination select").val(1);
            $("form[name='filter'] button[type='submit']").click();
        });
        $(".table-filter").tablefilter({'keyword':'auto'});
    });
</script>
<?php include(APP_FOOTER);?>