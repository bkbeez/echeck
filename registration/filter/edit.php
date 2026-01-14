<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/admin/?users'); ?>
<?php
    $form = (isset($_POST['form_as']) ? $_POST['form_as'] : null);
    $events_id = $_POST['events_id'] ?? null;
    
    $event = DB::one("SELECT events_name FROM events WHERE events_id = :id", ['id' => $events_id]);
    $lists = DB::sql("SELECT * FROM events_lists WHERE events_id = :id ORDER BY date_create DESC", ['id' => $events_id]);
?>

<style>

    .modal-manage .modal-content { 
        border-radius: 24px; 
        border: none;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08); 
        background-color: #ffffff;
        overflow: hidden;
    }


    .modal-manage .modal-header-custom {
        background: #fdfdff;
        padding: 25px 30px;
        color: #1a1b1f;
        text-align: left;
        position: relative;
        border-bottom: 1px solid #f0f0f5;
    }
    
    .modal-manage .header-title {
        color: #5d5fef;
        font-weight: 700;
        font-size: 1.4rem;
        margin-bottom: 5px;
    }

    .search-group-wrapper {
        padding: 20px 30px;
        background: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
    }

    .search-input-group {
        display: flex;
        flex: 1;
        max-width: 350px;
        background: #f5f6fa;
        border-radius: 12px;
        padding: 5px 15px;
        border: 1px solid transparent;
        transition: 0.3s;
    }

    .search-input-group:focus-within {
        border-color: #5d5fef;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(93, 95, 239, 0.1);
    }

    .search-input-group .form-control {
        background: transparent;
        border: none;
        padding: 8px 10px;
        font-size: 0.95rem;
    }

    .search-input-group .btn-search {
        background: transparent;
        color: #a0a3bd;
        border: none;
    }

    .table-responsive-custom {
        padding: 0 20px 20px 20px;
    }

    .table-index-style {
        width: 100%;
        border-collapse: collapse;
    }

    .table-index-style thead th {
        background-color: #fff;
        color: #a0a3bd;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 15px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #f0f0f5;
        text-align: center;
    }

    .table-index-style tbody tr {
        border-bottom: 1px solid #f8f9fe;
        transition: 0.2s;
    }

    .table-index-style tbody tr:hover {
        background-color: #fcfcff;
        transform: scale(1.002);
    }

    .table-index-style td {
        padding: 18px 15px;
        vertical-align: middle;
        text-align: center;
        color: #4e4b66;
        font-size: 0.95rem;
    }

    .badge-status-index {
        background: #ecfdf3;
        color: #10b981;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .btn-delete-soft {
        color: #ed2e7e;
        background: transparent;
        border: 1px solid #ffe5f0;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        transition: 0.3s;
    }

    .btn-delete-soft:hover {
        background: #fff1f7;
        color: #ed2e7e;
        border-color: #ed2e7e;
    }

    /* Pagination: ปุ่มโค้งมน */
    .filter-pagination {
        padding: 20px 30px;
        border-top: 1px solid #f0f0f5;
    }

    .btn-page-action {
        border-radius: 12px;
        padding: 8px 16px;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #4a5568;
    }

    .btn-page-action:hover:not(:disabled) {
        background: #f8fafc;
        border-color: #5d5fef;
        color: #5d5fef;
    }

    .btn-close-custom {
        position: absolute;
        right: 25px;
        top: 25px;
        background: #f5f6fa;
        border: none;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        color: #a0a3bd;
        transition: 0.3s;
    }

    .btn-close-custom:hover {
        background: #ed2e7e;
        color: #fff;
    }
</style>

