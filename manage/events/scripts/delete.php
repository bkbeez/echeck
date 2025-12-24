<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/admin/?users'); ?>
<?php
    if(!isset($_POST['id'])||!$_POST['id']){
        Status::error( Lang::get('NotFound').Lang::get('Id').' !!!' );
    }else if(!isset($_POST['email'])||!$_POST['email']){
        Status::error( Lang::get('NotFound').Lang::get('Email').' !!!' );
    }
    // Begin
    $parameters = array();
    $parameters['id'] = $_POST['id'];
    $parameters['email'] = $_POST['email'];
    if( DB::delete("DELETE FROM `member` WHERE id=:id AND email=:email;", $parameters) ){
        DB::delete("DELETE FROM `member_permission` WHERE email=:email;", array('email'=>$parameters['email']) );
        Status::success( Lang::get('SuccessDelete') );
    }
    Status::error( Lang::get('PleaseTryAgain'), array('title'=>Lang::get('CanNotDelete')) );
?>