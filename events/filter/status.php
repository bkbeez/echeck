<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    $form = ( (isset($_POST['form_as'])&&$_POST['form_as']) ? $_POST['form_as'] : null );
    if( isset($_POST['events_id'])&&$_POST['events_id'] ){
        $data = DB::one("SELECT events.*
                        , DATE_FORMAT(events.start_date, '%H:%i') AS start_time
                        , DATE_FORMAT(events.end_date, '%H:%i') AS end_time
                        FROM events
                        WHERE events.events_id=:events_id
                        LIMIT 1;"
                        , array('events_id'=>$_POST['events_id'])
        );
    }
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
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-manage">
        <form name="RecordForm" action="<?=$form?>/scripts/status.php" method="POST" enctype="multipart/form-data" class="form-manage" target="_blank">
            <input type="hidden" name="events_id" value="<?=((isset($data['events_id'])&&$data['events_id'])?$data['events_id']:null)?>">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h2 class="mb-0 text-dark text-start on-text-oneline"><i class="uil uil-circle" style="float:left;font-size:36px;line-height:36px;margin-right:3px;"></i> สถานะกิจกรรม</h2>
            </div>
            <div class="modal-body">
                <div class="alert alert-success mb-2">
                    <div class="row gx-1">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 mx-auto">
                            <div class="form-check mb-2">
                                <input id="status_0" class="form-check-input" type="radio" name="status" value="0"<?=((isset($data['status'])&&$data['status']<=0)?' checked':null)?>>
                                <label for="status_0" class="form-check-label form-payslip-select text-dark"><span class="underline-3 style-3 text-dark dark">DRAFT</span> ร่าง</label>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 mx-auto">
                            <div class="form-check mb-2">
                                <input id="status_1" class="form-check-input" type="radio" name="status" value="1"<?=((isset($data['status'])&&$data['status']==1)?' checked':null)?>>
                                <label for="status_1" class="form-check-label form-payslip-select text-dark"><span class="underline-3 style-3 text-green green">OPEN</span> เปิดลงทะเบียน</label>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 mx-auto">
                            <div class="form-check mb-2">
                                <input id="status_2" class="form-check-input" type="radio" name="status" value="2"<?=((isset($data['status'])&&$data['status']==2)?' checked':null)?>>
                                <label for="status_2" class="form-check-label form-payslip-select text-dark"><span class="underline-3 style-3 text-red red">CLOSE</span> ปิดลงทะเบียน</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-center">
                <div class="confirm-box"></div>
                <div class="row gx-1 row-button">
                    <div class="col-lg-6 col-md-6 pt-1">
                        <button type="button" class="btn btn-lg btn-green rounded-pill w-100" onclick="record_events('confirm');"><i class="uil uil-save"></i>บันทึก</button>
                    </div>
                    <div class="col-lg-6 col-md-6 pt-1">
                        <button type="button" class="btn btn-lg btn-outline-danger rounded-pill w-100" data-bs-dismiss="modal"><i class="uil uil-cancel"></i>ละทิ้ง</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    function record_events(action, params){
        $("form[name='RecordForm'] label>span>font").remove();
        if(action=="confirm"){
            if( params!=undefined ){
                $("form[name='RecordForm'] .confirm-box").html('').css('margin-top','0');
                $("form[name='RecordForm'] .row-button").show();
            }else{
                var htmls  = '<div class="fs-19 mb-2 text-center on-text-normal">ยืนยันเปลี่ยนสถานะกิจกรรม ใช่ หรือ ไม่ ?</div>';                    
                    htmls += '<button type="submit" class="btn btn-lg btn-icon btn-icon-start btn-success rounded-pill"><i class="uil uil-check-circle"></i>ใช่</button>';
                    htmls += '&nbsp;';
                    htmls += '<button type="button" class="btn btn-lg btn-icon btn-icon-start btn-outline-danger rounded-pill" onclick="record_events(\'confirm\', { \'on\':\'N\' });"><i class="uil uil-times-circle"></i>ไม่</button>';
                $("form[name='RecordForm'] .confirm-box").html(htmls).css('margin-top','-15px');
                $("form[name='RecordForm'] .row-button").hide();
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
                        $("form[name='filter'] table tbody ."+data.events_id+" .badge-status").attr('class',data.badge).html(data.htmls);
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