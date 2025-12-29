<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/admin/?users'); ?>
<?php
    $htmls = '';
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
        $sharedlists = DB::sql("SELECT events_shared.*
                                , TRIM(CONCAT(COALESCE(member.title,''),member.name,' ',COALESCE(member.surname,''))) AS fullname
                                , COALESCE(member.picture, member.picture_default) AS picture
                                FROM events_shared
                                LEFT JOIN member ON events_shared.email=member.email OR events_shared.email=member.email_cmu
                                WHERE events_shared.events_id=:events_id
                                ORDER BY events_shared.date_create;"
                                , array('events_id'=>$_POST['events_id'])
        );
        if( isset($sharedlists)&&count($sharedlists)>0 ){
            foreach($sharedlists as $list){
                $htmls .= '<div class="card card-'.md5($list['email']).' mb-1">';
                    $htmls .= '<div class="card-body text-dark">';
                        $htmls .= '<div class="delete">';
                            $htmls .= '<div class="delete-box">';
                                $htmls .= 'ยืนยันยกเลิกแชร์<br>';
                                $htmls .= '<span class="btn btn-success btn-sm" onclick="record_events(\'unshare\', { \'self\':this, \'on\':\'Y\', \'events_id\':\''.$list['events_id'].'\', \'email\':\''.$list['email'].'\' });">ใช่</span>';
                                $htmls .= '<span class="btn btn-outline-danger btn-sm" onclick="record_events(\'unshare\', { \'self\':this, \'on\':\'N\' });">ไม่</span>';
                            $htmls .= '</div>';
                            $htmls .= '<button type="button" class="btn btn-outline-danger" onclick="record_events(\'unshare\', { \'self\':this });"><spam class="uil uil-user-minus"></spam></button>';
                        $htmls .= '</div>';
                        $htmls .= '<div class="picture">';
                            $htmls .= '<img src="'.( (isset($list['picture'])&&$list['picture']) ? $list['picture'] : THEME_IMG.'/avatar.png' ).'" onerror="this.onerror=null;this.src=\''.THEME_IMG.'/avatar.png\';"/>';
                        $htmls .= '</div>';
                        $htmls .= '<div class="info">';
                            $htmls .= '<font class="name">'.( (isset($list['fullname'])&&$list['fullname']) ? $list['fullname'] : '<span class="text-ash on-text-i">Unknown... .. .</span>' ).'</font>';
                            $htmls .= '<br><span class="uil uil-envelopes"></span> '.$list['email'];
                        $htmls .= '</div>';
                    $htmls .= '</div>';
                $htmls .= '</div>';
            }
        }
    }
