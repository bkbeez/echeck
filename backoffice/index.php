<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    $index['page'] = 'backoffice';
    $link = APP_PATH.'/'.$index['page'];
    $form = APP_PATH.'/'.$index['page'];
    if( !Auth::check() ){
        $_SESSION['login_redirect'] = APP_HOME.'/'.$index['page']; 
        header('Location: '.APP_HOME.'/login');
        exit;
    }
    if( !Auth::admin() ){
        $_SESSION['deny'] = array();
        $_SESSION['deny']['title'] = ( (App::lang()=='en') ? 'Oops! For Officer Only' : 'ขออภัย! สำหรับเจ้าหน้าที่เท่านั้น' );
        header('Location: '.APP_HOME.'/deny');
        exit;
    }
    $meeting_id = User::meeting();
    $tabs = array('01'=>array('no'=>'&#10112;', 'width'=>'t-auto', 'name'=>'<span style="letter-spacing:-1.5px;">Checking</span>'
                            , 'qty'=>'<span id="on-check-qty" class="badge bg-red rounded-pill d-empty">0</span>'
                            , 'link'=>$link
                )
                , '02'=>array('no'=>'&#10113;', 'width'=>'t-auto', 'name'=>'Online'
                            , 'qty'=>null
                            , 'link'=>$link.'/?online'
                )
                , '03'=>array('no'=>'&#10114;', 'width'=>'t-auto', 'name'=>'Onsite'
                            , 'qty'=>null
                            , 'link'=>$link.'/?onsite'
                )
                , '04'=>array('no'=>'&#10115;', 'width'=>'t-auto', 'name'=>'Article'
                            , 'qty'=>'<span id="on-article-qty" class="badge bg-red rounded-pill d-empty">0</span>'
                            , 'link'=>$link.'/?article'
                )
                , '05'=>array('no'=>'&#10116;', 'width'=>'t-auto', 'name'=>'Fullpaper'
                            , 'qty'=>'<span id="on-fullpaper-qty" class="badge bg-red rounded-pill d-empty">0</span>'
                            , 'link'=>$link.'/?fullpaper'
                )
                , 'report'=>array('no'=>'&#10117;', 'width'=>'t-max', 'name'=>'Report'
                            , 'qty'=>null
                            , 'link'=>$link.'/?report'
                )
    );
    $tabby = '01';
    if( isset($_GET['online']) ){
        $tabby = '02';
    }else if( isset($_GET['onsite']) ){
        $tabby = '03';
    }else if( isset($_GET['article']) ){
        $tabby = '04';
    }else if( isset($_GET['fullpaper']) ){
        $tabby = '05';
    }else if( isset($_GET['report']) ){
        $tabby = 'report';
    }else if( isset($_GET['setting']) ){
        $tabby = 'setting';
    }else if( isset($_GET['mobile']) ){
        $tabby = 'mobile';
    }
