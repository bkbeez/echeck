<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/admin/?users'); ?>
<?php
    $form = ( (isset($_POST['form_as'])&&$_POST['form_as']) ? $_POST['form_as'] : null );
    $rolehtmls = '';
    if( (isset($_POST['date_at'])&&$_POST['date_at'])&&(isset($_POST['email'])&&$_POST['email']) ){
        $data = DB::one("SELECT xlg_login.*
                        , CONCAT(DATE_FORMAT(xlg_login.date_at,'%d/%m/'), (YEAR(xlg_login.date_at)+543),' ',DATE_FORMAT(xlg_login.date_at, '%H:%i:%s')) AS date_display
                        , TRIM(CONCAT(COALESCE(member.title,''),member.name,' ',COALESCE(member.surname,''))) AS fullname
                        , CONCAT(IF(xlg_login.ip_client IS NOT NULL,xlg_login.ip_client,'')
                            , IF(xlg_login.device IS NOT NULL,CONCAT(' &rang; ',xlg_login.device),'')
                            , IF(xlg_login.platform IS NOT NULL,CONCAT(' &rang; ',xlg_login.platform),'')
                            , IF(xlg_login.browser IS NOT NULL,CONCAT(' &rang; ',xlg_login.browser),'')
                        ) AS remark
                        FROM xlg_login
                        LEFT JOIN member ON xlg_login.email=member.email
                        WHERE xlg_login.date_at=:date_at AND xlg_login.email=:email
                        LIMIT 1;"
                        , array('date_at'=>$_POST['date_at'], 'email'=>$_POST['email'])
        );
    }
?>
<style type="text/css">
    .modal-dialog .modal-header {
        min-height:100px;
        background: #eeeef6;
    }
    .modal-dialog .modal-body {
        margin-top: -30px;
        padding-left: 35px;
        padding-right: 35px;
    }
    .modal-dialog .modal-body>.alert {
        padding: 5px 15px;
    }
</style>
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-manage">
        <form name="RecordForm" action="javascript:void(0);" method="POST" enctype="multipart/form-data" class="form-manage" target="_blank">
            <input type="hidden" name="id" value="<?=((isset($data['id'])&&$data['id'])?$data['id']:null)?>"/>
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h2 class="mb-0 text-primary text-start on-text-oneline"><i class="uil uil-user-circle" style="float:left;font-size:36px;line-height:36px;margin-right:3px;"></i> ข้อมูลผู้ใช้</h2>
            </div>
            <div class="modal-body">
                <div class="alert alert-primary alert-icon mb-2">
                    <div class="form-floating mb-1">
                        <div class="form-control on-text-display"><?=((isset($data['date_display'])&&$data['date_display'])?$data['date_display']:'-')?></div>
                        <label>วันที่/เวลา</label>
                    </div>
                    <div class="form-floating mb-1">
                        <div class="form-control on-text-display"><?=((isset($data['email'])&&$data['email'])?$data['email']:'-')?></div>
                        <label>อีเมล</label>
                    </div>
                    <div class="form-floating mb-1">
                        <div class="form-control on-text-display"><?=((isset($data['fullname'])&&$data['fullname'])?$data['fullname']:'-')?></div>
                        <label>ชื่อผู้ใช้</label>
                    </div>
                </div>
                <div class="alert alert-primary alert-icon mb-4" style="padding:5px 15px;">
                    <div class="mb-1">
                        <div class="form-control on-text-display">
                            <div style="right:20px;float:right;position:absolute;margin-top:-2px;"><a href="javascript:void(0);" class="btn btn-soft-primary btn-sm rounded-pill" onclick="record_events('display', { 'self':this });" display="N" style="padding:1px 7px 1px 5px;"><span class="uil uil-plus"></span><font class="on-text-normal">ข้อมูล</font></a></div>
                            <b class="fs-16">ข้อมูลอื่นๆ</b>
                        </div>
                    </div>
                    <div class="on-display" style="display:none;">
                        <div class="row gx-1">
                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 mx-auto">
                                <div class="form-floating mb-1">
                                    <div class="form-control on-text-display"><?=((isset($data['device'])&&$data['device'])?$data['device']:'-')?></div>
                                    <label>Device</label>
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 mx-auto">
                                <div class="form-floating mb-1">
                                    <div class="form-control on-text-display"><?=((isset($data['platform'])&&$data['platform'])?$data['platform']:'-')?></div>
                                    <label>Platform</label>
                                </div>
                            </div>
                        </div>
                        <div class="row gx-1">
                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 mx-auto">
                                <div class="form-floating mb-1">
                                    <div class="form-control on-text-display"><?=((isset($data['browser'])&&$data['browser'])?$data['browser']:'-')?></div>
                                    <label>Browser</label>
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 mx-auto">
                                <div class="form-floating mb-1">
                                    <div class="form-control on-text-display"><?=((isset($data['ip_client'])&&$data['ip_client'])?$data['ip_client']:'-')?></div>
                                    <label>IP Client</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    function record_events(action, params){
        if(action=='display'){
            if( $(params.self).attr('display')=='N' ){
                $(params.self).attr('display', 'Y');
                $(params.self).find('span').attr('class','uil uil-minus');
                $('.on-display').slideDown();
            }else{
                $(params.self).attr('display', 'N');
                $(params.self).find('span').attr('class','uil uil-plus');
                $('.on-display').slideUp();
            }
        }
    }
</script>