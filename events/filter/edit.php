<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/admin/?users'); ?>
<?php
    $form = ( (isset($_POST['form_as'])&&$_POST['form_as']) ? $_POST['form_as'] : null );
    $events_id = ( (isset($_POST['edit'])&&$_GET['edit']) ? $_GET['edit'] : null );
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
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-manage">
        <form name="RecordForm" action="<?=$form?>/scripts/update.php" method="POST" enctype="multipart/form-data" class="form-manage" target="_blank">
            <input type="hidden" name="events_id" value="<?=((isset($data['events_id'])&&$data['events_id'])?$data['events_id']:null)?>">
            <div class="modal-header" style="min-height:100px;background:#eef6f9;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h2 class="mb-0 text-green text-start on-text-oneline"><i class="uil uil-edit-alt fs-32" style="float:left;line-height:30px;margin-right:3px;"></i> แก้ไขข้อมูล</h2>
            </div>
            <div class="modal-body" style="margin-top:-30px;padding-left:35px;padding-right:35px;">
                <div class="alert alert-info alert-icon mb-2" style="padding:5px 15px;">
                    <div class="form-floating form-select-wrapper mb-1">
                        <select id="participant_type" name="participant_type" class="form-select" aria-label="...">
                            <option value="ALL"<?=((!isset($data['participant_type'])||$data['participant_type']=='ALL')?' selected':null)?>>[ALL] ทั่วไป</option>
                            <option value="LIST"<?=((isset($data['participant_type'])&&$data['participant_type']=='LIST')?' selected':null)?>>[LIST] เฉพาะผู้ที่มีรายชื่อ</option>
                        </select>
                        <label for="participant_type">ประเภทผู้เข้าร่วม <span class="text-red">*</span></label>
                    </div>
                    <div class="form-floating mb-1">
                        <textarea id="events_name" name="events_name" class="form-control" placeholder="..." style="height:89px;" required><?=((isset($data['events_name'])&&$data['events_name'])?$data['events_name']:null)?></textarea>
                        <label for="events_name">ชื่อกิจกรรม <span class="text-red">*</span></label>
                    </div>
                </div>
                <div class="alert alert-info alert-icon mb-2" style="padding:5px 15px;">
                    <p class="lead text-dark mb-1 text-start on-text-oneline">เริ่มต้นกิจกรรม</p>
                    <div class="row gx-1">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 mx-auto">
                            <div class="form-floating mb-1">
                                <input id="start_date" name="start_date" type="text" value="<?=((isset($data['start_date'])&&$data['start_date'])?Helper::date($data['start_date']):null)?>" class="form-control" data-provide="datepicker" data-date-language="th-th" pattern="\d{1,2}/\d{1,2}/\d{4}" autocomplete="off" placeholder="..." minlength="10" maxlength="10" onkeyup="this.value=this.value.replace(/[^0-9/:]/g,'');"/>
                                <label for="start_date">วันที่ [dd/mm/yyyy] <span class=text-red>*</span></label>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 mx-auto">
                            <div class="form-floating mb-1">
                                <input id="start_time" name="start_time" value="<?=((isset($data['start_time'])&&$data['start_time'])?$data['start_time']:null)?>" type="text" class="form-control" placeholder="..." minlength="5" maxlength="5" onkeyup="this.value=this.value.replace(/[^0-9:]/g,'');"/>
                                <label for="start_time">เวลา [hh:mm] <span class=text-red>*</span></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info alert-icon mb-2" style="padding:5px 15px;">
                    <p class="lead text-dark mb-1 text-start on-text-oneline">สิ้นสุดกิจกรรม</p>
                    <div class="row gx-1">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 mx-auto">
                            <div class="form-floating mb-1">
                                <input id="end_date" name="end_date" type="text" value="<?=((isset($data['end_date'])&&$data['end_date'])?Helper::date($data['end_date']):null)?>" class="form-control" data-provide="datepicker" data-date-language="th-th" pattern="\d{1,2}/\d{1,2}/\d{4}" autocomplete="off" placeholder="..." minlength="10" maxlength="10" onkeyup="this.value=this.value.replace(/[^0-9/:]/g,'');"/>
                                <label for="end_date">วันที่ [dd/mm/yyyy] <span class=text-red>*</span></label>
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 mx-auto">
                            <div class="form-floating mb-1">
                                <input id="end_time" name="end_time" value="<?=((isset($data['end_time'])&&$data['end_time'])?$data['end_time']:null)?>" type="text" class="form-control" placeholder="..." minlength="5" maxlength="5" onkeyup="this.value=this.value.replace(/[^0-9:]/g,'');"/>
                                <label for="end_time">เวลา [hh:mm] <span class=text-red>*</span></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-center">
                <div class="confirm-box"></div>
                <div class="row gx-1 row-button">
                    <div class="col-lg-6 col-md-6 pt-1">
                        <button type="button" class="btn btn-lg btn-icon btn-icon-start btn-green rounded-pill w-100" onclick="record_events('confirm');"><i class="uil uil-save"></i>บันทึก</button>
                    </div>
                    <div class="col-lg-6 col-md-6 pt-1">
                        <button type="button" class="btn btn-lg btn-icon btn-icon-start btn-outline-danger rounded-pill w-100" data-bs-dismiss="modal"><i class="uil uil-cancel"></i>ละทิ้ง</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
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
</script>