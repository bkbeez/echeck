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
        $organizeoptions = '';
        foreach($organizes as $item){
            $organizeoptions .= '<option value="'.$item['organize_id'].'">'.$item['organize_name'].'</option>';
        }
    }
?>
<style type="text/css">
    .modal-dialog .modal-header {
        min-height: 100px;
        background: #efeff8;
    }
    .modal-dialog .modal-body {
        margin-top: -30px;
        padding-left: 35px;
        padding-right: 35px;
    }
    .modal-dialog .modal-body>.alert {
        padding: 5px 15px;
    }
    .modal-dialog .modal-body table {
        width: 100%;
        margin: 0;
        font-weight: normal;
        font-family: sans-serif;
    }
    .modal-dialog .modal-body table .choose {
        width: 35px;
        text-align: center;
        vertical-align: top;
    }
    .modal-dialog .modal-body .form-check-input,
    .modal-dialog .modal-body table .choose>input {
        margin-top: 0;
        cursor: pointer;
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
    }
    .modal-dialog .modal-footer {
        margin: 0 0 0 0;
        height: 80px;
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
</style>
<div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content modal-manage">
        <form name="CheckLists" action="<?=$form?>/scripts/lists/employee/checklists.php" method="POST" enctype="multipart/form-data" class="form-manage" target="_blank">
            <input type="hidden" name="events_id" value="<?=((isset($_POST['events_id'])&&$_POST['events_id'])?$_POST['events_id']:null)?>"/>
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h2 class="mb-0 text-blue text-start on-text-oneline"><i class="uil uil-user-check" style="float:left;font-size:36px;line-height:36px;margin-right:3px;"></i> เพิ่มข้อมูลจากรายชื่อบุคลากร</h2>
            </div>
            <div class="modal-body">
                <div class="alert alert-primary alert-icon mb-2">
                    <div class="form-floating form-select-wrapper mb-1">
                        <select id="organize_id" name="organize_id" class="form-select" aria-label="...">
                            <option value="">เลือกหน่วยงาน</option>
                            <?=( isset($organizeoptions) ? $organizeoptions : null )?>
                        </select>
                        <label for="organize_id">หน่วยงาน <span class="text-red">*</span></label>
                    </div>
                </div>
            </div>
            <input type="submit" style="display:none;">
        </form>
        <form name="RecordForm" action="<?=$form?>/scripts/lists/employee/create.php" method="POST" enctype="multipart/form-data" class="form-manage" target="_blank">
            <input type="hidden" name="events_id" value="<?=((isset($_POST['events_id'])&&$_POST['events_id'])?$_POST['events_id']:null)?>"/>
            <div class="modal-body">&nbsp;</div>
            <div class="modal-footer text-center">
                <button type="submit" class="btn btn-lg btn-blue rounded-pill" disabled><i class="uil uil-check-circle"></i>เพิ่มรายชื่อที่เลือก</button>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    function record_events(action, params) {
        if(action=='type'){
            var checkboxes = document.getElementsByName(inputName);
            if (self.checked) {
                for (var i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].type == 'checkbox') {
                        checkboxes[i].checked = true;
                    }
                }
            } else {
                for (var i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].type == 'checkbox') {
                        checkboxes[i].checked = false;
                    }
                }
            }
        }
    }
    $(document).ready(function() {
        $("form[name='CheckLists']").change(function(){
            $("form[name='CheckLists'] input[type='submit']").click();
        });
        $("form[name='CheckLists']").ajaxForm({
            beforeSubmit: function (formData, jqForm, options) {
                
            },
            success: function(rs) {
                var data = JSON.parse(rs);
                if(data.status=='success'){
                    $("form[name='RecordForm'] .modal-body").html(data.htmls);
                }else{
                    $("form[name='RecordForm'] .modal-body").html('<div class="alert alert-danger alert-icon mb-2">'+data.text+'</div>');
                }
            }
        });
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