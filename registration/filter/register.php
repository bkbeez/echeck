<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/admin/?users'); ?>
<?php
    $form = ( (isset($_POST['form_as'])&&$_POST['form_as']) ? $_POST['form_as'] : null );
    $events_id = $_POST['events_id'] ?? null;
    $event = DB::one("SELECT events_name FROM events WHERE events_id = :id", ['id'=>$events_id]);
    $lists = DB::sql("SELECT * FROM events_lists WHERE events_id = :id ORDER BY firstname ASC", ['id'=>$events_id]);
?>
<style type="text/css">

    .modal-manage .modal-content {
        border-radius: 20px;
        overflow: hidden;
        border: none;
    }

    .modal-manage .modal-header {
        background: #ffffff;
        border-bottom: 1px solid #f0f0f0;
        padding: 30px 20px 15px 20px;
        display: block;
        text-align: center;
    }

    .modal-manage .modal-body {
        padding: 20px 30px;
    }

    .regis-title { font-size: 1.5rem;
        font-weight: bold; color: #333;
        margin-bottom: 5px; 
    }

    .event-info { font-size: 1rem;
        color: #666;
        margin-bottom: 20px;
    }

    .tab-container {
        display: inline-flex;
        background: #f1f3f5;
        border-radius: 50px;
        padding: 5px;
        margin-bottom: 20px;
        border: 1px solid #e0e0e0;
    }

    .tab-btn {
        padding: 8px 35px;
        border-radius: 50px;
        cursor: pointer;
        font-weight: 500;
        color: #777;
        transition: all 0.3s ease;
        border: none;
        background: transparent;
    }

    .tab-btn.active {
        background: #ffffff;
        color: #0d6efd;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .search-wrapper {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 15px;
    }

    .input-search-custom {
        border-radius: 50px;
        border: 1px solid #ddd;
        padding: 8px 20px;
        width: 220px;
        font-size: 0.9rem;
        outline: none;
        transition: border 0.3s;
    }

    .input-search-custom:focus {
        border-color: #0d6efd;
    }

    .table-custom {
        border-collapse: separate;
        border-spacing: 0 8px;
        width: 100%;
    }

    .table-custom thead th {
        border: none;
        color: #888;
        font-weight: 500;
        padding: 10px;
        text-transform: uppercase;
        font-size: 0.85rem;
    }

    .table-custom tbody tr {
        background: #fff;
        transition: transform 0.2s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .table-custom tbody tr:hover {
        background: #f8faff;
    }

    .table-custom td {
        padding: 15px;
        border-top: 1px solid #f0f0f0;
        border-bottom: 1px solid #f0f0f0;
    }

    .table-custom td:first-child {
        border-left: 1px solid #f0f0f0;
        border-radius: 12px 0 0 12px;
    }

    .table-custom td:last-child { 
        border-right: 1px solid #f0f0f0; 
        border-radius: 0 12px 12px 0; 
    
    }
    .btn-regis {
        border-radius: 50px;
        padding: 6px 20px;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .icon-qrcode-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        border: 2px solid #5d5fef;
        color: #5d5fef;
        margin-right: 10px;
    }

    .modern-search-container {
        display: flex;
        align-items: center;
        background: #ffffff;
        padding: 6px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid #edf2f7;
        width: 100%;
        max-width: 420px;
    }

    .search-input-group {
        display: flex;
        align-items: center;
        flex-grow: 1;
        padding-left: 12px;
    }

    .search-icon-lead {
        color: #5d5fef;
        font-size: 20px;
        margin-right: 10px;
    }

    .input-modern {
        border: none;
        outline: none;
        width: 100%;
        font-size: 15px;
        color: #475569;
        font-weight: 500;
    }

    .btn-modern-primary {
        background: linear-gradient(135deg, #5d5fef 0%, #4a4cd9 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        padding: 10px 22px;
        font-weight: 600;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(93, 95, 239, 0.3);
    }

    .btn-modern-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(93, 95, 239, 0.4);
        filter: brightness(1.1);
    }

    .btn-modern-primary:active {
        transform: translateY(0);
        box-shadow: 0 2px 6px rgba(93, 95, 239, 0.2);
    }

    .input-modern::placeholder {
        color: #94a3b8;
        font-weight: 400;
    }

    .qr-scan-area {
        background: #ffffff;
        border-radius: 30px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        display: inline-block;
        position: relative;
        margin: 20px 0;
    }

    .qr-icon-wrapper {
        position: relative;
        font-size: 120px;
        color: #5d5fef;
        line-height: 1;
    }

    .scan-line {
        position: absolute;
        width: 100%;
        height: 4px;
        background: rgba(93, 95, 239, 0.5);
        box-shadow: 0 0 15px #5d5fef;
        top: 0;
        left: 0;
        border-radius: 10px;
        animation: scanning 2s infinite ease-in-out;
    }

    @keyframes scanning {
        0%, 100% { top: 0%; }
        50% { top: 100%; }
    }

    .qr-instruction {
        color: #64748b;
        font-size: 1.1rem;
        margin-top: 20px;
        font-weight: 500;
    }


    #pane-qr {
        flex-direction: column;
        align-items: center;
    }
