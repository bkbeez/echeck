<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    $index['page'] = 'events';
    $link = APP_PATH.'/'.$index['page'];
    $form = APP_PATH.'/'.$index['page'];
    if( !Auth::check() ){
        $_SESSION['login_redirect'] = APP_HOME.'/'.$index['page']; 
        header('Location: '.APP_HOME.'/login');
        exit;
    }
    $loadpage = null;
    if( isset($_GET['list']) ){
        $index['view'] = 'lists';
        $index['back'] = $link;
        $index['addfooter'] = true;
        $loadpage = 'participants/index.php';
    }else{
        $index['view'] = 'lists';
        $loadpage = 'filter/index.php';
    }
?>
<?php include(APP_HEADER); ?>
<style type="text/css">
    body { background:url('<?=THEME_IMG?>/map.png') top center; }
</style>
<?php include(APP_ROOT.'/'.$index['page'].'/'.$loadpage); ?>
<?php include(APP_FOOTER); ?>