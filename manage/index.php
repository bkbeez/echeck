<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    $index['page'] = 'manage';
    $link = APP_PATH.'/'.$index['page'];
    $form = APP_PATH.'/'.$index['page'];
    if( !Auth::check() ){
        $_SESSION['login_redirect'] = APP_HOME.'/'.$index['page']; 
        header('Location: '.APP_HOME.'/login');
        exit;
    }
    $loadpage = null;
    if( isset($_GET['events']) ){
        $loadpage = 'events';
        $index['view'] = $loadpage;
    }else if( isset($_GET['participants']) ){
        $loadpage = 'participants';
        $index['view'] = $loadpage;
    }
?>
<?php include(APP_HEADER);?>
<?php if( isset($loadpage)&&$loadpage ){ include(APP_ROOT.'/'.$index['page'].'/'.$loadpage.'/index.php'); }else{ ?>
<style type="text/css">
    body { background:url('<?=THEME_IMG?>/map.png') top center; }
</style>
<section class="wrapper bg-sky angled lower-start">
    <div class="container pt-10 pb-12 pt-md-14 pb-md-17">
        <div class="row gx-lg-8 gx-xl-12 gy-10 align-items-center">
            <div class="col-md-10 offset-md-1 offset-lg-0 col-lg-5 mt-lg-n2 text-center text-lg-start order-2 order-lg-0" data-cues="slideInDown" data-group="page-title" data-delay="600">
                <h1 class="display-1 mb-5 mx-md-10 mx-lg-0">User Management, Accounts and Permissions<br /><span class="typer text-primary text-nowrap" data-delay="100" data-words="Admin, Administrator, Administrator Only"></span><span class="cursor text-primary" data-owner="typer"></span></h1>
                <p class="lead fs-lg mb-7">Dashboard of users and system logs.</p>
                <div class="d-flex justify-content-center justify-content-lg-start" data-cues="slideInDown" data-group="page-title-buttons" data-delay="900">
                    <span><a href="<?=$link.'/?users'?>" class="btn btn-lg btn-primary btn-icon btn-icon-start ounded me-2"><i class="uil uil-users-alt"></i> User Account</a></span>
                    <span><a href="<?=$link.'/?logs'?>" class="btn btn-lg btn-soft-red btn-icon btn-icon-start rounded"><i class="uil uil-dashboard"></i> Logs</a></span>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="row">
                    <div class="col-3 offset-1 offset-lg-0 col-lg-4 d-flex flex-column" data-cues="zoomIn" data-group="col-start" data-delay="300">
                        <div class="ms-auto mt-auto"><img class="img-fluid rounded shadow-lg" src="<?=THEME_IMG?>/photos/sa20.jpg" srcset="<?=THEME_IMG?>/photos/sa20@2x.jpg 2x" alt="" /></div>
                        <div class="ms-auto mt-5 mb-10"><img class="img-fluid rounded shadow-lg" src="<?=THEME_IMG?>/photos/sa18.jpg" srcset="<?=THEME_IMG?>/photos/sa18@2x.jpg 2x" alt="" /></div>
                    </div>
                    <div class="col-4 col-lg-5" data-cue="zoomIn">
                        <div><img class="w-100 img-fluid rounded shadow-lg" src="<?=THEME_IMG?>/photos/sa16.jpg" srcset="<?=THEME_IMG?>/photos/sa16@2x.jpg 2x" alt="" /></div>
                    </div>
                    <div class="col-3 d-flex flex-column" data-cues="zoomIn" data-group="col-end" data-delay="300">
                        <div class="mt-auto"><img class="img-fluid rounded shadow-lg" src="<?=THEME_IMG?>/photos/sa21.jpg" srcset="<?=THEME_IMG?>/photos/sa21@2x.jpg 2x" alt="" /></div>
                        <div class="mt-5"><img class="img-fluid rounded shadow-lg" src="<?=THEME_IMG?>/photos/sa19.jpg" srcset="<?=THEME_IMG?>/photos/sa19@2x.jpg 2x" alt="" /></div>
                        <div class="mt-5 mb-10"><img class="img-fluid rounded shadow-lg" src="<?=THEME_IMG?>/photos/sa17.jpg" srcset="<?=THEME_IMG?>/photos/sa17@2x.jpg 2x" alt="" /></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="wrapper bg-light angled upper-end lower-start">
    <div class="container py-1 position-relative">&nbsp;</div>
</section>
<?php } ?>
<?php include(APP_FOOTER);?>