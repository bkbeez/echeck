<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    $form = ( (isset($_POST['form_as'])&&$_POST['form_as']) ? $_POST['form_as'] : null );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.edu.cmu.ac.th/v1/organize/1/childs');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, EDU_API_USER.":".EDU_API_PASS);
    $result = curl_exec($ch);
    curl_close($ch);
    $organizes = json_decode($result, true);
    if( isset($organizes)&&count($organizes)>0 ){
        $organizeinputs = '';
        $organizeoptions = '';
        foreach($organizes as $item){
            $organizeoptions .= '<option value="'.$item['organize_id'].'">'.$item['organize_name'].'</option>';
            $organizeinputs .= '<input type="hidden" name="organize['.$item['organize_id'].']" value="'.$item['organize_name'].'"/>';
        }
    }
?>
<style type="text/css">
    .modal-dialog .modal-header {
        min-height: 100px;
        background: #3f78e0;
    }
    .modal-dialog .modal-body {
        margin-top: -30px;
        padding-left: 35px;
        padding-right: 35px;
    }
    .modal-dialog .modal-body>.alert {
        padding: 5px 8px;
    }
    .modal-dialog .modal-body table {
        margin: 0;
        width: 100%;
        font-weight: normal;
        font-family: sans-serif;
        table-layout: fixed;
        border-collapse: collapse;
    }
    .modal-dialog .modal-body table .choose {
        width: 35px;
        text-align: center;
        vertical-align: top;
    }
    .modal-dialog .modal-body .form-check-input {
        cursor: pointer;
    }
    .modal-dialog .modal-body .form-check-input:checked {
        border-color: #3f78e0;
        background-color: #3f78e0;
    }
    .modal-dialog .modal-body table .no {
        width: 3%;
        text-align: center;
        vertical-align: top;
    }
    .modal-dialog .modal-body table .mail {
        width: 25%;
    }
    .modal-dialog .modal-body table .name {
        width: auto;
    }
    .modal-dialog .modal-body table .organize {
        width: auto;
    }
    .modal-dialog .modal-body table tr td {
        font-size: 14px;
        padding: 4px 3px 2px 3px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .modal-dialog .modal-footer {
        margin: 0 0 0 0;
        height: 90px;
        min-height: auto;
        padding: 10px 0;
    }
    .modal-dialog .modal-footer button {
        margin: 0;
    }
    .modal-dialog .modal-footer button>i {
        float: left;
        font-size: 24px;
        line-height: 24px;
        margin-right: 3px;
    }
    .modal-dialog .state-loading {
        display: inline-block;
        position: relative;
        width: 128px;
        height: 128px;
        padding-left: 1px;
    }
    .modal-dialog .state-loading div {
        display: inline-block;
        position: absolute;
        top:32px;
        left: 2px;
        width: 18px;
        height: 64px;
        background: #3f78e0;
        animation: dialog-state-loading 1.2s cubic-bezier(0, 0.5, 0.5, 1) infinite;
    }
    .modal-dialog .state-loading div:nth-child(1) {
        top: 32px;
        left: 2px;
        height: 64px;
        animation-delay: -0.60s;
    }
    .modal-dialog .state-loading div:nth-child(2) {
        top: 32px;
        left: 23px;
        height: 64px;
        animation-delay: -0.48s;
    }
    .modal-dialog .state-loading div:nth-child(3) {
        top: 32px;
        left: 44px;
        height: 64px;
        animation-delay: -0.36s;
    }
    .modal-dialog .state-loading div:nth-child(4) {
        top: 32px;
        left: 65px;
        height: 64px;
        animation-delay: -0.24s;
    }
    .modal-dialog .state-loading div:nth-child(5) {
        top: 32px;
        left: 86px;
        height: 64px;
        animation-delay: -0.12s;
    }
    .modal-dialog .state-loading div:nth-child(6) {
        top: 0px;
        left: 107px;
        height: 128px;
        animation-delay: 0;
    }
    @keyframes dialog-state-loading {
        0% {
            top: 0px;
            height: 128px;
        }
        50%, 100% {
            top: 32px;
            height: 64px;
        }
    }
</style>
<div class="modal-dialog modal-xl">
    <div id="employee-top" class="modal-content modal-manage">
        <form name="CheckLists" action="<?=$form?>/scripts/lists/employee/select.php" method="POST" enctype="multipart/form-data" class="form-manage" target="_blank">
            <input type="hidden" name="events_id" value="<?=((isset($_POST['events_id'])&&$_POST['events_id'])?$_POST['events_id']:null)?>"/>
            <?=( isset($organizeinputs) ? $organizeinputs : null )?>
            <input type="hidden" name="form_as" value="<?=$form?>"/>
            <div class="modal-header">
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                <h2 class="mb-0 text-white text-start on-text-oneline"><i class="uil uil-plus-circle" style="float:left;font-size:45px;line-height:42px;margin-right:3px;"></i> Employee</h2>
            </div>
            <div class="modal-body">
                <div class="alert alert-info alert-icon mb-2" style="padding:5px 8px 1px 8px;">
                    <div class="row gx-1">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            <div class="form-floating form-select-wrapper mb-1">
                                <select id="organize_id" name="organize_id" class="form-select" aria-label="...">
                                    <option value="">เลือกหน่วยงาน</option>
                                    <?=( isset($organizeoptions) ? $organizeoptions : null )?>
                                </select>
                                <label for="organize_id">หน่วยงาน <span class="text-red">*</span></label>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            <div class="form-floating form-select-wrapper mb-1">
                                <select id="status" name="status" class="form-select" aria-label="...">
                                    <option value="workstatus">ทำงานปกติ</option>
                                    <option value="outofstatus">พ้นสภาพแล้ว</option>
                                </select>
                                <label for="status">สถานภาพ <span class="text-red">*</span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input id="checkall" class="form-check-input" type="checkbox" name="checkall" value="Y" onchange="record_events('checkall', { 'self':this });">
                        <label class="form-check-label text-dark" for="checkall"> เลือก<font class="underline-3 style-3 text-blue blue">ทั้งหมด</font></label>
                    </div>
                </div>
            </div>
            <input type="submit" style="display:none;">
        </form>
        <div class="form-lists form-manage">
            <input type="hidden" name="events_id" value="<?=((isset($_POST['events_id'])&&$_POST['events_id'])?$_POST['events_id']:null)?>"/>
            <div class="modal-body" style="margin-top:0;"></div>
            <div class="modal-footer text-center">
                <button type="button" class="btn btn-lg btn-blue rounded-pill" onclick="record_events('start');"><i class="uil uil-plus-circle"></i>เพิ่มรายชื่อที่เลือก</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function record_events(action, params) {
        if(action=='check'){
            var checked=0;
            var checkboxes = document.getElementsByName('lists[]');
            for (var i = 0; i < checkboxes.length; i++) {
                if(checkboxes[i].checked==true) {
                    checked++;
                }
            }
            if( checkboxes.length==checked ){
                document.CheckLists.checkall.checked = true;
            }else if(checked>0){
                document.CheckLists.checkall.checked = false;
            }
        }else if(action=='checkall'){
            var checkboxes = document.getElementsByName('lists[]');
            if(params.self.checked) {
                for(var i = 0; i < checkboxes.length; i++) {
                    if(checkboxes[i].type == 'checkbox') {
                        checkboxes[i].checked = true;
                    }
                }
            }else{
                for(var i = 0; i < checkboxes.length; i++) {
                    if(checkboxes[i].type == 'checkbox') {
                        checkboxes[i].checked = false;
                    }
                }
            }
        }else if(action=='start'){
            $("#ManageDialog").scrollTo("#employee-top");
            //$(".form-lists>.modal-body>form:first-child").find('input[type="submit"]').click();
        }
    }
    $(document).ready(function() {
        $("form[name='CheckLists'] select[name='organize_id']").change(function(){
            $("form[name='CheckLists'] input[type='submit']").click();
        });
        $("form[name='CheckLists'] select[name='status']").change(function(){
            $("form[name='CheckLists'] input[type='submit']").click();
        });
        $("form[name='CheckLists']").ajaxForm({
            beforeSubmit: function (formData, jqForm, options) {
                $(".form-lists>.modal-body").html('<div style="min-height:135px;margin-top:40px;"><center><div class="state-loading"><div></div><div></div><div></div><div></div><div></div><div></div></div></center></div>');
            },
            success: function(rs) {
                var data = JSON.parse(rs);
                if(data.status=='success'){
                    $(".form-lists>.modal-body").html(data.htmls);
                    $(".form-lists>.modal-body form[name='saving']").ajaxForm({
                        beforeSubmit: function (formData, jqForm, options) {

                        },
                        success: function(rs) {
                            var data = JSON.parse(rs);
                            if( data.login!=undefined&&data.login ){
                                runLogin();
                            }else{
                                if(data.status=='success'){
                                    if( data.removeas!=undefined&&data.removeas ){
                                        //$("form."+data.removeas+" .input-group-submit").html('<button type="submit" class="btn btn-primary" style="margin:0;"><i class="glyphicon glyphicon-floppy-saved"></i> SAVE</button>');
                                        $(".form-lists>.modal-body>form."+data.removeas).hide(function(){
                                            $(this).remove();
                                            if( $(".form-lists>.modal-body>form:first-child").find('input[type="submit"]').length>0 ){
                                                $(".form-lists>.modal-body>form:first-child").find('input[type="submit"]').click();
                                            }else{
                                                $(".form-lists>.modal-footer").html('<button type="button" class="btn btn-lg btn-green rounded-pill"><i class="uil uil-check-circle"></i>บันทึกรายชื่อที่เลือกเรียบร้อยแล้ว</button>');
                                                $(".form-lists>.modal-footer>button").fadeOut(function(){
                                                    $("#ManageDialog").modal('hide');
                                                    $("form[name='filter'] input[name='state']").val(null);
                                                    $("form[name='filter'] button[type='submit']").click();
                                                });
                                            }
                                        });
                                    }else{
                                        console.log("Done");
                                    }
                                }else{
                                    console.log("Error");
                                }
                            }
                        }
                    });
                }else{
                    $(".form-lists .modal-body").html('<div class="alert alert-danger alert-icon mb-2">'+data.text+'</div>');
                }
            }
        });
    });
</script>