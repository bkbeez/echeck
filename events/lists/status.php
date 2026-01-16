<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    $form = ( (isset($_POST['form_as'])&&$_POST['form_as']) ? $_POST['form_as'] : null );
    $header = '<h2 class="mb-0 text-dark text-start on-text-oneline"><i class="uil uil-circle" style="float:left;font-size:45px;line-height:42px;margin-right:3px;"></i> ข้อมูลการลงทะเบียน</h2>';
    $header_bg = 'bg-soft-ash';
    $status = 'ash';
    $status_bg = 'secondary';
    if( (isset($_POST['id'])&&$_POST['id'])&&(isset($_POST['events_id'])&&$_POST['events_id']) ){
        $data = DB::one("SELECT events_lists.*
                        , TRIM(CONCAT(COALESCE(events_lists.prefix,''),events_lists.firstname,' ',COALESCE(events_lists.lastname,''))) AS fullname
                        , CONCAT(DATE_FORMAT(events_lists.date_checkin,'%d/%m/'), (YEAR(events_lists.date_checkin)+543),' เวลา ',DATE_FORMAT(events_lists.date_checkin, '%H:%i')) AS date_checkin_display
                        , CONCAT(DATE_FORMAT(events_lists.date_cancel,'%d/%m/'), (YEAR(events_lists.date_cancel)+543),' เวลา ',DATE_FORMAT(events_lists.date_cancel, '%H:%i')) AS date_cancel_display
                        FROM events_lists
                        WHERE events_lists.id=:id AND events_lists.events_id=:events_id
                        LIMIT 1;"
                        , array('id'=>$_POST['id'], 'events_id'=>$_POST['events_id'])
        );
        if( isset($data['organization'])&&$data['organization'] ){
            $organization = $data['organization'];
            if(isset($data['department'])&&$data['department']){
                $organization .= '<br>&rang; '.$data['department'];
            }
        }
        if( isset($data['date_cancel_display'])&&$data['date_cancel_display'] ){
            $header = '<h2 class="mb-0 text-red text-start on-text-oneline"><i class="uil uil-times-circle" style="float:left;font-size:45px;line-height:42px;margin-right:3px;"></i> ข้อมูลการลงทะเบียน</h2>';
            $header_bg = 'bg-soft-red';
            $status = 'red';
            $status_bg = 'success';
        }else if( isset($data['date_checkin_display'])&&$data['date_checkin_display'] ){
            $header = '<h2 class="mb-0 text-green text-start on-text-oneline"><i class="uil uil-check-circle" style="float:left;font-size:45px;line-height:42px;margin-right:3px;"></i> ข้อมูลการลงทะเบียน</h2>';
            $header_bg = 'bg-soft-green';
            $status = 'green';
            $status_bg = 'success';
        }
    }
