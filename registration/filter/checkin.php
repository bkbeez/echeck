<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/admin/?users'); ?>
<?php
    $form = ( (isset($_POST['form_as'])&&$_POST['form_as']) ? $_POST['form_as'] : null );
    $user_id = ( (isset($_POST['user_id'])&&$_POST['user_id']) ? intval($_POST['user_id']) : 0 );
    // ตรวจสอบค่าที่ส่งมา (Validate Input)
    if( !isset($_POST['events_id']) || !$_POST['events_id'] ){
        Status::error( 'ไม่พบรหัสกิจกรรม (Events ID)' );
    }
    if( !isset($_POST['firstname']) || !$_POST['firstname'] ){
        Status::error( 'กรุณาระบุชื่อจริง' );
    }

    // --- เตรียมข้อมูล (Parameters) ---
    $events_id    = $_POST['events_id'];
    $student_id   = isset($_POST['student_id']) ? trim($_POST['student_id']) : '';
    $email        = isset($_POST['email']) ? trim($_POST['email']) : '';
    
    // ข้อมูลส่วนตัว
    $params = array();
    $params['events_id']    = $events_id;
    $params['prefix']       = isset($_POST['prefix']) ? $_POST['prefix'] : '';
    $params['firstname']    = $_POST['firstname'];
    $params['lastname']     = isset($_POST['lastname']) ? $_POST['lastname'] : '';
    $params['organization'] = isset($_POST['organization']) ? $_POST['organization'] : '';
    $params['email']        = $email;
    $params['student_id']   = $student_id;
    
    // ข้อมูลระบบ (System Data)
    $params['status']       = 1; // 1 = เช็คอิน/เข้าร่วม
    $params['user_action']  = User::get('email'); // ผู้ทำรายการ (Staff)
    $params['now']          = date('Y-m-d H:i:s');

    // --- ตรวจสอบรายชื่อซ้ำ (Duplicate Check) ---
    // ตรวจสอบจาก Student ID หรือ Email เพื่อดูว่าเคยมีรายชื่อในกิจกรรมนี้ไหม
    $check_sql = "SELECT id FROM events_lists WHERE events_id = :events_id ";
    $check_params = array('events_id' => $events_id);
    
    $is_exist = false;
    $target_id = null;

    if( $student_id != '' ){
        $check_sql .= " AND student_id = :student_id ";
        $check_params['student_id'] = $student_id;
        $existing = DB::one($check_sql, $check_params);
    } else if ( $email != '' ){
        $check_sql .= " AND email = :email ";
        $check_params['email'] = $email;
        $existing = DB::one($check_sql, $check_params);
    } else {
        $existing = false;
    }

    if( $existing ){
        // --- กรณีที่ 1: มีรายชื่ออยู่แล้ว (Update Status) ---
        $update_sql = "UPDATE events_lists SET 
                        status = 1
                        , date_checkin = :now
                        , user_checkin = :user_action
                        , date_update = :now
                        , user_update = :user_action
                        -- อัปเดตข้อมูลส่วนตัวด้วย เผื่อมีการแก้ไขหน้างาน
                        , prefix = :prefix
                        , firstname = :firstname
                        , lastname = :lastname
                        , organization = :organization
                        WHERE id = :id AND events_id = :events_id";
        
        // เพิ่ม ID สำหรับ WHERE condition
        $params['id'] = $existing['id'];
        
        // ลบ params ที่ไม่ได้ใช้ใน Update (user_action เอาไว้ใช้แต่ query ใช้ชื่ออื่น)
        // เพื่อความชัวร์ สร้าง array ใหม่สำหรับ execute
        $exec_params = [
            'now' => $params['now'],
            'user_action' => $params['user_action'],
            'prefix' => $params['prefix'],
            'firstname' => $params['firstname'],
            'lastname' => $params['lastname'],
            'organization' => $params['organization'],
            'id' => $existing['id'],
            'events_id' => $params['events_id']
        ];

        if( DB::update($update_sql, $exec_params) ){
            Status::success( "เช็คอินสำเร็จ (อัปเดตข้อมูลเดิม)", array('action'=>'update') );
        } else {
            Status::error( "ไม่สามารถบันทึกข้อมูลได้ (Update Error)" );
        }

    } else {
        // --- กรณีที่ 2: ไม่มีรายชื่อ (Insert New Record) ---
        // สร้าง ID ใหม่ (ถ้า Database ไม่ได้ Auto Increment หรือต้องการ UUID)
        $params['id'] = md5(uniqid(rand(), true)); 

        $insert_sql = "INSERT INTO events_lists (
            id
            , events_id
            , prefix
            , firstname
            , lastname
            , organization
            , email
            , student_id
            , status
            , date_create
            , user_create
            , date_checkin
            , user_checkin
        ) VALUES (
            :id
            , :events_id
            , :prefix
            , :firstname
            , :lastname
            , :organization
            , :email
            , :student_id
            , :status
            , :now
            , :user_action
            , :now
            , :user_action
        )";

        if( DB::query($insert_sql, $params) ){
            Status::success( "ลงทะเบียนและเช็คอินสำเร็จ", array('action'=>'insert') );
        } else {
            Status::error( "ไม่สามารถเพิ่มรายชื่อได้ (Insert Error)" );
        }
    }
