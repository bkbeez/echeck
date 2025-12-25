<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/event'); ?>
<?php
    $form = ( (isset($_POST['form_as'])&&$_POST['form_as']) ? $_POST['form_as'] : null );
    $data = null;
    if( isset($_POST['events_id']) && $_POST['events_id'] ){
        $user_id = '';
        if (isset($_SESSION['login']) && isset($_SESSION['login']['user'])) {
            $user_id = isset($_SESSION['login']['user']['email']) ? $_SESSION['login']['user']['email'] : 
                        (isset($_SESSION['login']['user']['id']) ? $_SESSION['login']['user']['id'] : '');
        }
        $data = Event::getOwnedEvent($_POST['events_id'], $user_id);
    }
    if( !isset($data['events_id']) || !$data['events_id'] ){
        echo '<div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-body text-center"><p class="text-red">'.( (App::lang()=='en') ? 'Event not found' : 'ไม่พบกิจกรรม' ).'</p></div></div></div>';
        exit();
    }
    
    // Format dates for display
    $start_date_display = '';
    $end_date_display = '';
    if( isset($data['start_date']) && $data['start_date'] && $data['start_date'] != '0000-00-00' ){
        $start_date_display = Helper::dateDisplay($data['start_date'], App::lang());
    }
    if( isset($data['end_date']) && $data['end_date'] && $data['end_date'] != '0000-00-00' ){
        $end_date_display = Helper::dateDisplay($data['end_date'], App::lang());
    }
    
    // Get status value (extract number from enum like "1=เปิดการเข้าร่วม")
    $status_value = '0';
    if( isset($data['status']) && $data['status'] ){
        if( preg_match('/^(\d+)=/', $data['status'], $matches) ){
            $status_value = $matches[1];
        }
    }