?>
<style type="text/css">
    .modal-dialog .modal-header {
        min-height:100px;
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
        <form name="RecordForm" action="<?=$form?>/scripts/lists/status.php" method="POST" enctype="multipart/form-data" class="form-manage" target="_blank">
            <input type="hidden" name="id" value="<?=((isset($data['id'])&&$data['id'])?$data['id']:null)?>">
            <input type="hidden" name="events_id" value="<?=((isset($data['events_id'])&&$data['events_id'])?$data['events_id']:null)?>">
            <div class="modal-header <?=$header_bg?>">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <?=$header?>
            </div>
            <div class="modal-body">
                <div class="alert alert-<?=$status_bg?> mb-2">
                    <div class="row gx-0 gy-0 row-success">
                        <div class="d-flex flex-row">
                            <div><div class="icon text-<?=$status?> me-2 mt-n3" style="font-size:52px;line-height:75px;"><i class="uil uil-user-square"></i></div></div>
                            <div>
                                <h5 class="mb-0 text-<?=$status?> on-font-primary">ชื่อ-สกุล</h5>
                                <p class="on-text-normal text-dark" style="margin-top:-6px;"><?=( (isset($data['fullname'])&&$data['fullname']) ? $data['fullname'] : '-' )?></p>
                            </div>
                        </div>
                        <div class="d-flex flex-row">
                            <div><div class="icon text-<?=$status?> me-2 mt-n3" style="font-size:52px;line-height:75px;"><i class="uil uil-envelopes"></i></div></div>
                            <div>
                                <h5 class="mb-0 text-<?=$status?> on-font-primary">อีเมล</h5>
                                <p class="on-text-normal text-dark" style="margin-top:-6px;"><?=( (isset($data['email'])&&$data['email']) ? $data['email'] : '-' )?></p>
                            </div>
                        </div>
                        <?php if( isset($organization)&&$organization ){ ?>
                        <div class="d-flex flex-row">
                            <div><div class="icon text-<?=$status?> me-2 mt-n3" style="font-size:52px;line-height:75px;"><i class="uil uil-university"></i></div></div>
                            <div>
                                <h5 class="mb-0 text-<?=$status?> on-font-primary">สังกัด</h5>
                                <p class="on-text-normal text-dark" style="margin-top:-2px;line-height:18px;"><?=$organization?></p>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="d-flex flex-row on-success">
                            <div><div class="icon text-<?=$status?> me-2 mt-n3" style="font-size:52px;line-height:75px;"><i class="uil uil-calendar-alt"></i></div></div>
                            <div>
                                <h5 class="mb-0 text-<?=$status?> on-font-primary">วันที่เข้าร่วม</h5>
                                <p class="on-text-normal text-dark m-0" style="margin-top:-2px;line-height:18px;">&rang;<?=( (isset($data['date_checkin_display'])&&$data['date_checkin_display']) ? $data['date_checkin_display'] : '-' )?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if( $status=='green' ){ ?>
            <div class="modal-footer text-center">
                <div class="confirm-box"></div>
                <div class="row-button text-center">
                    <button type="button" class="btn btn-lg btn-danger rounded-pill w-100" onclick="record_events('reset');"><i class="uil uil-cancel"></i>รีเซตการลงทะเบียน</button>
                </div>
            </div>
            <?php } ?>
        </form>
    </div>
</div>
<script type="text/javascript">
    function record_events(action, params){
        $("form[name='RecordForm'] label>span>font").remove();
        if(action=="reset"){
            if( params!=undefined ){
                $("form[name='RecordForm'] .confirm-box").html('').css('margin-top','0');
                $("form[name='RecordForm'] .row-button").show();
            }else{
                var htmls  = '<div class="fs-19 mb-2 text-center on-text-normal">ยืนยัน<b class=text-red>รีเซตการลงทะเบียน</b> ใช่ หรือ ไม่ ?</div>';                    
                    htmls += '<button type="submit" class="btn btn-lg btn-icon btn-icon-start btn-success rounded-pill"><i class="uil uil-check-circle"></i>ใช่</button>';
                    htmls += '&nbsp;';
                    htmls += '<button type="button" class="btn btn-lg btn-icon btn-icon-start btn-outline-danger rounded-pill" onclick="record_events(\'reset\', { \'on\':\'N\' });"><i class="uil uil-times-circle"></i>ไม่</button>';
                    htmls += '<input type="hidden" name="reset" value="Y">';
                $("form[name='RecordForm'] .confirm-box").html(htmls).css('margin-top','-15px');
                $("form[name='RecordForm'] .row-button").hide();
            }
        }else if(action=="cancel"){
            if( params!=undefined ){
                $("form[name='RecordForm'] .confirm-box").html('').css('margin-top','0');
                $("form[name='RecordForm'] .row-button").show();
            }else{
                var htmls  = '<div class="fs-19 mb-2 text-center on-text-normal">ยืนยัน<b class=text-red>ยกเลิกการลงทะเบียน</b> ใช่ หรือ ไม่ ?</div>';                    
                    htmls += '<button type="submit" class="btn btn-lg btn-icon btn-icon-start btn-success rounded-pill"><i class="uil uil-check-circle"></i>ใช่</button>';
                    htmls += '&nbsp;';
                    htmls += '<button type="button" class="btn btn-lg btn-icon btn-icon-start btn-outline-danger rounded-pill" onclick="record_events(\'cancel\', { \'on\':\'N\' });"><i class="uil uil-times-circle"></i>ไม่</button>';
                    htmls += '<input type="hidden" name="cancel" value="Y">';
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
                        $("form[name='filter'] table tbody ."+data.id+" .status, form[name='filter'] table tbody ."+data.id+" .status-o").html(data.htmls);
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