?>
?>
<style type="text/css">
    .modal-dialog .modal-header {
        min-height:100px;
        background: #edf9f6;
    }
    .modal-dialog .modal-body {
        margin-top: -30px;
        padding-left: 35px;
        padding-right: 35px;
    }
    .modal-dialog .modal-body>.alert {
        padding: 5px 15px;
    }
    .modal-dialog .modal-footer button>i {
        float: left;
        font-size: 24px;
        line-height: 24px;
        margin-right: 3px;
    }
</style>
<div class="modal-dialog modal-dialog-centered modal-lg"> <div class="modal-content modal-manage" style="background-color: #f5f5f5;"> <div class="modal-header border-0 pb-0 justify-content-center position-relative">
            <h3 class="mb-0 fw-bold">ลงทะเบียน</h3>
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body pt-2">
            <div class="text-start mb-3 px-3">
                <span class="fw-bold">ชื่อกิจกรรม :</span> 
                <span class="text-primary"><?=((isset($data['events_name'])&&$data['events_name'])?$data['events_name']:'-')?></span>
            </div>

            <div class="d-flex justify-content-center mb-3">
                <div class="btn-group bg-white rounded-pill border shadow-sm p-1" role="group" style="width: 300px;">
                    <input type="radio" class="btn-check" name="view_mode" id="mode_list" autocomplete="off" checked onchange="toggleView('list')">
                    <label class="btn btn-sm rounded-pill px-4 fw-bold" for="mode_list" id="label_list">รายชื่อ</label>

                    <input type="radio" class="btn-check" name="view_mode" id="mode_qr" autocomplete="off" onchange="toggleView('qr')">
                    <label class="btn btn-sm rounded-pill px-4 fw-bold" for="mode_qr" id="label_qr">QR Code</label>
                </div>
            </div>

            <div id="view_list_container">
                <div class="d-flex justify-content-end mb-3 px-3">
                    <div class="input-group" style="width: 250px;">
                        <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3"><i class="uil uil-search"></i></span>
                        <input type="text" id="search_participant" class="form-control border-start-0 rounded-end-pill" placeholder="ค้นหารายชื่อ" onkeyup="filterTable()">
                    </div>
                </div>

                <div class="table-responsive px-3" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover align-middle bg-white shadow-sm rounded">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th scope="col" width="10%">#</th>
                                <th scope="col" width="60%">ชื่อ - นามสกุล</th>
                                <th scope="col" width="30%" class="text-center">ลงทะเบียน</th>
                            </tr>
                        </thead>
                        <tbody id="table_body">
                            <?php if(count($participants) > 0): ?>
                                <?php foreach($participants as $index => $row): ?>
                                    <?php 
                                        // ตรวจสอบชื่อ (ถ้าแยก column firstname/lastname ก็ให้ concat)
                                        $fullname = $row['first_name'] . ' ' . $row['last_name']; // ปรับตามชื่อ Column จริงใน DB
                                        // สถานะเช็คอิน
                                        $is_checked = ($row['status'] == 1); 
                                    ?>
                                    <tr class="search-item">
                                        <td><?=($index+1)?></td>
                                        <td class="name-col"><?=$fullname?> <br><small class="text-muted"><?=$row['student_id']?></small></td>
                                        <td class="text-center">
                                            <?php if($is_checked): ?>
                                                <span class="badge bg-success rounded-pill"><i class="uil uil-check"></i> เรียบร้อย</span>
                                                <br><small class="text-muted"><?=date('d/m/Y H:i', strtotime($row['date_checkin']))?></small>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="manualCheckin('<?=$row['id']?>', '<?=$data['events_id']?>', this)">
                                                    ลงทะเบียน
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">ไม่พบรายชื่อผู้เข้าร่วม</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="view_qr_container" class="text-center py-4" style="display: none;">
                <div class="alert alert-info d-inline-block">
                    <i class="uil uil-camera"></i> พื้นที่สำหรับแสดงกล้อง Scan QR Code
                </div>
                <div id="reader" style="width: 300px; margin: 0 auto;"></div>
                </div>

        </div>
        <div class="modal-footer border-0">
            </div>
    </div>
