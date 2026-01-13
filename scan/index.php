
<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    $index['page'] = 'scan';
    $index['hidefooter'] = true;
    $link = APP_PATH.'/'.$index['page'];
    $form = APP_PATH.'/'.$index['page'];
    if( !Auth::check() ){
        $_SESSION['login_redirect'] = APP_HOME.'/'.$index['page']; 
        header('Location: '.APP_HOME.'/login');
        exit;
    }
    if( isset($_POST['events_id']) ){
        $events_id = $_POST['events_id'];
        include('check.php');
    }else{
        include('scan.php');
    }
?>