?>
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-manage">
        <form name="RecordForm" action="<?=$form?>/scripts/update.php" method="POST" enctype="multipart/form-data" class="form-manage" target="_blank">
            <input type="hidden" name="events_id" value="<?=((isset($data['events_id'])&&$data['events_id'])?$data['events_id']:null)?>">
            <div class="modal-header" style="min-height:100px;background:#eef6f9;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h2 class="mb-0 text-start on-text-oneline"><i class="uil uil-edit-alt fs-32"></i> <?=( (App::lang()=='en') ? 'Edit Event' : 'แก้ไขกิจกรรม' )?></h2>
            </div>
            <div class="modal-body" style="margin-top:-30px;padding-left:35px;padding-right:35px;">
                <div class="on-status"></div>
                <div class="alert alert-info alert-icon mb-2" style="padding:5px 15px;">
                    <div class="row gx-1">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-floating mb-1">
                                <div class="form-control on-text-display"><?=((isset($data['events_id'])&&$data['events_id'])?$data['events_id']:'-')?></div>
                                <label><?=((isset($data['date_create'])&&$data['date_create'])?Helper::datetimeDisplay($data['date_create'], App::lang()):'-')?></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info alert-icon mb-2" style="padding:5px 15px;">
                    <p class="lead text-dark mb-1 text-start on-text-oneline"><?=( (App::lang()=='en') ? 'Event Information' : 'ข้อมูลกิจกรรม' )?></p>
                    <div class="form-floating mb-1">
                        <input name="events_name" value="<?=((isset($data['events_name'])&&$data['events_name'])?htmlspecialchars($data['events_name']):null)?>" type="text" class="form-control" placeholder="<?=( (App::lang()=='en') ? 'Event Name' : 'ชื่อกิจกรรม' )?>" id="events_name" required>
                        <label for="events_name"><?=( (App::lang()=='en') ? 'Event Name' : 'ชื่อกิจกรรม' )?> *<span></span></label>
                        <div class="on-events_name"></div>
                    </div>
                    <div class="row gx-1">
                        <div class="col-lg-6 col-md-6">
                            <div class="form-floating mb-1">
                                <input name="start_date" type="text" id="start_date" class="form-control" placeholder="dd/mm/yyyy" value="<?=$start_date_display?>" required autocomplete="off">
                                <label for="start_date"><?=( (App::lang()=='en') ? 'Start Date' : 'วันที่เริ่มต้น' )?> *<span></span></label>
                                <div class="on-start_date"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="form-floating mb-1">
                                <input name="end_date" type="text" id="end_date" class="form-control" placeholder="dd/mm/yyyy" value="<?=$end_date_display?>" required autocomplete="off">
                                <label for="end_date"><?=( (App::lang()=='en') ? 'End Date' : 'วันที่สิ้นสุด' )?> *<span></span></label>
                                <div class="on-end_date"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mb-1">
                        <select name="participant_type" class="form-select" id="participant_type">
                            <option value="ALL" <?=((isset($data['participant_type'])&&$data['participant_type']=='ALL')?' selected':null)?>><?=( (App::lang()=='en') ? 'Everyone' : 'ทุกคน' )?></option>
                            <option value="LIST" <?=((isset($data['participant_type'])&&$data['participant_type']=='LIST')?' selected':null)?>><?=( (App::lang()=='en') ? 'List Only' : 'เฉพาะรายชื่อ' )?></option>
                        </select>
                        <label for="participant_type"><?=( (App::lang()=='en') ? 'Participant Type' : 'ประเภทผู้เข้าร่วม' )?></label>
                    </div>
                    <div class="form-floating mb-1">
                        <select name="status" class="form-select" id="status">
                            <option value="0" <?=($status_value=='0'?' selected':null)?>><?=( (App::lang()=='en') ? 'Draft' : 'ร่าง' )?></option>
                            <option value="1" <?=($status_value=='1'?' selected':null)?>><?=( (App::lang()=='en') ? 'Open' : 'เปิดการเข้าร่วม' )?></option>
                            <option value="2" <?=($status_value=='2'?' selected':null)?>><?=( (App::lang()=='en') ? 'Closed' : 'ปิดการเข้าร่วม' )?></option>
                            <option value="3" <?=($status_value=='3'?' selected':null)?>><?=( (App::lang()=='en') ? 'Cancelled' : 'ยกเลิก' )?></option>
                        </select>
                        <label for="status"><?=( (App::lang()=='en') ? 'Status' : 'สถานะ' )?></label>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-center">
                <div class="confirm-box"></div>
                <div class="row gx-1 row-button">
                    <div class="col-lg-6 col-md-6 pt-1">
                        <button type="button" class="btn btn-lg btn-icon btn-icon-start btn-blue rounded-pill w-100" onclick="record_events('confirm');"><i class="uil uil-check-circle"></i><?=Lang::get('Save')?></button>
                    </div>
                    <div class="col-lg-6 col-md-6 pt-1">
                        <button type="button" class="btn btn-lg btn-icon btn-icon-start btn-soft-ash rounded-pill w-100" data-bs-dismiss="modal"><i class="uil uil-times-circle"></i><?=Lang::get('Close')?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    function record_events(action, params){
        $("form[name='RecordForm'] .on-status, form[name='RecordForm'] .on-focus").html('');
        if(action=="confirm"){
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
        // Initialize date pickers
        let startDatePicker, endDatePicker;
        
        const startInput = document.getElementById('start_date');
        const endInput = document.getElementById('end_date');
        
        // Format initial values for display
        if (startInput && startInput.value) {
            const parts = startInput.value.split('/');
            if (parts.length === 3) {
                // Already in d/m/Y format
            } else if (/^\d{4}-\d{2}-\d{2}$/.test(startInput.value)) {
                // Convert Y-m-d to d/m/Y
                const dateParts = startInput.value.split('-');
                startInput.value = dateParts[2] + '/' + dateParts[1] + '/' + dateParts[0];
            }
        }
        if (endInput && endInput.value) {
            const parts = endInput.value.split('/');
            if (parts.length === 3) {
                // Already in d/m/Y format
            } else if (/^\d{4}-\d{2}-\d{2}$/.test(endInput.value)) {
                // Convert Y-m-d to d/m/Y
                const dateParts = endInput.value.split('-');
                endInput.value = dateParts[2] + '/' + dateParts[1] + '/' + dateParts[0];
            }
        }
        
        startDatePicker = flatpickr("#start_date", {
            dateFormat: "d/m/Y",
            altInput: false,
            placeholder: "dd/mm/yyyy",
            allowInput: true,
            parseDate: function(datestr, format) {
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
                if (selectedDates.length > 0) {
                    const formatted = selectedDates[0].toISOString().split('T')[0];
                    instance.input.setAttribute('data-date-value', formatted);
                }
            }
        });
        
        if (startDatePicker.selectedDates.length > 0) {
            endDatePicker.set('minDate', startDatePicker.selectedDates[0]);
        }
        
        $("form[name='RecordForm']").ajaxForm({
            beforeSubmit: function (formData, jqForm, options) {
                // Convert date format before submit
                if (startInput && startInput.value) {
                    const startValue = startInput.getAttribute('data-date-value');
                    if (startValue) {
                        startInput.value = startValue;
                    } else if (startDatePicker && startDatePicker.selectedDates.length > 0) {
                        startInput.value = startDatePicker.selectedDates[0].toISOString().split('T')[0];
                    } else {
                        const parts = startInput.value.split('/');
                        if (parts.length === 3) {
                            startInput.value = parts[2] + '-' + parts[1].padStart(2, '0') + '-' + parts[0].padStart(2, '0');
                        }
                    }
                }
                
                if (endInput && endInput.value) {
                    const endValue = endInput.getAttribute('data-date-value');
                    if (endValue) {
                        endInput.value = endValue;
                    } else if (endDatePicker && endDatePicker.selectedDates.length > 0) {
                        endInput.value = endDatePicker.selectedDates[0].toISOString().split('T')[0];
                    } else {
                        const parts = endInput.value.split('/');
                        if (parts.length === 3) {
                            endInput.value = parts[2] + '-' + parts[1].padStart(2, '0') + '-' + parts[0].padStart(2, '0');
                        }
                    }
                }
                
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
                        if( typeof window.location.reload === 'function' ){
                            window.location.reload();
                        }else{
                            document.location.reload();
                        }
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
</script>
