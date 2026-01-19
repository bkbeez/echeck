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
    }else{
        if( Helper::isLocal() ){
            $admin = "admin@mail.com";
        }else{
            header('Location: '.APP_HOME.'/login/signingoogle.php');
            exit;
        }
    }
?>
<!DOCTYPE html>
<html lang="<?=App::lang()?>">
    <head app-lang="<?=App::lang()?>" app-path="<?=APP_PATH?>">
        <meta charset="utf-8" />
        <meta name="keywords" content="<?=APP_CODE?>,EDU CMU CHECKIN">
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
        <link rel="apple-touch-icon" sizes="256x204" href="<?=APP_PATH?>/favicon.png">
        <link rel="apple-touch-icon-precomposed" href="<?=APP_PATH?>/favicon.png" />
        <link rel="stylesheet" href="<?=THEME_CSS?>/plugins.css">
        <link rel="stylesheet" href="<?=THEME_CSS?>/style.css">
        <link rel="stylesheet" href="<?=THEME_CSS?>/grape.css">
        <link rel="stylesheet" href="<?=THEME_JS?>/sweetalert/sweetalert2.min.css" />
        <link rel="stylesheet" href="<?=THEME_CSS?>/index.css?<?=time()?>" />
        <script type="text/javascript" src="<?=THEME_JS?>/jquery-2.1.1.js"></script>
        <script type="text/javascript" src="<?=THEME_JS?>/jquery.form.min.js"></script>
        <script type="text/javascript" src="<?=THEME_JS?>/sweetalert/sweetalert2.min.js"></script>
        <script type="text/javascript" src="<?=THEME_JS?>/index.js?<?=time()?>"></script>
        <style type="text/css">
            html,body { background: url('<?=THEME_IMG?>/map.png') top center; }
            .login .card {width: 480px;}
            .login form .signin-google .btn { padding:8px 0 8px 0; }
            .login form .signin-google .btn>img { height:32px;margin:0 5px 0 0;-webkit-border-radius:50%;-moz-border-radius:50%;border-radius:50%;border:2px solid white; }
            @media screen and (max-height:435px) {
                .login .card-body { padding-top:5px;padding-bottom:5px; }
            }
        </style>
    </head>
    <body>
        <div class="page-loader"></div>
        <div class="container login on-font-primary">
            <div class="row">
                <div class="col d-flex justify-content-center align-items-center" style="height:100vh;">
                    <div class="card">
                        <div class="row gx-0">
                            <div class="col-lg-12 p-2">
                                <div class="card-body">
                                    <form name="LoginForm" action="<?=APP_PATH?>/login/loging.php" method="POST" enctype="multipart/form-data" class="form-manage">
                                        <figure class="text-center"><img src="<?=THEME_IMG?>/logo/logo@2x.png" style="width:165px;"/></figure>
                                        <div class="blockquote-details">
                                            <img class="rounded-circle w-12" src="<?=THEME_IMG?>/avatar.png">
                                            <div class="info">
                                                <h5 class="mb-1">Choose login mode :</h5>
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
                                            <input id="admin_email" name="admin_email" value="<?=( isset($admin) ? $admin : null )?>" type="email" class="form-control" placeholder="...">
                                            <label for="admin_email">Admin's Email</label>
                                        </div>
                                        <div class="form-floating mt-2 mb-2 on-member">
                                            <input id="member_email" name="member_email" value="" type="email" class="form-control" placeholder="..." disabled>
                                            <label for="member_email">Member's Email</label>
                                        </div>
                                        <div class="text-center mb-3">
                                            <button type="submit" class="btn btn-primary rounded-pill w-100">LOGIN</button>
                                        </div>
                                        <div class="divider-icon text-center my-1">Or</div>
                                        <div class="signin-google text-center mb-3">
                                            <button type="button" class="btn btn-red rounded-pill w-100 onclick="login_events('google');"><img src="<?=THEME_IMG?>/google.png" alt="google" style="background:white;">Sign in with Google</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="progress-wrap"><svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102"><path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" /></svg></div>
        <script type="text/javascript" src="<?=THEME_JS?>/plugins.js"></script>
        <script type="text/javascript" src="<?=THEME_JS?>/theme.js"></script>
    </body>
</html>
<script type="text/javascript">
    function login_events(self){
        if( self.value=='member' ){
            $("form[name='LoginForm'] input[name='member_email']").removeAttr('disabled');
            $("form[name='LoginForm'] input[name='member_email']").focus();
        }else{
            $("form[name='LoginForm'] input[name='member_email']").attr('disabled', true);
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