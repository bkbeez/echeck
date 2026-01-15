<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    $form = ( (isset($_POST['form_as'])&&$_POST['form_as']) ? $_POST['form_as'] : null );
    // Years
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.edu.cmu.ac.th/v1/student/descyears');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, EDU_API_USER.":".EDU_API_PASS);
    $result1 = curl_exec($ch);
    curl_close($ch);
    $checkyears = json_decode($result1, true);
    if( isset($checkyears)&&count($checkyears)>0 ){
        $yearoptions = '';
        foreach($checkyears as $item){
            $yearoptions .= '<option value="'.$item['year'].'">'.$item['year'].'</option>';
        }
    }
    // Majors
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.edu.cmu.ac.th/v1/student/majors');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, EDU_API_USER.":".EDU_API_PASS);
    $result2 = curl_exec($ch);
    curl_close($ch);
    $checkmajors = json_decode($result2, true);
    if( isset($checkmajors)&&count($checkmajors)>0 ){
        $majoroptions = '';
        foreach($checkmajors as $item){
            $majoroptions .= '<option value="'.$item['major'].'">'.$item['major'].'</option>';
        }
    }
?>
<style type="text/css">
    .modal-dialog .modal-body {
        padding-left:35px;
        padding-right:35px;
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
        border-color: #f78b77;
        background-color: #f78b77;
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
    .modal-dialog .modal-footer{
        min-height:60px;
        background-color:#f78b77;
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
    <div class="modal-content modal-manage">
        <form name="CheckForm" action="<?=$form?>/scripts/lists/student/select.php" method="POST" enctype="multipart/form-data" class="form-manage" target="_blank">
            <input type="hidden" name="events_id" value="<?=((isset($_POST['events_id'])&&$_POST['events_id'])?$_POST['events_id']:null)?>"/>
            <input type="hidden" name="form_as" value="<?=$form?>"/>
            <div class="modal-header" style="min-height:105px;background:#f78b77;">
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                <h2 class="mb-0 text-white text-start on-text-oneline"><i class="uil uil-plus-circle" style="float:left;font-size:45px;line-height:42px;margin-right:3px;"></i> Student</h2>
            </div>
            <div class="modal-body" style="margin-top:-30px;">
                <?php //Helper::debug($lists); ?>
                <div class="alert alert-warning alert-icon mb-0" style="padding:5px 8px 1px 8px;">
                    <div class="row gx-1">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-4">
                            <div class="form-floating form-select-wrapper mb-1">
                                <select id="year" name="year" class="form-select" aria-label="...">
                                    <?=( isset($yearoptions) ? $yearoptions : null )?>
                                </select>
                                <label for="year">รหัสปี <span class="text-red">*</span></label>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-4">
                            <div class="form-floating form-select-wrapper mb-1">
                                <select id="major" name="major" class="form-select" aria-label="...">
                                    <option value="">เลือกสาขาวิชา</option>
                                    <?=( isset($majoroptions) ? $majoroptions : null )?>
                                </select>
                                <label for="major">สาขาวิชา <span class="text-red">*</span></label>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-4">
                            <div class="form-floating form-select-wrapper mb-1">
                                <select id="status" name="status" class="form-select" aria-label="...">
                                    <option value="normstatus">สถานภาพปกติ</option>
                                    <option value="gradstatus">สำเร็จการศึกษา</option>
                                    <option value="outofstatus">พ้นสถานภาพแล้ว</option>
                                </select>
                                <label for="status">สถานะ <span class="text-red">*</span></label>
                            </div>
                        </div>
                    </div>
                    <div class="on-check-all" style="min-height:48px;padding-top:5px;display:none;">
                        <div style="right:0;float:right;position:absolute;margin:-5px 8px 0 0;">
                            <button type="button" class="btn btn-sm btn-blue" onclick="record_events('start');" style="margin:0;padding-left:28px;padding-right:5px;"><i class="uil uil-plus-circle" style="float:left;font-size:20px;line-height:10px;margin-left:-15px;position:absolute;"></i>เพิ่มรายชื่อ&nbsp;<span class="badge bg-pale-ash text-dark rounded-pill" style="top:1px;">0</span></button>
                        </div>
                        <div class="form-check" style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">
                            <input id="checkall-top" class="form-check-input" type="checkbox" name="checkTop" value="Y" onchange="record_events('checkall', { 'self':this });">
                            <label class="form-check-label text-dark" for="checkall-top"> <font class="underline-3 style-3 text-blue blue">ทั้งหมด</font></sup></label>
                        </div>
                    </div>
                </div>
            </div>
            <input type="submit" style="display:none;">
        </form>
        <div class="modal-body checklists" style="padding-top:5px;">
            <div class="alert alert-light alert-icon text-center text-red mb-0" style="min-height:50px;padding:8px 8px 1px 8px;">โปรดเลือกหน่วยงานก่อน !!!</div>
        </div>
        <div class="modal-body finished" style="margin-bottom:-30px;">
            <div class="alert alert-warning alert-icon mb-0 on-check-all" style="min-height:55px;padding:12px 8px 1px 8px;display:none;">
                <div style="right:0;float:right;position:absolute;margin:-5px 8px 0 0;">
                    <button type="button" class="btn btn-sm btn-blue" onclick="record_events('start');" style="margin:0;padding-left:28px;padding-right:5px;"><i class="uil uil-plus-circle" style="float:left;font-size:20px;line-height:10px;margin-left:-15px;position:absolute;"></i>เพิ่มรายชื่อ&nbsp;<span class="badge bg-pale-ash text-dark rounded-pill" style="top:1px;">0</span></button>
                </div>
                <div class="form-manage">
                    <div class="form-check">
                        <input id="checkall-bottom" class="form-check-input" type="checkbox" name="checkBottom" value="Y" onchange="record_events('checkall', { 'self':this });">
                        <label class="form-check-label text-dark" for="checkall-bottom"> <font class="underline-3 style-3 text-blue blue">ทั้งหมด</font></sup></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer text-center"></div>
    </div>
</div>
<script type="text/javascript">
    function record_events(action, params) {
        if(action=='check'){
            var checked = 0;
            var checkboxes = document.getElementsByName('lists[]');
            for (var i = 0; i < checkboxes.length; i++) {
                if(checkboxes[i].checked==true) {
                    checked++;
                }
            }
            document.getElementById('checkall-top').checked = false;
            document.getElementById('checkall-bottom').checked = false;
            $(".modal-dialog .modal-body button[type='button']>.badge").html(checked);
            if( checkboxes.length==checked ){
                document.getElementById('checkall-top').checked = true;
                document.getElementById('checkall-bottom').checked = true;
            }
            if(params.self.checked){
                $(".modal-dialog .modal-body.checklists>.AT-"+params.at).attr('checked',true);
            }else{
                $(".modal-dialog .modal-body.checklists>.AT-"+params.at).removeAttr('checked');
            }
        }else if(action=='checkall'){
            if(params.self.name=='checkBottom'){
                document.getElementById('checkall-top').checked = params.self.checked;
            }else{
                document.getElementById('checkall-bottom').checked = params.self.checked;
            }
            var checkboxes = document.getElementsByName('lists[]');
            if(params.self.checked){
                for(var i = 0; i < checkboxes.length; i++) {
                    if(checkboxes[i].type == 'checkbox') {
                        checkboxes[i].checked = true;
                    }
                }
                $(".modal-dialog .modal-body.checklists>form").attr('checked',true);
                $(".modal-dialog .modal-body button[type='button']>.badge").html(checkboxes.length);
            }else{
                for(var i = 0; i < checkboxes.length; i++) {
                    if(checkboxes[i].type == 'checkbox') {
                        checkboxes[i].checked = false;
                    }
                }
                $(".modal-dialog .modal-body.checklists>form").removeAttr('checked');
                $(".modal-dialog .modal-body button[type='button']>.badge").html('0');
            }
        }else if(action=='start'){
            $(".modal-dialog .modal-body.checklists>form .on-status").html('');
            if( $(".modal-dialog .modal-body.checklists>form[checked]")!=undefined ){
                var id = $(".modal-dialog .modal-body.checklists>form[checked]").attr('id');
                $("#ManageDialog").scrollTo("#"+id);
                $(".modal-dialog .modal-body.checklists>form.AT-"+id).find('input[type="submit"]').click();
            }
        }
    }
    $(document).ready(function() {
        $("form[name='CheckForm'] select[name='year'], form[name='CheckForm'] select[name='major'], form[name='CheckForm'] select[name='status']").change(function(){
            $("form[name='CheckForm'] input[type='submit']").click();
        });
        $("form[name='CheckForm']").ajaxForm({
            beforeSubmit: function (formData, jqForm, options) {
                document.getElementById('checkall-top').checked = false;
                document.getElementById('checkall-top').disabled = true;
                document.getElementById('checkall-bottom').checked = false;
                document.getElementById('checkall-bottom').disabled = true;
                $(".modal-dialog .modal-body .on-check-all").hide();
                $(".modal-dialog .modal-body.checklists").html('<div style="min-height:135px;margin-top:40px;"><center><div class="state-loading"><div></div><div></div><div></div><div></div><div></div><div></div></div></center></div>');
            },
            success: function(rs) {
                var data = JSON.parse(rs);
                if(data.status=='success'){
                    $(".modal-dialog .modal-body.checklists").html(data.htmls);
                    document.getElementById('checkall-top').disabled = false;
                    document.getElementById('checkall-bottom').disabled = false;
                    $(".modal-dialog .modal-body .on-check-all").show();
                    $(".modal-dialog .modal-body.checklists form[name='saving']").ajaxForm({
                        beforeSubmit: function (formData, jqForm, options) {
                            $(".modal-dialog .modal-body.checklists>.AT-"+formData[0].value+" .on-status").html('');
                        },
                        success: function(rs) {
                            var data = JSON.parse(rs);
                            if( data.login!=undefined&&data.login ){
                                runLogin();
                            }else{
                                if( data.at!=undefined&&data.at ){
                                    if(data.status=='success'){
                                        $(".modal-dialog .modal-body.checklists>form."+data.at).remove();
                                    }else{
                                        document.getElementById(data.at).checked = false;
                                        document.getElementById('checkall-top').checked = false;
                                        document.getElementById('checkall-bottom').checked = false;
                                        $(".modal-dialog .modal-body.checklists>form."+data.at+" .on-status").html(data.text);
                                        $(".modal-dialog .modal-body.checklists>form."+data.at).removeAttr('checked');
                                        $(".modal-dialog .modal-body button[type='button']>.badge").html(parseInt($(".modal-dialog .modal-body button[type='button']>.badge").html())-1);
                                    }
                                    if( $(".modal-dialog .modal-body.checklists>form[checked]").length>0 ){
                                        var id = $(".modal-dialog .modal-body.checklists>form[checked]").attr('id');
                                        $("#ManageDialog").scrollTo("#"+id);
                                        $(".modal-dialog .modal-body.checklists>form.AT-"+id).find('input[type="submit"]').click();
                                    }else{
                                        if( $(".modal-dialog .modal-body.checklists>form").length>0 ){
                                            $(".modal-dialog .modal-body button[type='button']>.badge").html('0');
                                            $("form[name='filter'] input[name='state']").val(null);
                                            $("form[name='filter'] button[type='submit']").click();
                                        }else{
                                            $(".modal-dialog .modal-body .on-check-all").hide();
                                            $(".modal-dialog .modal-body.finished").html('<div class="alert alert-success alert-icon text-center mb-0" style="min-height:55px;padding:12px 35px 8px 35px;"><span class="uil uil-check-circle" style="display:inline-block;font-size:32px;line-height:30px;margin-left:-36px;position:fixed;"></span> บันทึกรายชื่อเรียบร้อยแล้ว</div>');
                                            $(".modal-dialog").fadeOut(1500, function(){
                                                $("#ManageDialog").modal('hide');
                                                $("form[name='filter'] input[name='state']").val(null);
                                                $("form[name='filter'] button[type='submit']").click();
                                            });
                                        }
                                    }
                                }
                            }
                        }
                    });
                }else{
                    $(".modal-dialog .modal-body.checklists").html(data.text);
                }
            }
        });
    });
</script>