</style>
</style>
<div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content modal-manage shadow-lg">
        <div class="modal-header">
            <button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="regis-title">ลงทะเบียน</div>
            <div class="event-info text-start">ชื่อกิจกรรม : <span class="text-primary"><?=($event['events_name'] ?? 'ไม่ได้ระบุ')?></span></div>
            <div class="tab-container">
                <button type="button" class="tab-btn active" id="btn-list" onclick="changeTab('list')">รายชื่อ</button>
                <button type="button" class="tab-btn" id="btn-qr" onclick="changeTab('qr')">QR Code</button>
            </div>
        </div>
        <div class="modal-body bg-light">
            <div id="pane-list">
                <div class="row mb-3">
                    <div class="col-md-12 d-flex justify-content-end">
                        <div class="modern-search-container">
                            <div class="search-input-group">
                                <i class="uil uil-search search-icon-lead"></i>
                                <input type="text" id="findInput" class="input-modern" placeholder="ค้นหารายชื่อ..." onkeyup="doSearch()">
                            </div>
                            <button type="button" class="btn-modern-primary">ค้นหา</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 450px; padding: 2px;">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th width="10%" class="text-center">#</th>
                                <th width="60%">ชื่อ - นามสกุล</th>
                                <th width="30%" class="text-center">ลงทะเบียน</th>
                            </tr>
                        </thead>
                        <tbody id="p_table">
                            <?php if($lists): foreach($lists as $i => $row): ?>
                            <tr class="item-row">
                                <td class="text-center text-muted"><?=($i+1)?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?=$row['firstname'].' '.$row['lastname']?></div>
                                    <div class="small text-muted"><i class="uil uil-card-atm"></i> <?=$row['student_id']?></div>
                                </td>
                                <td class="text-center" id="action-cell-<?=$row['id']?>">
                                    <?php if($row['status'] == 1): ?>
                                        <button class="btn btn-success btn-regis disabled"><i class="uil uil-check-circle"></i> เรียบร้อย</button>
                                    <?php else: ?>
                                        <button class="btn btn-outline-primary btn-regis" onclick="ajaxCheckin('<?=$row['id']?>', '<?=$events_id?>')">ลงทะเบียน</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="3" class="text-center py-5 text-muted">ยังไม่พบรายชื่อผู้ลงทะเบียน</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="pane-qr" style="display: none;" class="text-center py-5">
                <div class="qr-scan-area animate__animated animate__zoomIn">
                    <div class="qr-icon-wrapper">
                        <div class="scan-line"></div>
                        <i class="uil uil-qrcode-scan"></i>
                    </div>
                </div>

                <div class="mt-3">
                    <h4 class="fw-bold text-dark">พร้อมสำหรับการสแกน</h4>
                </div>

                <div id="qr-status-msg" class="mt-4" style="display:none;">
                    <span class="badge bg-soft-success text-success p-2 px-4 rounded-pill">
                        <i class="uil uil-check-circle me-1"></i> ตรวจสอบข้อมูลสำเร็จ
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function changeTab(mode) {
        $('.tab-btn').removeClass('active');
        if(mode === 'list') {
            $('#btn-list').addClass('active');
            $('#pane-list').fadeIn(200);
            $('#pane-qr').hide();
        } else {
            $('#btn-qr').addClass('active');
            $('#pane-qr').fadeIn(200);
            $('#pane-list').hide();
        }
    }
    function doSearch() {
        var input = document.getElementById("findInput").value.toUpperCase();
        var rows = document.querySelectorAll(".item-row");
        rows.forEach(function(row) {
            var text = row.textContent || row.innerText;
            row.style.display = text.toUpperCase().indexOf(input) > -1 ? "" : "none";
        });
    }
    function ajaxCheckin(id, ev_id) {
        if(!confirm('ยืนยันการลงทะเบียนเข้างาน?')) return;
        if(typeof runStart === "function") runStart();
        $.ajax({
            url: '<?=$form?>/filter/action_checkin.php',
            type: 'POST',
            data: { id: id, events_id: ev_id },
            dataType: 'json',
            success: function(res) {
                if(typeof runStop === "function") runStop();
                if(res.status === 'success') {
                    $('#action-cell-'+id).html('<button class="btn btn-success btn-regis disabled animate__animated animate__pulse"><i class="uil uil-check-circle"></i> เรียบร้อย</button>');
                } else {
                    alert(res.message);
                }
            },
            error: function() {
                if(typeof runStop === "function") runStop();
                alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
            }
        });
    }
</script>