<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    $index['page'] = 'backoffice';
    $link = APP_PATH.'/'.$index['page'];
    $form = APP_PATH.'/'.$index['page'];
    $tabby = '01';
    if( isset($_GET['users']) ){
        $tabby = '02';
    }
    $tabs = array('01'=>array('icon'=>'<i class="uil uil-dashboard"></i>', 'name'=>'แดชบอร์ด', 'link'=>$link)
                , '02'=>array('icon'=>'<i class="uil uil-users-alt"></i>', 'name'=>'บัญชีผู้ใช้', 'link'=>$link.'/?users')

    );
    $tabhtmls = '<div class="row gx-1 gy-1 justify-content-center">';
    foreach($tabs as $key => $item){
        $tabhtmls .= '<div class="col-3">';
            $tabhtmls .= '<button type="button" class="btn btn-lg '.(($key==$tabby)?' btn-primary':' btn-outline-primary').' rounded w-100" onclick="document.location=\''.$item['link'].'\';">';
                $tabhtmls .= '<div class="icon-box">'.$item['icon'].'</div><span>'.$item['name'].'</span>';
            $tabhtmls .= '</button>';
        $tabhtmls .= '</div>';
    }
    $tabhtmls .= '</div>';
?>
<?php include(APP_HEADER);?>
<style type="text/css">
    body { background:url('<?=THEME_IMG?>/map.png') top center; }
    .tab-menus button>.icon-box {
        width: 32px;
        height: 32px;
        background: none;
        text-align: center;
        display: inline-block;
        border-radius: 50%;
        -moz-border-radius: 50%;
        -webkit-border-radius: 50%;
    }
    .tab-menus button>.icon-box>i {
        color: #3f78e0;
        font-size: 26px;
        line-height: 32px;
    }
    .tab-menus button>span {
        overflow:hidden;
        white-space:nowrap;
        text-overflow:ellipsis;
        padding: 0 0 0 5px;
    }
    .tab-menus button:hover>.icon-box,
    .tab-menus button.btn-primary>.icon-box {
        background: white;
    }
</style>
<section class="wrapper bg-soft-primary">
    <div class="container tab-menus pt-2 pb-4"><?=$tabhtmls?></div>
</section>
<section class="wrapper">
    <div class="container"><?php include(APP_ROOT.'/'.$index['page'].'/'.$tabby.'/index.php'); ?></div>
</section>
<?php include(APP_FOOTER);?>