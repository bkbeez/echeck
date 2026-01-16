<?php 
include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); 
Auth::ajax(APP_PATH.'/admin/?users'); 

$form = $_POST['form_as'] ?? null;
$events_id = $_POST['events_id'] ?? null;

$event = DB::one("SELECT events_name FROM events WHERE events_id = :id", ['id' => $events_id]);
$lists = DB::sql("SELECT * FROM events_lists WHERE events_id = :id ORDER BY date_create DESC", ['id' => $events_id]);
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .modal-manage .modal-content { 
        border-radius: 24px; border: none; 
        box-shadow: 0 10px 40px rgba(0,0,0,0.08); 
        background-color: #ffffff; overflow: hidden; 
    }
    .modal-manage .modal-header-custom { 
        background: #fdfdff; padding: 25px 30px; 
        color: #1a1b1f; position: relative; 
        border-bottom: 1px solid #f0f0f5; 
    }
    .modal-manage .header-title { 
        color: #5d5fef; font-weight: 700; 
        font-size: 1.4rem; 
        margin-bottom: 5px; 
    }
    .search-group-wrapper { 
        padding: 20px 30px; 
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
    }
    .search-input-group:focus-within { 
        border-color: #5d5fef;
        background: #fff; 
        box-shadow: 0 0 0 4px rgba(93, 95, 239, 0.1); 
    }
    .search-input-group .form-control { 
        background: transparent; 
        border: none; padding: 8px 10px; 
        font-size: 0.95rem; 
        box-shadow: none; 
    }
    .table-responsive-custom { 
        padding: 0 20px 20px 20px; 
        overflow-x: auto; 
    }
    .table-index-style { 
        width: 100%; 
        border-collapse: collapse; 
        min-width: 800px; 
    }
    .table-index-style thead th { 
        background-color: #fff; 
        color: #a0a3bd; 
        font-weight: 600; 
        font-size: 0.85rem; 
        padding: 15px; 
        border-bottom: 1px solid #f0f0f5; 
        text-align: center; 
    }
    .table-index-style td { 
        padding: 18px 15px; 
        vertical-align: middle; 
        text-align: center; 
        color: #4e4b66; 
    }
    .badge-status-index { 
        display: inline-flex; 
        align-items: center; 
        padding: 6px 14px; 
        border-radius: 8px; 
        font-size: 0.8rem; 
        font-weight: 600; 
    }
    .btn-delete-soft { 
        color: #ed2e7e; 
        background: transparent;
        border: 1px solid #ffe5f0; 
        width: 38px; height: 38px; 
        border-radius: 10px; 
        cursor: pointer; 
    }
    .btn-delete-soft:hover { 
        background: #ed2e7e; 
        color: #fff; 
    }
    .btn-close-custom { 
        position: absolute; 
        right: 25px; top: 25px; 
        background: #f5f6fa; 
        border: none; 
        border-radius: 50%; 
        width: 35px; 
        height: 35px; 
        color: #a0a3bd; 
        cursor: pointer; 
        }
</style>

<div class="modal-dialog modal-xl modal-dialog-centered modal-manage">
    <div class="modal-content">
        <div class="modal-header-custom">
            <h3 class="header-title">รายชื่อผู้ลงทะเบียน</h3>
            <div class="text-muted small"><i class="uil uil-calender me-1"></i> กิจกรรม: <?=htmlspecialchars($event['events_name'] ?? 'ไม่พบชื่อกิจกรรม')?></div>
            <button type="button" class="btn-close-custom" data-bs-dismiss="modal"><i class="uil uil-times"></i></button>
        </div>

        <div class="search-group-wrapper">
            <div class="search-input-group">
                <i class="uil uil-search mt-2" style="color:#a0a3bd"></i>
                <input type="text" id="findInput" class="form-control" placeholder="ค้นหาชื่อหรือหน่วยงาน..." onkeyup="doSearch()">
            </div>
            <div class="text-muted small">รวมทั้งสิ้น <strong><?=count($lists ?: [])?></strong> รายการ</div>
        </div>

        <div class="table-responsive-custom">
            <table class="table-index-style">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="25%" class="text-start">ชื่อ-นามสกุล</th>
                        <th width="20%">หน่วยงาน</th>
                        <th width="15%">วันที่ลงทะเบียน</th>
                        <th width="15%">สถานะ</th>
                        <th width="5%">จัดการ</th>
                    </tr>
                </thead>
                <tbody id="p_table">
                    <?php if(!empty($lists)): foreach($lists as $i => $row): ?>
                        <tr class="item-row">
                            <td class="text-muted small"><?=($i+1)?></td>
                            <td class="text-start"><strong><?=htmlspecialchars(($row['prefix']??'').$row['firstname'].' '.$row['lastname'])?></strong></td>
                            <td><span class="text-muted small"><?=htmlspecialchars($row['organization'] ?? '-')?></span></td>
                            <td><?=date('d M Y H:i', strtotime($row['date_create']))?> น.</td>
                            <td>
                                <?php if(($row['status'] ?? 0) == 1): ?>
                                    <span class="badge-status-index" style="background: #ecfdf3; color: #10b981;"><i class="uil uil-check-circle me-1"></i> ลงทะเบียนสำเร็จ</span>
                                <?php else: ?>
                                    <span class="badge-status-index" style="background: #fff4e5; color: #ff9800;"><i class="uil uil-clock me-1"></i> ยังไม่ได้สแกน</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn-delete-soft" onclick="cancelRegis('<?=$row['id']?>', '<?=$events_id?>')"><i class="uil uil-trash-alt"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="6" class="py-5 text-center text-muted">ไม่พบข้อมูลผู้ลงทะเบียน</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function doSearch() {
    let input = document.getElementById("findInput").value.toUpperCase();
    let rows = document.querySelectorAll(".item-row");
    rows.forEach(row => {
        row.style.display = row.innerText.toUpperCase().includes(input) ? "" : "none";
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
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.showLoading();
            $.ajax({
                url: '/registration/script/delete.php',
                type: 'POST',
                data: { id: id, events_id: ev_id },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire('สำเร็จ', res.message, 'success').then(() => {
                            if (typeof manage_events === 'function') {
                                manage_events('edit', {events_id: ev_id});
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire('เกิดข้อผิดพลาด', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้', 'error');
                }
            });
        }
    });
}
</script>