<div class="modal-dialog modal-xl modal-dialog-centered modal-manage">
    <div class="modal-content">
        <div class="modal-header-custom">
            <h3 class="header-title">รายชื่อผู้ลงทะเบียน</h3>
            <div class="text-muted" style="font-size: 0.9rem;">
                <i class="uil uil-calender me-1"></i> กิจกรรม: <?=htmlspecialchars($event['events_name'] ?? 'ไม่พบชื่อกิจกรรม')?>
            </div>
            <button type="button" class="btn-close-custom" data-bs-dismiss="modal">
                <i class="uil uil-times"></i>
            </button>
        </div>

        <div class="search-group-wrapper">
            <div class="search-input-group">
                <button class="btn-search"><i class="uil uil-search"></i></button>
                <input type="text" id="findInput" class="form-control" placeholder="ค้นหาชื่อผู้ลงทะเบียน" onkeyup="doSearch()">
            </div>
            
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small">แสดง</span>
                <select name="limit" class="form-select form-select-sm" style="border-radius: 8px; width: 80px;">
                    <option value="50"<?=((!isset($filter['limit'])||intval($filter['limit'])==50)?' selected':null)?>>50</option>
                        <option value="100"<?=((isset($filter['limit'])&&intval($filter['limit'])==100)?' selected':null)?>>100</option>
                        <option value="250"<?=((isset($filter['limit'])&&intval($filter['limit'])==250)?' selected':null)?>>250</option>
                        <option value="500"<?=((isset($filter['limit'])&&intval($filter['limit'])==500)?' selected':null)?>>500</option>
                        <option value="750"<?=((isset($filter['limit'])&&intval($filter['limit'])==750)?' selected':null)?>>750</option>
                        <option value="1000"<?=((isset($filter['limit'])&&intval($filter['limit'])==1000)?' selected':null)?>>1000</option>
                </select>
                <span class="text-muted small">รายการ</span>
            </div>
        </div>

        <div class="table-responsive-custom">
            <table class="table-index-style">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="20%" class="text-start">ชื่อ-นามสกุล</th>
                        <th width="20%">ตำแหน่ง/หน่วยงาน</th>
                        <th width="15%">ประเภท</th>
                        <th width="15%">วันที่ลงทะเบียน</th>
                        <th width="10%">สถานะ</th>
                        <th width="10%">จัดการ</th>
                    </tr>
                </thead>
                <tbody id="p_table">
                    <?php if($lists): foreach($lists as $i => $row): ?>
                    <tr class="item-row">
                        <td class="text-muted" style="font-size: 0.8rem;"><?=($i+1)?></td>
                        <td class="text-start">
                            <div class="fw-bold" style="color: #1a1b1f;"><?=htmlspecialchars($row['prefix'].$row['firstname'].' '.$row['lastname'])?></div>
                        </td>
                        <td><span class="text-muted small"><?=htmlspecialchars($row['organization'] ?? '-')?></span></td>
                        <td>
                            <span style="color: #5d5fef; font-size: 0.85rem; font-weight: 500;">
                                <?=htmlspecialchars($row['type'] ?? 'ทั่วไป')?>
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 500;"><?=date('d M Y', strtotime($row['date_create']))?></div>
                            <div class="text-muted small"><?=date('H:i', strtotime($row['date_create']))?> น.</div>
                        </td>
                        <td><span class="badge-status-index">ลงทะเบียนสำเร็จ</span></td>
                        <td>
                            <button title="ยกเลิก" class="btn-delete-soft" onclick="cancelRegis('<?=$row['id']?>', '<?=$row['events_id']?>')">
                                <i class="uil uil-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="7" class="py-5 text-center">
                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="60" style="opacity: 0.2;" class="mb-3">
                            <div class="text-muted">ยังไม่มีรายชื่อผู้ลงทะเบียนในขณะนี้</div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="filter-pagination">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">แสดงหน้าที่ 1 จาก 1</div>
                <div class="d-flex gap-2">
                    <button class="btn-page-action" disabled><i class="uil uil-angle-left"></i> ก่อนหน้า</button>
                    <button class="btn-page-action">ถัดไป <i class="uil uil-angle-right"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function doSearch() {
    var input = document.getElementById("findInput").value.toUpperCase();
    var rows = document.querySelectorAll(".item-row");
    rows.forEach(function(row) {
        var text = row.innerText.toUpperCase();
        row.style.display = text.indexOf(input) > -1 ? "" : "none";
    });
}

function cancelRegis(id, ev_id) {
    Swal.fire({
        title: 'ยืนยันการลบรายชื่อ?',
        text: "เมื่อลบแล้วจะไม่สามารถเรียกคืนข้อมูลได้",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#5d5fef',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ยืนยันลบ',
        cancelButtonText: 'ยกเลิก',
        borderRadius: '15px'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?=$form?>/script/delete.php',
                type: 'POST',
                data: { id: id, events_id: ev_id },
                dataType: 'json',
                success: function(res) {
                    if(res.status === 'success') {
                        manage_events('edit', {events_id: ev_id});
                        
                        if(typeof Toast !== 'undefined') {
                            Toast.fire({ icon: 'success', title: 'ลบเรียบร้อยแล้ว' });
                        }
                    } else {
                        Swal.fire('เกิดข้อผิดพลาด', res.message, 'error');
                    }
                }
            });
        }
    });
}
</script>