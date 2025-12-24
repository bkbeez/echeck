<?php if(!isset($index['page'])||$index['page']!='manage'){ header("location:".((isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']==443)?'https://':'http://').$_SERVER["HTTP_HOST"]); exit(); } ?>
<style type="text/css">
    body { background:url('<?=THEME_IMG?>/map.png') top center; }
</style>
<section class="wrapper bg-sky angled lower-end">
    <div class="container pt-7 pt-md-11 pb-8">
        <div class="site-intro" data-cues="slideInDown" data-group="page-title" data-delay="600">
            <h1 class="display-1 text-white mb-4"><i class="uil uil-file-plus-alt"></i> เพิ่มกิจกรรมใหม่</h1>
        </div>
    </div>
</section>
<section class="wrapper image-wrapper bg-auto no-overlay bg-image bg-map" data-image-src="<?=THEME_IMG?>/map.png" style="background: url('<?=THEME_IMG?>/map.png') top center;">
    <div class="container pt-18 pb-15">
        <div class="row text-center">
            <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
                <h3 class="display-4 text-sky mb-10 px-xl-10">กิจกรรมและรายชื่อผู้เข้าร่วม</h3>
            </div>
        </div>
        <div class="position-relative mb-7">

        </div>
    </div>
</section>