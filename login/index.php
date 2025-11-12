<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    if( Auth::check() ){
        $redirect = APP_HOME;
        if( isset($_SESSION['login_redirect']) ){
            $redirect = $_SESSION['login_redirect'];
            unset($_SESSION['login_redirect']);
        }
        header('Location: '.$redirect);
        exit;
    }
    $index['page'] = 'login';
    if( !Helper::isLocal() ){
        header('Location: '.APP_HOME.'/login/signingoogle.php');
        exit;
    }
?>
<!DOCTYPE html>
<html lang="<?=App::lang()?>">
    <head app-lang="<?=App::lang()?>" app-path="<?=APP_PATH?>">
        <meta charset="utf-8" />
        <meta name="keywords" content="<?=APP_CODE?>,EDU CMU">
        <meta name="description" content="<?=APP_FACT_TH.','.APP_FACT_EN?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible" />
        <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
        <title><?=APP_CODE?> | Login</title>
        <link rel="icon" type="image/png" href="<?=APP_PATH?>/favicon.png" />
        <link rel="icon" type="image/png" sizes="32x32" href="<?=APP_PATH?>/favicon.png" />
        <link rel="icon" type="image/png" sizes="16x16" href="<?=APP_PATH?>/favicon.png" />
        <link rel="icon shortcut" type="image/ico" href="<?=APP_PATH?>/favicon.ico" />
        <link rel="apple-touch-icon" sizes="76x76" href="<?=APP_PATH?>/favicon.png" />
        <link rel="apple-touch-icon" sizes="180x180" href="<?=APP_PATH?>/favicon.png">
        <link rel="apple-touch-icon-precomposed" href="<?=APP_PATH?>/favicon.png" />
        <link rel="stylesheet" href="<?=THEME_CSS?>/plugins.css">
        <link rel="stylesheet" href="<?=THEME_CSS?>/style.css">
        <link rel="stylesheet" href="<?=THEME_JS?>/sweetalert/sweetalert2.min.css" />
        <link rel="stylesheet" href="<?=THEME_CSS?>/index.css?<?=time()?>" />
        <script type="text/javascript" src="<?=THEME_JS?>/jquery-2.1.1.js"></script>
        <script type="text/javascript" src="<?=THEME_JS?>/jquery.form.min.js"></script>
        <script type="text/javascript" src="<?=THEME_JS?>/sweetalert/sweetalert2.min.js"></script>
        <script type="text/javascript" src="<?=THEME_JS?>/index.js?<?=time()?>"></script>
        <style type="text/css">
            body {
                background-size: cover !important;
                background-attachment: fixed !important;
                background: no-repeat center center;
                background-color: rgb(22 38 132)!important;
                background-image: url('<?=THEME_IMG?>/bg/bg-blue.jpg');
            }
            .login .card {
                width: 480px;
            }
        </style>
    </head>
    <body>
        <div class="container login">
            <div class="row">
                <div class="col d-flex justify-content-center align-items-center" style="height:100vh;">
                    <div class="card">
                        <div class="row gx-0">
                            <div class="col-lg-12 p-2">
                                <div class="card-body">
                                    <form name="LoginForm" action="<?=APP_PATH?>/login/signinlocal.php" method="POST" enctype="multipart/form-data" class="form-manage">
                                        <figure class="text-center"><img src="<?=THEME_IMG?>/logo/logo@2x.png" style="width:200px;"/></figure>
                                        <h2 class="mt-2 mb-1 text-center"><?=Lang::get('Welcome')?></h2>
                                        <div class="blockquote-details">
                                            <img class="rounded-circle w-12" src="<?=THEME_IMG?>/avatar.png">
                                            <div class="info">
                                                <h5 class="mb-1">Login User</h5>
                                                <div class="row gx-2">
                                                    <div class="col-md-6">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="login_as" value="admin" checked onchange="login_events(this);">
                                                            <label class="form-check-label form-participant-select">Admin</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="login_as" value="member" onchange="login_events(this);">
                                                            <label class="form-check-label form-participant-select">Member</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-floating mt-2 mb-2 on-admin">
                                            <div class="form-control">admin@edu.cmu.ac.th</div>
                                            <label for="email"><?=Lang::get('Email')?></label>
                                        </div>
                                        <div class="form-floating mt-2 mb-2 on-member" style="display:none;">
                                            <input name="email" value="" type="email" class="form-control" placeholder="..." id="email">
                                            <label for="email"><?=Lang::get('Email')?></label>
                                        </div>
                                        <button type="submit" class="btn btn-lg btn-icon btn-icon-start btn-blue rounded-pill w-100"><i class="uil uil-user"></i> <?=Lang::get('Login')?></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
<script type="text/javascript">
    function login_events(self){
        if( self.value=='member' ){
            $("form[name='LoginForm'] .on-admin").hide();
            $("form[name='LoginForm'] .on-member").fadeIn();
            $("form[name='LoginForm'] input[name='email']").focus();
        }else{
            $("form[name='LoginForm'] .on-member").hide();
            $("form[name='LoginForm'] .on-admin").fadeIn();
        }
    }
    $(document).ready(function() {
        $("form[name='LoginForm']").ajaxForm({
            beforeSubmit: function (formData, jqForm, options) {
                runStart();
            },
            success: function(rs) {
                runStop();
                var data = JSON.parse(rs);
                if(data.status=='success'){
                    $("body").fadeOut('slow', function(){
                        document.location = data.url;
                    });;
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
            }
        });
    });
</script>