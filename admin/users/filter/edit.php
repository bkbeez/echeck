<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/admin/?users'); ?>
<?php
    $form = ( (isset($_POST['form_as'])&&$_POST['form_as']) ? $_POST['form_as'] : null );
    if( isset($_POST['id'])&&$_POST['id'] ){
        $data = DB::one("SELECT member.*
                        , member_permission.role AS user_role
                        FROM member
                        LEFT JOIN member_permission ON member.email=member_permission.email
                        WHERE member.id=:id
                        LIMIT 1;"
                        , array('id'=>$_POST['id'])
        );
        if( isset($data['email'])&&$data['email'] ){
            $list1s = DB::sql("SELECT * FROM xlg_member WHERE email=:email ORDER BY date_at DESC LIMIT 5;", array('email'=>$data['email']));
            if( isset($lists)&&count($lists)>0 ){
                foreach($lists as $item){
                    $changeloghtmls = '<div class="col-lg-12 col-md-12">';
                        $changeloghtmls .= '<div class="form-floating mb-1">';
                            $changeloghtmls .= '<div class="form-control on-text-display">'.$item['remark'].'</div>';
                            $changeloghtmls .= '<label>'.Helper::datetimeDisplay($item['date_at'], App::lang()).' - '.$item['title'].'</label>';
                        $changeloghtmls .= '</div>';
                    $changeloghtmls .= '</div>';
                }
            }else{
                $changeloghtmls = '<div class="col-lg-12 col-md-12">';
                    $changeloghtmls .= '<div class="mb-1">';
                        $changeloghtmls .= '<div class="form-control on-text-display text-ash"><em>- '.( (App::lang()=='en') ? 'No edit history' : 'ไม่มีการแก้ไข' ).'... .. .</em></div>';
                    $changeloghtmls .= '</div>';
                $changeloghtmls .= '</div>';
            }
        }
    }
?>
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-manage">
        <form name="RecordForm" action="<?=$form?>/scripts/update.php" method="POST" enctype="multipart/form-data" class="form-manage" target="_blank">
            <input type="hidden" name="id" value="<?=((isset($data['id'])&&$data['id'])?$data['id']:null)?>">
            <input type="hidden" name="email" value="<?=((isset($data['email'])&&$data['email'])?$data['email']:null)?>">
            <div class="modal-header" style="min-height:100px;background:#eef6f9;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h2 class="mb-0 text-start on-text-oneline"><i class="uil uil-edit-alt fs-32"></i> <?=( (App::lang()=='en') ? 'Edit Data' : 'แก้ไขข้อมูล' )?></h2>
            </div>
            <div class="modal-body" style="margin-top:-30px;padding-left:35px;padding-right:35px;">
                <div class="alert alert-info alert-icon mb-2" style="padding:5px 15px;">
                    <div class="row gx-1">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-floating mb-1">
                                <div class="form-control on-text-display"><?=((isset($data['email'])&&$data['email'])?$data['email']:'-')?></div>
                                <label><?=((isset($data['date_create'])&&$data['date_create'])?Helper::datetimeDisplay($data['date_create'], App::lang()):'-')?></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info alert-icon mb-2" style="padding:5px 15px;">
                    <p class="lead text-dark mb-1 text-start on-text-oneline"><?=( (App::lang()=='en') ? 'User Data' : 'ข้อมูลผู้ใช้' )?></p>
                    <div class="form-floating mb-1">
                        <input name="title" value="<?=((isset($data['title'])&&$data['title'])?$data['title']:null)?>" type="text" class="form-control" placeholder="<?=Lang::get('NameTitle')?>" id="title">
                        <label for="title"><?=Lang::get('NameTitle')?></label>
                    </div>
                    <div class="form-floating mb-1">
                        <input name="name" value="<?=((isset($data['name'])&&$data['name'])?$data['name']:null)?>" type="text" class="form-control" placeholder="<?=Lang::get('NameFirst')?>" id="name" required>
                        <label for="name"><?=Lang::get('NameFirst')?> *<span></span></label>
                    </div>
                    <div class="form-floating mb-1">
                        <input name="surname" value="<?=((isset($data['surname'])&&$data['surname'])?$data['surname']:null)?>" type="text" class="form-control" placeholder="<?=Lang::get('NameLast')?>" id="surname">
                        <label for="surname"><?=Lang::get('NameLast')?></label>
                    </div>
                </div>
                <div class="alert alert-danger alert-icon mb-2" style="padding:5px 15px;">
                    <div class="mb-1">
                        <div class="form-control on-text-display">
                            <div style="right:20px;float:right;position:absolute;margin-top:-2px;"><a href="javascript:void(0);" class="btn btn-soft-primary btn-sm rounded-pill" onclick="record_events('info', { 'self':this });" display="N" style="padding:1px 7px 1px 5px;"><span class="uil uil-plus"></span><font class="on-text-normal"><?=( (App::lang()=='en') ? 'Info' : 'ข้อมูล' )?></font></a></div>
                            <div style="float:left;margin:-15px 5px 0 -10px;"><span class="fs-32 uil uil-shield-check"></span></div>
                            <b class="fs-16"><?=((isset($data['user_role'])&&$data['user_role'])?$data['user_role']:$data['USER'])?></b>
                        </div>
                    </div>
                    <div class="row gx-1 on-info" style="display:none;"><?=(isset($changeloghtmls)?$changeloghtmls:null)?></div>
                </div>
            </div>
            <div class="modal-footer text-center">
                <div class="confirm-box"></div>
                <div class="row gx-1 row-button">
                    <div class="col-lg-6 col-md-6 pt-1">
                        <button type="button" class="btn btn-lg btn-icon btn-icon-start btn-primary rounded-pill w-100" onclick="record_events('confirm');"><i class="uil uil-check-circle"></i><?=Lang::get('Save')?></button>
                    </div>
                    <div class="col-lg-6 col-md-6 pt-1">
                        <button type="button" class="btn btn-lg btn-icon btn-icon-start btn-outline-default rounded-pill w-100" data-bs-dismiss="modal"><i class="uil uil-times-circle"></i><?=Lang::get('Close')?></button>
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