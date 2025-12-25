<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    $form = ( (isset($_POST['form_as'])&&$_POST['form_as']) ? $_POST['form_as'] : null );
    if( isset($_POST['events_id']) && $_POST['events_id'] ){
        $data = DB::one("SELECT events.*
                        ,events_list.email AS user_role
                        FROM events
                        LEFT JOIN events_lists ON events.email=events_lists.email
                        WHERE events.events_id=:events_id
                        LIMIT 1;"
                        , array('events_id'=>$_POST['events_id'])
        );

        $list1s = DB::sql("SELECT * FROM events WHERE email=:email ORDER BY date_at DESC LIMIT 5;", array('email'=>$data['email']));
            if( isset($lists)&&count($lists)>0 ){
                foreach($lists as $item){
                    $changeloghtmls = '<div class="col-lg-12 col-md-12">';
                        $changeloghtmls .= '<div class="form-floating mb-1">';
                            $changeloghtmls .= '<div class="form-control on-text-display">'.$item['remark'].'</div>';
                            $changeloghtmls .= '<label>'.Helper::datetimeDisplay($item['date_at'], App::lang()).' - '.$item['title'].'</label>';
                        $changeloghtmls .= '</div>';
                    $changeloghtmls .= '</div>';
                }
            }else{
                $changeloghtmls = '<div class="col-lg-12 col-md-12">';
                    $changeloghtmls .= '<div class="mb-1">';
                        $changeloghtmls .= '<div class="form-control on-text-display text-ash"><em>- '.( (App::lang()=='en') ? 'No edit history' : 'ไม่มีการแก้ไข' ).'... .. .</em></div>';
                    $changeloghtmls .= '</div>';
                $changeloghtmls .= '</div>';
            }
        }
    
        if(isset($data["events_id"]) && $data["events_id"]){
            // Process form submission
            $updateData = array(
                'events_name' => isset($_POST['name']) ? trim($_POST['name']) : $data['events_name'],
                'start_date' => isset($_POST['start_date']) ? trim($_POST['start_date']) : $data['start_date'],
                'end_date' => isset($_POST['end_date']) ? trim($_POST['end_date']) : $data['end_date'],
                'participant_type' => isset($_POST['participant_type']) ? trim($_POST['participant_type']) : $data['participant_type'],
                'status' => isset($_POST['status']) ? intval($_POST['status']) : $data['status'],
            );
            
            try {
                DB::update('events', $updateData, array('events_id' => $data['events_id']));
                $success = "แก้ไขกิจกรรมสำเร็จ";
                $data = array_merge($data, $updateData);
            } catch (Exception $e) {
                $error = "เกิดข้อผิดพลาดในการแก้ไขกิจกรรม: " . $e->getMessage();
            }
        } else {
            $error = "ไม่พบกิจกรรมที่ต้องการแก้ไข";
        }
    
