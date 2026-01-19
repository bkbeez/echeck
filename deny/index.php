<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    $index['page'] = 'deny';
    $index['title'] = 'ขออภัย! หน้าไม่พร้อมใช้งาน';
    $index['message'] = 'หน้าที่คุณกำลังค้นหาไม่พร้อมใช้งานหรือถูกย้ายแล้ว ลองหน้าอื่นหรือไปที่หน้าแรกด้วยปุ่มด้านล่าง <b class=on-blink>&darr;</b>';
    if( App::lang()=='en' ){
        $index['title'] = 'Oops! Page Not Found.';
        $index['message'] = 'The page you are looking for is not available or has been moved. Try a different page or go to homepage with the button below. <b class=on-blink>&darr;</b>';
    }
    if( isset($_SESSION['deny']) ){
        if( isset($_SESSION['deny']['title'])&&$_SESSION['deny']['title'] ){
            $index['title'] = $_SESSION['deny']['title'];
        }
        if( isset($_SESSION['deny']['message'])&&$_SESSION['deny']['message'] ){
            $index['message'] = $_SESSION['deny']['message'];
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
        <title><?=$index['title']?></title>
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
        <link rel="stylesheet" href="<?=THEME_CSS?>/index.css" />
        <script type="text/javascript" src="<?=THEME_JS?>/jquery-2.1.1.js"></script>
        <script type="text/javascript" src="<?=THEME_JS?>/index.js"></script>
    </head>
    <body>
        <div class="page-loader"></div>
        <div class="container login on-font-primary">
            <div class="row">
                <div class="col d-flex justify-content-center align-items-center" style="height:100vh;">
                    <div class="row">
                        <div class="col-lg-9 col-xl-8 mx-auto text-center">
                            <figure class="mb-10"><img src="<?=THEME_IMG?>/404.png" srcset="<?=THEME_IMG?>/404@2x.png 2x" style="width:75%;"></figure>
                        </div>
                        <div class="col-lg-8 col-xl-7 col-xxl-6 mx-auto text-center">
                            <h1 class="mb-3 text-danger"><?=$index['title']?></h1>
                            <?=( (isset($index['message'])&&$index['message']) ? '<p class="lead mb-7 px-md-12 px-lg-5 px-xl-7" style="font-weight:normal;">'.$index['message'].'</p>' : null )?>
                            <button type="button" class="btn btn-danger rounded-pill" onclick="gotohome();"><i class="uil uil-sign-in-alt" style="float:left;font-size:36px;line-height:32px;margin-right:3px;"></i><?=Lang::get('GoToHome')?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="progress-wrap"><svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102"><path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" /></svg></div>
        <script type="text/javascript" src="<?=THEME_JS?>/plugins.js"></script>
        <script type="text/javascript" src="<?=THEME_JS?>/theme.js"></script>
        <script type="text/javascript">
            function gotohome(){
                $("body>.container").addClass('bounceIn').fadeIn(1500,function(){
                    $(this).removeClass('bounceIn').addClass('bounceOut').fadeOut(1500,function(){
                        document.location = '<?=APP_HOME?>';
                    });
                });
            }
        </script>
    </body>
</html>