?>
<style type="text/css">
    .modal-dialog .modal-header {
        min-height:100px;
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
    .alert-shared .share-lists .card {
        height: 42px;
        line-height: 42px;
        overflow: hidden;
    }
    .alert-shared .share-lists .card .card-body {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        padding: 0px 50px 0px 6px !important;
    }
    .alert-shared .share-lists .card .card-body .info {
        font-size: 12px;
        line-height: 14px;
        padding-top: 7px;
        font-weight: normal;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .alert-shared .share-lists .card .card-body .info>font {
        font-size: 13px;
    }
    .alert-shared .share-lists .card .card-body .picture {
        float: left;
        width: 31px;
        height: 31px;
        margin: 6px 5px 0 0;
        overflow: hidden;
        border-radius: 0.4rem;
        -moz-border-radius: 0.4rem;
        -webkit-border-radius: 0.4rem;
        border:1px solid #e8ecf2;
    }
    .alert-shared .share-lists .card .card-body .picture>img {
        width:100%;
        margin-top: -20px;
    }
    .alert-shared .share-lists .card .card-body .delete {
        right: 0;
        float: right;
        position: absolute;
    }
    .alert-shared .share-lists .card .card-body .delete>.btn {
        border-width: 1px;
        margin: -4px 6px 0 0;
        padding: 2px 7px 0 7px;
    }
    .alert-shared .share-lists .card .card-body .delete .delete-box {
        color: red;
        font-size: 12px;
        line-height: 16px;
        padding: 4px 7px 0 0;
        display: none;
        text-align: right;
        font-weight: normal;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .alert-shared .share-lists .card .card-body .delete .delete-box>.btn {
        font-size: 10px;
        cursor: pointer;
        padding: 0 6px;
        margin-right: 4px;
    }
    .modal-dialog .modal-footer {
        min-height:100px;
        background: #efeff8;
    }
</style>
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-manage">
        <form name="RecordForm" action="<?=$form?>/scripts/share.php" method="POST" enctype="multipart/form-data" class="form-manage" target="_blank">
            <input type="hidden" name="events_id" value="<?=((isset($data['events_id'])&&$data['events_id'])?$data['events_id']:null)?>">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h2 class="mb-0 text-dark text-start on-text-oneline"><i class="uil uil-users-alt" style="float:left;font-size:36px;line-height:36px;margin-right:3px;"></i> ผู้ใช้ที่ได้รับแชร์</h2>
            </div>
            <div class="modal-body">
                <div class="alert alert-primary mb-2 share-lists"><?=$htmls?></div>
            </div>
            <div class="modal-footer">
                <div class="alert alert-primary alert-shared" role="alert">
                    <div class="row shared-adding gx-1">
                        <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 mx-auto">
                            <div class="form-floating mb-1">
                                <input id="email" name="email" value="" type="email" class="form-control" placeholder="email...">
                                <label for="email">อีเมลผู้รับแชร์ <span class=text-red>*</span></label>
                            </div>
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 mx-auto">
                            <button type="submit" class="btn btn-soft-dark w-100" disabled><i class="uil uil-user-plus" style="float:left;font-size:24px;line-height:24px;margin-right:3px;"></i>แชร์</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    function record_events(action, params){
        $("form[name='RecordForm'] label>span>font").remove();
        if(action=='share'){
            if( params.self.value ){
                $("form[name='RecordForm'] .shared-adding button").attr('class','btn btn-primary w-100').removeAttr('disabled');
            }else{
                $("form[name='RecordForm'] .shared-adding button").attr({'class':'btn btn-soft-dark w-100', 'disabled':true});
            }
        }else if(action=='onshare'){
            if( $("form[name='RecordForm'] .alert-shared input[name='shared']").val() ){
                $.ajax({
                    url : "<?=$form?>/scripts/share.php",
                    type: 'POST',
                    data:{'events_id':$("form[name='RecordForm'] input[name='events_id']").val(), 'email':$("form[name='RecordForm'] input[name='shared']").val() },
                    dataType: "json",
                    beforeSend: function( xhr ) {
                        $("form[name='RecordForm'] .alert-shared label>span>font").remove();
                    }
                }).done(function(data) {
                    if(data.status=='success'){
                        $("form[name='RecordForm'] .alert-shared .share-lists").append(data.htmls);
                        $("form[name='RecordForm'] .alert-shared input[name='shared']").val(null);
                        $("form[name='RecordForm'] .shared-adding button").attr({'class':'btn btn-soft-dark w-100', 'disabled':true});
                    }else{
                        if( data.onfocus!=undefined&&data.onfocus ){
                            $("form[name='RecordForm'] .alert-shared input[name='"+data.onfocus+"']").focus();
                            $("form[name='RecordForm'] .alert-shared label[for='"+data.onfocus+"']>span").html('<font class="fs-12 on-text-normal-i text-red">'+data.text+'</font>');
                        }
                    }
                });
            }
        }else if(action=='unshare'){
            if( params.on!=undefined ){
                if( params.on=='N' ){
                    $(params.self).parent().parent().parent().parent().find('.on-status').remove();
                    $(params.self).parent().parent().parent().parent().find('.info>.name').show();
                    $(params.self).parent().parent().find('.delete-box').hide();
                    $(params.self).parent().parent().find('button').fadeIn();
                }else{
                    $.ajax({
                        url : "<?=$form?>/scripts/unshare.php",
                        type: 'POST',
                        data:{'events_id':params.events_id, 'email':params.email },
                        dataType: "json",
                        beforeSend: function( xhr ) {
                            $(params.self).parent().parent().parent().parent().find('.info>.name').hide();
                            $(params.self).parent().parent().parent().parent().find('.on-status').remove();
                            $(params.self).parent().parent().parent().parent().find('.info>.name').before('<font class="on-status"><img src="<?=THEME_IMG?>/small-loading.gif" style="float:left;position:absolute;"/><em class="text-orange" style="padding-left:20px;">โปรดรอสักครู่... .. .</em></font>');
                        }
                    }).done(function(data) {
                        if(data.status=='success'){
                            $(params.self).parent().parent().parent().parent().find('.on-status').html('<span class="on-status text-green">'+data.text+'</span>');
                            $(params.self).parent().parent().parent().parent().fadeOut('slow', function(){
                                $(this).remove();
                                if( $("form[name='RecordForm'] .alert-shared .share-lists>.card").length<=0 ){
                                    $("form[name='filter'] table tbody ."+data.events_id+" .badge-shared").remove();
                                }
                            });
                        }else{
                            $(params.self).parent().parent().parent().parent().find('.on-status').html('<span class="on-status text-red">'+data.text+'</span>');
                        }
                    });
                }
            }else{
                $(params.self).parent().find('button').hide();
                $(params.self).parent().find('.delete-box').fadeIn();
            }
        }
    }
    $(document).ready(function() {
        $("form[name='RecordForm'] input[name='shared']").change(function(){
            if( this.value ){
                $("form[name='RecordForm'] .shared-adding button").attr('class','btn btn-primary w-100').removeAttr('disabled');
            }else{
                $("form[name='RecordForm'] .shared-adding button").attr({'class':'btn btn-soft-dark w-100', 'disabled':true});
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
                    $("form[name='RecordForm'] .modal-footer").fadeOut(1500, function(){
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