?>
    <div class="page-loader"></div>
        <div class="content-wrapper on-font-primary">
        <!-- Body -->
            <?=App::menus($index)?>
    <div class="container py-5">
        <div class="page-header mb-5">
            <h1 class="display-6 mb-2">✏️ แก้ไขกิจกรรม</h1>
            <p class="mb-0 opacity-75">แก้ไขข้อมูลกิจกรรม</p>
        </div>
        
        <div class="card content-card">
            <div class="card-body p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($event): ?>
                <form method="GET" class="mt-3">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($event['events_id']) ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">รหัสกิจกรรม</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($event['events_id']) ?>" disabled>
                        <small class="text-muted">รหัสกิจกรรมไม่สามารถแก้ไขได้</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ชื่อกิจกรรม <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($event['events_name']) ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วันที่เริ่มต้น <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="text" name="start_date" id="start_date" class="form-control" placeholder="dd/mm/yyyy" value="<?= htmlspecialchars($event['start_date']) ?>" required autocomplete="off">
                                <i class="bi bi-calendar position-absolute" style="right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #6c757d;"></i>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วันที่สิ้นสุด <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="text" name="end_date" id="end_date" class="form-control" placeholder="dd/mm/yyyy" value="<?= htmlspecialchars($event['end_date']) ?>" required autocomplete="off">
                                <i class="bi bi-calendar position-absolute" style="right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #6c757d;"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ประเภทผู้เข้าร่วม</label>
                        <select name="participant_type" class="form-select">
                            <option value="ALL" <?= ($event['participant_type'] === 'ALL') ? 'selected' : '' ?>>ทุกคน</option>
                            <option value="LIST" <?= ($event['participant_type'] === 'LIST') ? 'selected' : '' ?>>เฉพาะผู้ที่มีรายชื่อ</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">สถานะ</label>
                        <select name="status" class="form-select">
                            <option value="0" <?= ($eventStatusInt === 0) ? 'selected' : '' ?>>ร่าง</option>
                            <option value="1" <?= ($eventStatusInt === 1) ? 'selected' : '' ?>>เปิดการเข้าร่วม</option>
                            <option value="2" <?= ($eventStatusInt === 2) ? 'selected' : '' ?>>ปิดการเข้าร่วม</option>
                            <option value="3" <?= ($eventStatusInt === 3) ? 'selected' : '' ?>>ยกเลิก</option>
                        </select>
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-check-circle me-2"></i>บันทึกการแก้ไข
                        </button>
                        <a href="index.php" class="btn btn-secondary px-4">
                            <i class="bi bi-arrow-left me-2"></i>กลับ
                        </a>
                    </div>
                </form>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>ไม่พบข้อมูลกิจกรรม
                    </div>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>กลับไปยังรายการกิจกรรม
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script>
        function record_events(action, params){
        $("form[name='RecordForm'] .on-status, form[name='RecordForm'] .on-focus").html('');
        if(action=='info'){
            if( $(params.self).attr('display')=='N' ){
                $(params.self).attr('display', 'Y');
                $(params.self).find('span').attr('class','uil uil-minus');
                $(".form-manage .on-info").slideDown();
            }else{
                $(params.self).attr('display', 'N');
                $(params.self).find('span').attr('class','uil uil-plus');
                $(".form-manage .on-info").slideUp();
            }
        }else if(action=="confirm"){
            if( params!=undefined ){
                $("form[name='RecordForm'] .confirm-box").html('').css('margin-top','0');
                $("form[name='RecordForm'] .row-button").show();
            }else{
                var htmls  = '<div class="fs-19 mb-2 text-center on-text-normal"><?=( (App::lang()=='en') ? 'Are you sure to change ?' : 'ยืนยันการเปลี่ยนแปลง ?' )?></div>';                    
                    htmls += '<button type="submit" class="btn btn-lg btn-icon btn-icon-start btn-success rounded-pill"><i class="uil uil-check-circle"></i><?=Lang::get('Yes')?></button>';
                    htmls += '&nbsp;';
                    htmls += '<button type="button" class="btn btn-lg btn-icon btn-icon-start btn-outline-danger rounded-pill" onclick="record_events(\'confirm\', { \'on\':\'N\' });"><i class="uil uil-times-circle"></i><?=Lang::get('No')?></button>';
                $("form[name='RecordForm'] .confirm-box").html(htmls).css('margin-top','-15px');
                $("form[name='RecordForm'] .row-button").hide();
            }
        }
    }
    $(document).ready(function() {
        $("form[name='RecordForm']").ajaxForm({
            beforeSubmit: function (formData, jqForm, options) {
                $("form[name='RecordForm'] .on-status, form[name='RecordForm'] .on-focus").html('');
                runStart();
            },
            success: function(rs) {
                runStop();
                var data = JSON.parse(rs);
                if(data.status=='success'){
                    $("form[name='RecordForm'] .modal-header, form[name='RecordForm'] .modal-body, hr").hide();
                    var htmls ='<div class="d-flex flex-row text-start" style="margin-top:15px;">';
                        htmls +='<div style="margin-top:-5px;"><span class="icon btn btn-circle btn-lg btn-success pe-none me-4"><i class="uil uil-check"></i></span></div>';
                        htmls +='<div class="text-primary" style="font-weight:normal;">';
                            htmls +='<h4 class="mb-0 text-green on-text-oneline" style="color:#3a2e74;">'+data.title+'</h4>';
                            htmls +='<p class="fs-14 text-green">'+data.text+'</p>';
                        htmls +='</div>';
                    htmls +='</div>';
                    $("form[name='RecordForm'] .modal-footer").html(htmls);
                    $("form[name='RecordForm'] .modal-footer").fadeOut(1000, function(){
                        $("#ManageDialog").modal('hide');
                        $("form[name='filter'] input[name='state']").val(null);
                        $("form[name='filter'] button[type='submit']").click();
                    });
                }else{
                    if( data.swal!=undefined ){
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
                    }else{
                        if( $("form[name='RecordForm'] .on-"+data.onfocus).length>0 ){
                            $("form[name='RecordForm'] .on-"+data.onfocus).html('<font class="fs-12 on-text-normal-i text-red">'+data.text+'</font>');
                        }else{
                            $("form[name='RecordForm'] .on-status").html('<font class="on-text-normal-i text-red">'+data.text+'</font>');
                        }
                        $("form[name='RecordForm'] .confirm-box").html('').css('margin-top','0');
                        $("form[name='RecordForm'] .row-button").show();
                        if( data.onfocus!=undefined&&data.onfocus ){
                            $("form[name='RecordForm'] input[name='"+data.onfocus+"']").focus();
                        }
                    }
                }
            }
        });
    });
        // Helper function to format date for display
        function formatDateForDisplay(dateStr) {
            if (!dateStr) return '';
            // If already in Y-m-d format, convert to d/m/Y
            if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
                const parts = dateStr.split('-');
                return parts[2] + '/' + parts[1] + '/' + parts[0];
            }
            return dateStr;
        }

        // Initialize date pickers
        let startDatePicker, endDatePicker;
        
        document.addEventListener('DOMContentLoaded', function() {
            const startInput = document.getElementById('start_date');
            const endInput = document.getElementById('end_date');
            
            // Format initial values for display
            if (startInput.value) {
                startInput.value = formatDateForDisplay(startInput.value);
            }
            if (endInput.value) {
                endInput.value = formatDateForDisplay(endInput.value);
            }
            
            startDatePicker = flatpickr("#start_date", {
                dateFormat: "d/m/Y",
                altInput: false,
                placeholder: "dd/mm/yyyy",
                allowInput: true,
                parseDate: function(datestr, format) {
                    // Try to parse d/m/Y format (day/month/year)
                    const parts = datestr.split('/');
                    if (parts.length === 3) {
                        const day = parseInt(parts[0], 10);
                        const month = parseInt(parts[1], 10);
                        const year = parseInt(parts[2], 10);
                        if (month >= 1 && month <= 12 && day >= 1 && day <= 31 && year >= 1900) {
                            return new Date(year, month - 1, day);
                        }
                    }
                    return null;
                },
                onChange: function(selectedDates, dateStr, instance) {
                    // Update hidden value to Y-m-d format
                    if (selectedDates.length > 0) {
                        const formatted = selectedDates[0].toISOString().split('T')[0];
                        instance.input.setAttribute('data-date-value', formatted);
                        if (endDatePicker) {
                            endDatePicker.set('minDate', selectedDates[0]);
                        }
                    }
                }
            });

            endDatePicker = flatpickr("#end_date", {
                dateFormat: "d/m/Y",
                altInput: false,
                placeholder: "dd/mm/yyyy",
                allowInput: true,
                parseDate: function(datestr, format) {
                    // Try to parse d/m/Y format (day/month/year)
                    const parts = datestr.split('/');
                    if (parts.length === 3) {
                        const day = parseInt(parts[0], 10);
                        const month = parseInt(parts[1], 10);
                        const year = parseInt(parts[2], 10);
                        if (month >= 1 && month <= 12 && day >= 1 && day <= 31 && year >= 1900) {
                            return new Date(year, month - 1, day);
                        }
                    }
                    return null;
                },
                onChange: function(selectedDates, dateStr, instance) {
                    // Update hidden value to Y-m-d format
                    if (selectedDates.length > 0) {
                        const formatted = selectedDates[0].toISOString().split('T')[0];
                        instance.input.setAttribute('data-date-value', formatted);
                    }
                }
            });
            
            if (startDatePicker.selectedDates.length > 0) {
                endDatePicker.set('minDate', startDatePicker.selectedDates[0]);
            }
        });

        // Form validation and conversion
        document.querySelector('form').addEventListener('submit', function(e) {
            const startInput = document.getElementById('start_date');
            const endInput = document.getElementById('end_date');
            
            // Convert display format to Y-m-d for submission
            if (startInput.value) {
                const startValue = startInput.getAttribute('data-date-value');
                if (startValue) {
                    startInput.value = startValue;
                } else if (startDatePicker && startDatePicker.selectedDates.length > 0) {
                    startInput.value = startDatePicker.selectedDates[0].toISOString().split('T')[0];
                } else {
                    // Try to parse d/m/Y format (day/month/year)
                    const parts = startInput.value.split('/');
                    if (parts.length === 3) {
                        startInput.value = parts[2] + '-' + parts[1].padStart(2, '0') + '-' + parts[0].padStart(2, '0');
                    }
                }
            }
            
            if (endInput.value) {
                const endValue = endInput.getAttribute('data-date-value');
                if (endValue) {
                    endInput.value = endValue;
                } else if (endDatePicker && endDatePicker.selectedDates.length > 0) {
                    endInput.value = endDatePicker.selectedDates[0].toISOString().split('T')[0];
                } else {
                    // Try to parse d/m/Y format (day/month/year)
                    const parts = endInput.value.split('/');
                    if (parts.length === 3) {
                        endInput.value = parts[2] + '-' + parts[1].padStart(2, '0') + '-' + parts[0].padStart(2, '0');
                    }
                }
            }
            
            // Validate dates
            if (startInput.value && endInput.value) {
                const start = new Date(startInput.value);
                const end = new Date(endInput.value);
                
                if (isNaN(start.getTime()) || isNaN(end.getTime())) {
                    e.preventDefault();
                    alert('รูปแบบวันที่ไม่ถูกต้อง กรุณาใช้รูปแบบ dd/mm/yyyy');
                    return false;
                }
                
                if (start > end) {
                    e.preventDefault();
                    alert('วันที่เริ่มต้นต้องไม่เกินวันที่สิ้นสุด');
                    return false;
                }
            }
        });
    </script>
<?=App::footer($index)?>