?>
<?php include(APP_HEADER);?>
<style type="text/css">
    body {
        background-color:rgb(22 38 132)!important;
    }
    .iceahe-profile {
        float: right;
    }
    .tab-bar {
        width: 100%;
        height: 50px;
        overflow:hidden;
        white-space:nowrap;
        text-overflow:ellipsis;
        border-bottom:2px solid #edeff4;
    }
    .tab-bar>.tab {
        width: 125px;
        float: left;
        font-size: 18px;
        cursor: pointer;
        padding: 6px 2px 5px 2px;
        font-weight: normal;
        overflow:hidden;
        white-space:nowrap;
        text-overflow:ellipsis;
        border-bottom:2px solid white;
    }
    .tab-bar>.tab.t-max {
        width: 135px;
    }
    .tab-bar>.tab .badge {
        float: left;
        font-size: 9px;
        position: absolute;
        margin: -5px 0 0 -15px;
    }
    .tab-bar>.tab .badge.d-empty {
        color: #999!important;
        padding-left: 5px;
        padding-right: 5px;
        background: #ddd!important;
    }
    .tab-bar>.tab>.i-box {
        float:left;
        width: 30px;
        height: 35px;
        margin-right: 2px;
        line-height: 32px;
        text-align: center;
    }
    .tab-bar>.tab>.i-box>font {
        font-size: 32px;
        font-weight: normal;
    }
    .tab-bar>.tab:hover,
    .tab-bar>.tab.active {
        color: #3b337d;
        border-bottom-color: #3b337d;
    }
    .tab-bar>.tab.setting {
        right:5px;
        width: 32px;
        float: right;
        border:none;
        padding: 5px 0;
        position: absolute;
    }
    .tab-bar .tab.setting>.i-box{
        float: right;
    }
    .tab-bar .tab.setting>.i-box>i {
        font-size: 28px;
    }
    .tab-bar>.tab.setting:hover,
    .tab-bar>.tab.setting.active {
        background: #eaecf2;
        border-radius: 25px;
        -moz-border-radius: 25px;
        -webkit-border-radius: 25px;
        border-bottom-color: white;
    }
    .tab-body .tab-body-title {
        display: none;
        background: #3f78e0;
        border-radius: 10px;
        -moz-border-radius: 10px;
        -webkit-border-radius: 10px;
    }
    .tab-body .tab-body-title>h3 {
        color: white;
    }
    /* Table */
    .table-filter .filter-result .date {
        width: 115px;
    }
    .table-filter .filter-result .name {
        width: 30%;
    }
    .table-filter .filter-result .institute {
        width: auto;
    }
    .table-filter .filter-result .amount {
        width: auto;
    }
    .table-filter .filter-result .name>span,
    .table-filter .filter-result .institute>span,
    .table-filter .filter-result .amount>span,
    .table-filter .filter-result .amount>small {
        display: block;
        font-weight: normal;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .table-filter .filter-result .name>.phone>.email {
        padding-left: 3px;
    }
    .table-filter .filter-result .name>.date-o,
    .table-filter .filter-result .name>.amount-o,
    .table-filter .filter-result .name>.status-o,
    .table-filter .filter-result .name>.institute-o,
    .table-filter .filter-result .name>.participant-o,
    .table-filter .filter-result .name>.staff-o,
    .table-filter .filter-result .institute>.staff-o,
    .table-filter .filter-result .institute>.amount-o,
    .table-filter .filter-result .institute>.status-o {
        display: none;
    }
    .table-filter .filter-result .TEST td {
        background: #fff8ee;
    }
    .table-filter .filter-result sup.test {
        color: red;
        font-style: italic;
    }
    .table-filter .filter-result .at-o>code {
        color: #45c4a0;
        padding:1px 5px;
        font-size: 16px;
        background: #e1f6f0;
        border-radius: 0.2rem;
        -moz-border-radius: 0.2rem;
        -webkit-border-radius: 0.2rem;
    }
    .table-filter .filter-result .vip-o>code {
        padding:2px 3px 1px 3px;
        font-size: 14px;
        border-radius: 0.2rem;
        -moz-border-radius: 0.2rem;
        -webkit-border-radius: 0.2rem;
    }
    .table-filter .filter-result .wifi-o>code {
        color: #45c4a0;
        padding:1px 3px;
        font-size: 16px;
        background: #e1f6f0;
        border-radius: 0.2rem;
        -moz-border-radius: 0.2rem;
        -webkit-border-radius: 0.2rem;
    }
    @media only all and (max-width: 991px) {
        .tab-bar>.tab {
            width: 16%;
        }
        .tab-bar>.tab.t-max {
            width: auto;
        }
        .table-filter .filter-result .name>.phone>.email {
            display: block;
            padding-left: 0;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
        .table-filter .filter-result .amount {
            display: none;
        }
        .table-filter .filter-result .institute>.staff-o,
        .table-filter .filter-result .institute>.amount-o,
        .table-filter .filter-result .institute>.status-o {
            display: block;
        }
        .table-filter .filter-result .status-o>span {
            font-size: 14px;
            padding-left: 3px;
        }
        .table-filter .filter-result .actions.act-2 {
            width: 45px;
        }
        .table-filter .filter-result .actions .btn-box.delete {
            margin-top: -4px;
        }
    }
    @media only all and (max-width: 768px) {
        .tab-body .tab-body-title {
            display: block;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
        .table-filter .filter-result .name {
            width: auto;
        }
        .table-filter .filter-result .institute {
            display: none;
        }
        .table-filter .filter-result .name>.amount-o,
        .table-filter .filter-result .name>.status-o,
        .table-filter .filter-result .name>.staff-o,
        .table-filter .filter-result .name>.institute-o,
        .table-filter .filter-result .name>.participant-o {
            display: block;
        }
    }
    @media only all and (max-width: 667px) {
        .tab-bar>.tab,
        .tab-bar>.tab.t-auto,
        .tab-bar>.tab.t-max {
            width: 16%;
        }
        .tab-bar>.tab span.name {
            display: none;
        }
        .table-filter .filter-result .date {
            display: none;
        }
        .table-filter .filter-result .name>.date-o {
            display: block;
        }
    }
</style>
<?php ?>
<section class="wrapper">
    <div class="container backoffice">
        <div style="padding:12px 0;">
            <div class="iceahe-profile">
                <a class="nav-link position-relative d-flex flex-row align-items-center" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-cart">
                    <div class="iceahe-profile-box" style="margin-top:15px;"><img src="<?=User::get('picture')?>" onerror="this.onerror=null;this.src='<?=THEME_IMG.'/avatar.png'?>';"/></div>
                </a>
            </div>
            <img src="<?=THEME_IMG.'/logo/logo.png?'.time()?>" style="height:75px;cursor:pointer;" onclick="document.location='<?=APP_HOME?>';"/>
        </div>
        <div class="row gx-0">
            <div class="col-lg-12 mx-auto">
                <div class="card" style="padding:4px 5px 1px 5px;">
                    <?php
                        $tabhtmls = '<div class="tab-bar">';
                            foreach($tabs as $key => $item){
                                $tabhtmls .= '<div class="tab '.$item['width'].(($key==$tabby)?' active':null).'" onclick="document.location=\''.$item['link'].'\';">';
                                    $tabhtmls .= '<div class="i-box"><font>'.$item['no'].'</font></div>';
                                    $tabhtmls .= ( $item['qty'] ? $item['qty'] : null );
                                    $tabhtmls .= '<span class="name">'.$item['name'].'</span>';
                                $tabhtmls .= '</div>';
                            }
                            if( Auth::admin()||User::get('staff_role')=='STAFF' ){
                                $tabhtmls .= '<div class="tab setting'.(isset($_GET['setting'])?' active':null).'" onclick="document.location=\''.$link.'/?setting\';"><div class="i-box"><i class="uil uil-ellipsis-v"></i></div></div>';
                            }
                        $tabhtmls .= '</div>';
                        echo $tabhtmls;
                    ?>
                    <div class="tab-body">
                        <?=( isset($tabs[$tabby]['name']) ? '<div class="tab-body-title text-center"><h3 class="mt-2 mb-2 on-text-normal">'.$tabs[$tabby]['name'].'</h3></div>' : null )?>
                        <?php include(APP_ROOT.'/'.$index['page'].'/'.$tabby.'/index.php'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <? //App::profile() ?>
</section>
<div id="ManageDialog" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="false" aria-modal="true"></div>
<script type="text/javascript">
    function manage_events(action, params){
        if(action=='new'){
            params['form_as'] = '<?=$form?>';
            $("#ManageDialog").load("<?=$form?>/scripts/participant/new.php", params, function(response, status, xhr){
                if(status=="error"){
                    $(this).html('<div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content text-center">'+xhr.status + "<br>" + xhr.statusText+'<div class="modal-body"></div></div></div>');
                }else{
                    $("#ManageDialog").modal('show');
                }
            });
        }else if(action=='check'){
            params['form_as'] = '<?=$form?>';
            $("#ManageDialog").load("<?=$form?>/scripts/payment/check.php", params, function(response, status, xhr){
                if(status=="error"){
                    $(this).html('<div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content text-center">'+xhr.status + "<br>" + xhr.statusText+'<div class="modal-body"></div></div></div>');
                }else{
                    $("#ManageDialog").modal('show');
                }
            });
        }else if(action=='display'){
            params['form_as'] = '<?=$form?>';
            $("#ManageDialog").load("<?=$form?>/scripts/participant/display.php", params, function(response, status, xhr){
                if(status=="error"){
                    $(this).html('<div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content text-center">'+xhr.status + "<br>" + xhr.statusText+'<div class="modal-body"></div></div></div>');
                }else{
                    $("#ManageDialog").modal('show');
                }
            });
        }else if(action=='manage'){
            params['form_as'] = '<?=$form?>';
            $("#ManageDialog").load("<?=$form?>/scripts/participant/manage.php", params, function(response, status, xhr){
                if(status=="error"){
                    $(this).html('<div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content text-center">'+xhr.status + "<br>" + xhr.statusText+'<div class="modal-body"></div></div></div>');
                }else{
                    $("#ManageDialog").modal('show');
                }
            });
        }else if(action=='sorting'){
            $("#ManageDialog").load("<?=$form?>/scripts/participant/sort.php", { 'form_as':'<?=$form?>', 'link_as':'<?=$link?>'  }, function(response, status, xhr){
                if(status=="error"){
                    $(this).html('<div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content text-center">'+xhr.status + "<br>" + xhr.statusText+'<div class="modal-body"></div></div></div>');
                }else{
                    $("#ManageDialog").modal('show');
                }
            });
        }else if(action=='fullpaper'){
            params['form_as'] = '<?=$form?>';
            $("#ManageDialog").load("<?=$form?>/scripts/fullpaper/check.php", params, function(response, status, xhr){
                if(status=="error"){
                    $(this).html('<div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content text-center">'+xhr.status + "<br>" + xhr.statusText+'<div class="modal-body"></div></div></div>');
                }else{
                    $("#ManageDialog").modal('show');
                }
            });
        }else if(action=='publish'){
            params['form_as'] = '<?=$form?>';
            $("#ManageDialog").load("<?=$form?>/scripts/publish/check.php", params, function(response, status, xhr){
                if(status=="error"){
                    $(this).html('<div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content text-center">'+xhr.status + "<br>" + xhr.statusText+'<div class="modal-body"></div></div></div>');
                }else{
                    $("#ManageDialog").modal('show');
                }
            });
        }else if(action=='fppopen'){
            swal({
                'title':'<b class="text-blue" style="font-size:100px;"><i class="uil uil-check-circle"></i></b>',
                'html' :'<?=( (App::lang()=='en') ? 'Are you sure to open for fullpaper ?' : 'ยืนยันเปิดรับบทความฉบับสมบูรณ์ ?' )?>',
                'showCloseButton': false,
                'showConfirmButton': true,
                'showCancelButton': true,
                'focusConfirm': false,
                'allowEscapeKey': false,
                'allowOutsideClick': false,
                'confirmButtonClass': 'btn btn-icon btn-icon-start btn-success rounded-pill',
                'confirmButtonText':'<font class="fs-16"><i class="uil uil-check-circle"></i> <?=Lang::get('Yes')?></font>',
                'cancelButtonClass': 'btn btn-icon btn-icon-start btn-outline-danger rounded-pill',
                'cancelButtonText':'<font class="fs-16"><i class="uil uil-times-circle"></i> <?=Lang::get('No')?></font>',
                'buttonsStyling': false
            }).then(
                function () {
                    $.ajax({
                        url : "<?=$form?>/scripts/fullpaper/status.php",
                        type: 'POST',
                        data: params,
                        dataType: "json",
                        beforeSend: function( xhr ) {
                            runStart();
                        }
                    }).done(function(data) {
                        runStop();
                        if(data.status=='success'){
                            swal({
                                'type': data.status,
                                'title': data.title,
                                'html': data.text,
                                'showConfirmButton': false,
                                'timer': 1500
                            }).then(
                                function () {},
                                function (dismiss) {
                                    if (dismiss === 'timer') {
                                        $("form[name='filter'] button[type='submit']").click();
                                    }
                                }
                            );
                        }else{
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
                        }
                    });
                },
                function (dismiss) {
                    if (dismiss === 'cancel') {
                        swal.close();
                    }
                }
            );
        }
    }
</script>
<?php include(APP_FOOTER);?>