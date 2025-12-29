<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/admin/?users'); ?>
<?php
    $today = new datetime();
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
        <form name="RecordForm" action="<?=$form?>/scripts/create.php" method="POST" enctype="multipart/form-data" class="form-manage" target="_blank">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h2 class="mb-0 text-blue text-start on-text-oneline"><i class="uil uil-plus" style="float:left;font-size:36px;line-height:36px;margin-right:3px;"></i> กิจกรรมใหม่</h2>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning alert-icon mb-2">
                    <div class="form-floating form-select-wrapper mb-1">
                        <select id="participant_type" name="participant_type" class="form-select" aria-label="...">
                            <option value="ALL" selected>[ALL] ทั่วไป</option>
                            <option value="LIST">[LIST] เฉพาะผู้ที่มีรายชื่อ</option>
                        </select>
                        <label for="participant_type">ประเภทผู้เข้าร่วม <span class="text-red">*</span></label>
                    </div>
                    <div class="form-floating mb-1">
                        <textarea id="events_name" name="events_name" class="form-control" placeholder="..." style="height:89px;" required></textarea>
                        <label for="events_name">ชื่อกิจกรรม <span class="text-red">*</span></label>
                    </div>
                </div>
                <div class="alert alert-warning alert-icon mb-2">
                    <p class="lead text-dark mb-1 text-start on-text-oneline">เริ่มต้นกิจกรรม</p>
                    <div class="row gx-1">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 mx-auto">
                            <div class="form-floating mb-1">
                                <input id="start_date" name="start_date" type="text" value="<?=Helper::date($today)?>" class="form-control" data-provide="datepicker" data-date-language="th-th" pattern="\d{1,2}/\d{1,2}/\d{4}" autocomplete="off" placeholder="..." minlength="10" maxlength="10" onkeyup="this.value=this.value.replace(/[^0-9/:]/g,'');"/>
                                <label for="start_date">วันที่ [dd/mm/yyyy] <span class=text-red>*</span></label>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 mx-auto">
                            <div class="form-floating mb-1">
                                <input id="start_time" name="start_time" value="<?=($today->modify("+1 hours"))->format("H")?>:00" type="text" class="form-control" placeholder="..." minlength="5" maxlength="5" onkeyup="this.value=this.value.replace(/[^0-9:]/g,'');"/>
                                <label for="start_time">เวลา [hh:mm] <span class=text-red>*</span></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-warning alert-icon mb-2">
                    <p class="lead text-dark mb-1 text-start on-text-oneline">สิ้นสุดกิจกรรม</p>
                    <div class="row gx-1">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 mx-auto">
                            <div class="form-floating mb-1">
                                <input id="end_date" name="end_date" type="text" value="<?=Helper::date($today)?>" class="form-control" data-provide="datepicker" data-date-language="th-th" pattern="\d{1,2}/\d{1,2}/\d{4}" autocomplete="off" placeholder="..." minlength="10" maxlength="10" onkeyup="this.value=this.value.replace(/[^0-9/:]/g,'');"/>
                                <label for="end_date">วันที่ [dd/mm/yyyy] <span class=text-red>*</span></label>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 mx-auto">
                            <div class="form-floating mb-1">
                                <input id="end_time" name="end_time" value="<?=($today->modify("+1 hours"))->format("H")?>:00" type="text" class="form-control" placeholder="..." minlength="5" maxlength="5" onkeyup="this.value=this.value.replace(/[^0-9:]/g,'');"/>
                                <label for="end_time">เวลา [hh:mm] <span class=text-red>*</span></label>
                            </div>
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