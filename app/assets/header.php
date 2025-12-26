<!DOCTYPE html>
<html lang="<?=App::lang()?>">
    <head app-lang="<?=App::lang()?>" app-path="<?=APP_PATH?>">
        <meta charset="utf-8" />
        <meta name="keywords" content="<?=APP_CODE?>,EDU CMU CHECKIN">
        <meta name="description" content="<?=APP_FACT_TH.','.APP_FACT_EN?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible" />
        <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
        <title><?=APP_CODE.( isset($index['page']) ? ' - '.ucfirst($index['page']) : null )?></title>
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
        <link rel="stylesheet" href="<?=THEME_CSS?>/colors/grape.css">
        <link rel="stylesheet" href="<?=THEME_CSS?>/index.css">
        <link rel="stylesheet" href="<?=THEME_CSS?>/table.css">
        <link rel="stylesheet" href="<?=THEME_JS?>/sweetalert/sweetalert2.min.css" />
        <link rel="stylesheet" href="<?=THEME_JS?>/datepicker/thai/datepicker.css" />
        <script type="text/javascript" src="<?=THEME_JS?>/jquery-2.1.1.js"></script>
        <script type="text/javascript" src="<?=THEME_JS?>/jquery.form.min.js"></script>
        <script type="text/javascript" src="<?=THEME_JS?>/sweetalert/sweetalert2.min.js"></script>
        <script type="text/javascript" src="<?=THEME_JS?>/qrcode/html5-qrcode.min.js?<?=time()?>"></script>
        <script type="text/javascript" src="<?=THEME_JS?>/qrcode/jquery.qrcode-0.12.0.min.js"></script>
        <script type="text/javascript" src="<?=THEME_JS?>/datepicker/thai/bootstrap-datepicker.js"></script>
        <script type="text/javascript" src="<?=THEME_JS?>/datepicker/thai/bootstrap-datepicker-thai.js"></script>
        <script type="text/javascript" src="<?=THEME_JS?>/datepicker/thai/locales/bootstrap-datepicker.th.js"></script>
        <script type="text/javascript" src="<?=THEME_JS?>/index.js?<?=time()?>"></script>
    </head>
    <body>
        <div class="page-loader"></div>
        <div class="content-wrapper on-font-primary">
        <!-- Body -->
            <?=App::menus($index)?>