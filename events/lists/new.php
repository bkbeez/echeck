<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/admin/?users'); ?>
<?php
    $form = ( (isset($_POST['form_as'])&&$_POST['form_as']) ? $_POST['form_as'] : null );
?>
<style type="text/css">
    .modal-dialog .modal-header {
        min-height: 100px;
        background: #fef7ed;
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
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-manage">
        <form name="RecordForm" action="<?=$form?>/scripts/lists/create.php" method="POST" enctype="multipart/form-data" class="form-manage" target="_blank">
            <input type="hidden" name="events_id" value="<?=((isset($_POST['events_id'])&&$_POST['events_id'])?$_POST['events_id']:null)?>"/>
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h2 class="mb-0 text-blue text-start on-text-oneline"><i class="uil uil-user-plus" style="float:left;font-size:36px;line-height:36px;margin-right:3px;"></i> รายชื่อใหม่</h2>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning alert-icon mb-2">
                    <div class="form-floating form-select-wrapper mb-1">
                        <select id="type" name="type" class="form-select" aria-label="..." onchange="record_events('type', { 'self':this });">
                            <option value="EMPLOYEE">บุคลากร</option>
                            <option value="STUDENT">นักศึกษา</option>
                            <option value="OTHER" selected>บุคคลทั่วไป</option>
                        </select>
                        <label for="type">ประเภทผู้เข้าร่วม <span class="text-red">*</span></label>
                    </div>
                    <div class="form-floating mb-1 on-email" style="display:none;">
                        <input id="email" name="email" value="" type="email" class="form-control" placeholder="...">
                        <label for="email">อีเมลที่ลงทะเบียน <span class="text-red">*</span></label>
                    </div>
                    <div class="form-floating mb-1 on-student" style="display:none;">
                        <input id="student_id" name="student_id" value="" type="text" class="form-control" placeholder="...">
                        <label for="student_id">รหัสนักศึกษา <span class="text-red">*</span></label>
                    </div>
                    <div class="fs-12 text-red" style="margin-top:-5px;">* <em>ไม่สามารถเปลี่ยนแปลงในภายหลังไม่ได้</em></div>
                </div>
                <div class="alert alert-warning alert-icon mb-2">
                    <p class="lead text-dark mb-1 text-start on-text-oneline">ข้อมูลผู้เข้าร่วม</p>
                    <div class="form-floating mb-1">
                        <input id="prefix" name="prefix" value="" type="text" class="form-control" placeholder="...">
                        <label for="prefix">คำนำหน้า</label>
                    </div>
                    <div class="form-floating mb-1">
                        <input id="firstname" name="firstname" value="" type="text" class="form-control" placeholder="...">
                        <label for="firstname">ชื่อ <span class="text-red">*</span></label>
                    </div>
                    <div class="form-floating mb-1">
                        <input id="lastname" name="lastname" value="" type="text" class="form-control" placeholder="...">
                        <label for="lastname">สกุล</label>
                    </div>
                    <div class="form-floating form-select-wrapper mb-1">
                        <select id="organization" name="organization" class="form-select" aria-label="..." onchange="record_events('organization', { 'self':this });">
                            <option value="EMPTY">ไม่มี</option>
                            <?=Helper::organizationOption()?>
                            <option value="OTHER">อื่นๆ ระบุเอง</option>
                        </select>
                        <label for="organization">สังกัด</label>
                    </div>
                    <div class="form-floating mb-1 on-organization" style="display:none;">
                        <input id="organization_other" name="organization_other" value="" type="text" class="form-control" placeholder="...">
                        <label for="organization_other">ชื่อสังกัด <span class="text-red">*</span></label>
                    </div>
                    <div class="on-department">
                        <div class="form-floating mb-1">
                            <input id="department" name="department" value="" type="text" class="form-control" disabled placeholder="...">
                            <label for="department">หน่วยงาน/แผนก <sup>( <em>ถ้ามี...</em> )</sup></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-center">
                <div class="row gx-1 row-button">
                    <div class="col-lg-6 col-md-6 pt-1">
                        <button type="submit" class="btn btn-lg btn-blue rounded-pill w-100"><i class="uil uil-check-circle"></i>เพิ่มใหม่</button>
                    </div>
                    <div class="col-lg-6 col-md-6 pt-1">
                        <button type="button" class="btn btn-lg btn-outline-danger rounded-pill w-100" data-bs-dismiss="modal"><i class="uil uil-times-circle"></i>ยกเลิก</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    function record_events(action, params) {
        if(action=='type'){
            if(params.self.value=='EMPLOYEE'){
                $("form[name='RecordForm'] .on-email").fadeIn();
                $("form[name='RecordForm'] .on-student").fadeOut();
            }else if(params.self.value=='STUDENT'){
                $("form[name='RecordForm'] .on-email").fadeIn();
                $("form[name='RecordForm'] .on-student").fadeIn();
            }else{
                $("form[name='RecordForm'] .on-email").fadeOut();
                $("form[name='RecordForm'] .on-student").fadeOut();
            }
        }else if(action=='organization'){
            if(params.self.value=='EMPTY'){
                var htmls  = '<div class="form-floating mb-1">';
                    htmls += '<input id="department" name="department" value="" type="text" class="form-control" disabled placeholder="...">';
                    htmls += '<label for="department">หน่วยงาน/แผนก <sup>( <em>ถ้ามี...</em> )</sup></label>';
                htmls += '</div>';
                $("form[name='RecordForm'] .on-department").html(htmls);
            }else if(params.self.value=='OTHER'){
                $("form[name='RecordForm'] .on-organization").fadeIn();
            }else{
                $("form[name='RecordForm'] .on-organization").fadeOut();
                var htmls = '';
                if(params.self.value=='คณะศึกษาศาสตร์'){
                    htmls += '<div class="form-floating form-select-wrapper mb-1">';
                        htmls += '<select id="department" name="department" class="form-select" aria-label="..." onchange="record_events(\'department\', { \'self\':this });">';
                            htmls += '<?=Helper::departmentOption()?>';
                            htmls += '<option value="OTHER">อื่นๆ ระบุเอง</option>';
                        htmls += '</select>';
                        htmls += '<label for="department">หน่วยงาน/แผนก</label>';
                    htmls += '</div>';
                    htmls += '<div class="form-floating mb-1 on-department-other" style="display:none;">';
                        htmls += '<input id="department_other" name="department_other" value="" type="text" class="form-control" placeholder="...">';
                        htmls += '<label for="department_other">ชื่อหน่วยงาน/แผนก <span class="text-red">*</span></label>';
                    htmls += '</div>';
                }else{
                    htmls += '<div class="form-floating mb-1">';
                        htmls += '<input id="department" name="department" value="" type="text" class="form-control" placeholder="...">';
                        htmls += '<label for="department">หน่วยงาน/แผนก <sup>( <em>ถ้ามี...</em> )</sup></label>';
                    htmls += '</div>';
                }
                $("form[name='RecordForm'] .on-department").html(htmls);
            }
        }else if(action=='department'){
            if(params.self.value=='OTHER'){
                $("form[name='RecordForm'] .on-department-other").fadeIn();
            }else{
                $("form[name='RecordForm'] .on-department-other").fadeOut();
            }
        }
    }
    $(document).ready(function() {
        $("form[name='RecordForm']").ajaxForm({
            beforeSubmit: function (formData, jqForm, options) {
                $("form[name='RecordForm'] label>span>font").remove();
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
                            htmls +='<h4 class="mb-0 text-green on-text-oneline on-font-primary" style="color:#3a2e74;">'+data.title+'</h4>';
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
                    if( data.onfocus!=undefined&&data.onfocus ){
                        $("form[name='RecordForm'] input[name='"+data.onfocus+"']").focus();
                        $("form[name='RecordForm'] label[for='"+data.onfocus+"']>span").html('<font class="fs-12 on-text-normal-i text-red">'+data.text+'</font>');
                    }else if( data.onselect!=undefined&&data.onselect ){
                        $("form[name='RecordForm'] select[name='"+data.onselect+"']").focus();
                        $("form[name='RecordForm'] label[for='"+data.onselect+"']>span").html('<font class="fs-12 on-text-normal-i text-red">'+data.text+'</font>');
                    }
                }
            }
        });
    });
</script>