</div>

<style>
    /* CSS สำหรับ Toggle ให้ดูเหมือนในภาพ */
    .btn-check:checked + .btn {
        background-color: #e0e0e0;
        color: #000;
        box-shadow: inset 0 3px 5px rgba(0,0,0,0.125);
    }
    .btn-check:not(:checked) + .btn {
        background-color: transparent;
        color: #666;
        border: none;
    }
</style>

<script type="text/javascript">
    // ฟังก์ชันสลับหน้าจอ List / QR
    function toggleView(mode) {
        if(mode === 'list') {
            $('#view_list_container').show();
            $('#view_qr_container').hide();
            // Start QR Scanner if needed
        } else {
            $('#view_list_container').hide();
            $('#view_qr_container').show();
            // Stop QR Scanner if needed
        }
    }

    // ฟังก์ชันค้นหารายชื่อในตาราง
    function filterTable() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("search_participant");
        filter = input.value.toUpperCase();
        table = document.getElementById("table_body");
        tr = table.getElementsByTagName("tr");

        for (i = 0; i < tr.length; i++) {
            // ค้นหาจาก Column ที่ 2 (Index 1) คือ ชื่อ-นามสกุล
            td = tr[i].getElementsByTagName("td")[1]; 
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }

    // ฟังก์ชันกดเช็คอินรายคน (Manual)
    function manualCheckin(id, events_id, btnElement) {
        if(!confirm('ยืนยันการลงทะเบียน?')) return;

        // เรียก AJAX ไปยังไฟล์ PHP เช็คอินที่เราทำไว้ก่อนหน้านี้
        $.ajax({
            url: '<?=$form?>/checkin/action_checkin.php', // ตรวจสอบ path ให้ถูกต้อง
            type: 'POST',
            data: { id: id, events_id: events_id },
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    // เปลี่ยนปุ่มเป็น Badge สีเขียว
                    var parentTd = $(btnElement).parent();
                    parentTd.html('<span class="badge bg-success rounded-pill"><i class="uil uil-check"></i> เรียบร้อย</span><br><small class="text-muted">เมื่อสักครู่</small>');
                } else {
                    alert(response.message || 'เกิดข้อผิดพลาด');
                }
            },
            error: function() {
                alert('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้');
            }
        });
    }
</script>