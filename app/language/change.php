<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    $_SESSION['SITE_LANGUAGE'] = ( (isset($_POST['lang'])&&$_POST['lang']) ? $_POST['lang'] : 'th' );
    Status::success( ( (App::lang()=='en') ? 'Language was changed' : 'เปลี่ยนภาษาเรียบร้อยแล้ว' ) );
?>