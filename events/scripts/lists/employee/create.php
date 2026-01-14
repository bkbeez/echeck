<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    // Begin
    $parameters = array();
    $parameters['removeas'] = 'AT-'.$_POST['employee_id'];
    Status::success( 'บันทึกเรียบร้อยแล้ว', $parameters);
?>