<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    $index['page'] = 'home';
    $index['addfooter'] = true;
?>
<?php include(APP_HEADER); ?>
<style type="text/css">
    .navbar-brand {
        min-height: 72px;
    }
    .navbar-brand img.on-light {
        height: 148px;
    }
    #mainsite-navbar, .navbar-other {
        margin-top: -70px;
    }
    .navbar-stick #mainsite-navbar,
    .navbar-stick .navbar-other {
        margin-top: 0;
    }
</style>
<section class="wrapper bg-primary angled lower-end">
    <div class="container pt-5 pb-10 mt-n14">
        <div class="row gx-0 gy-10 align-items-center">
            <div class="col-lg-6 site-intro" data-cues="slideInDown" data-group="page-title" data-delay="600">
                <h1 class="display-1 text-yellow pt-12 mb-4"><?=APP_NAME?><br/>
                <span class="typer text-sky text-nowrap" data-delay="100" data-words="คณะศึกษาศาสตร์,Faculty of Education,มหาวิทยาลัยเชียงใหม่,Chiang Mai University"></span><span class="cursor fs-38 text-sky" data-owner="typer"></span></h1>
                <p class="lead fs-24 lh-sm text-white mb-7 pe-lg-0 pe-xxl-15">ระบบบริการจัดการข้อมูลกิจกรรมและรายชื่อผู้เข้าร่วม สำหรับใช้ลงทะเบียนเข้าร่วมกิจกรรมต่างๆ ที่จัดโดยคณะศึกษาศาสตร์ มหาวิทยาลัยเชียงใหม่</p>
                <div><a href="<?=APP_HOME?>/events" class="btn btn-lg btn-soft-primary rounded-pill">เริ่มต้นใช้งาน &rarr;</a></div>
            </div>
            <div class="col-lg-5 offset-lg-1 mb-n18" data-cues="slideInDown">
                <div class="swiper-container dots-over shadow-lg" data-margin="0" data-autoplay="true" data-autoplaytime="3000" data-nav="true" data-dots="false" data-items="1">
                    <div class="swiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide"><img src="<?=THEME_IMG?>/slide/photo-1.png" srcset="<?=THEME_IMG?>/slide/photo-1.png 2x" class="rounded" alt="slide-photo" /></div>
                            <div class="swiper-slide"><img src="<?=THEME_IMG?>/slide/photo-2.png" srcset="<?=THEME_IMG?>/slide/photo-2.png 2x" class="rounded" alt="slide-photo" /></div>
                            <div class="swiper-slide"><img src="<?=THEME_IMG?>/slide/photo-3.png" srcset="<?=THEME_IMG?>/slide/photo-3.png 2x" class="rounded" alt="slide-photo" /></div>
                            <div class="swiper-slide"><img src="<?=THEME_IMG?>/slide/photo-4.png" srcset="<?=THEME_IMG?>/slide/photo-4.png 2x" class="rounded" alt="slide-photo" /></div>
                            <div class="swiper-slide"><img src="<?=THEME_IMG?>/slide/photo-5.png" srcset="<?=THEME_IMG?>/slide/photo-5.png 2x" class="rounded" alt="slide-photo" /></div>
                            <div class="swiper-slide"><img src="<?=THEME_IMG?>/slide/photo-6.png" srcset="<?=THEME_IMG?>/slide/photo-6.png 2x" class="rounded" alt="slide-photo" /></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="wrapper image-wrapper bg-auto no-overlay bg-image bg-map" data-image-src="<?=THEME_IMG?>/map.png" style="background: url('<?=THEME_IMG?>/map.png') top center;">
    <div class="container pt-17 pb-15">
        <div class="row text-center">
            <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
                <h3 class="display-4 text-primary mb-5 px-xl-10">กิจกรรมและรายชื่อผู้เข้าร่วม</h3>
            </div>
        </div>
        <div class="position-relative mb-7">
            <div class="shape rounded-circle bg-soft-yellow rellax w-16 h-16" data-rellax-speed="1" style="bottom: -2.5rem; right: -2.5rem; z-index: 0;"></div>
            <div class="shape bg-dot blue rellax w-16 h-17" data-rellax-speed="1" style="top: -0.5rem; left: -2.5rem; z-index: 0;"></div>
            <div class="row gx-md-5 gy-5 text-center">
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon btn btn-circle btn-lg btn-primary pe-none mb-3"><i class="uil uil-edit"></i></div>
                            <h4>จัดการกิจกรรม</h4>
                            <a href="<?=APP_HOME?>/events" class="more hover link-primary">เข้าใช้งาน</a>
                        </div>
                    </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon btn btn-circle btn-lg btn-primary pe-none mb-3"><i class="uil uil-users-alt"></i></div>
                            <h4>รายชื่อผู้เข้าร่วม</h4>
                            <a href="<?=APP_HOME?>/participants" class="more hover link-primary">เข้าใช้งาน</a>
                        </div>
                    </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon btn btn-circle btn-lg btn-primary pe-none mb-3"><i class="uil uil-qrcode-scan"></i></div>
                            <h4>ลงทะเบียน<sup> USER</sup></h4>
                            <a href="<?=APP_HOME?>/participants/user_register.php" class="more hover link-primary">เข้าใช้งาน</a>
                        </div>
                    </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon btn btn-circle btn-lg btn-primary pe-none mb-3"><i class="uil uil-desktop"></i></div>
                            <h4>ลงทะเบียน<sup> STAFF</sup></h4>
                            <a href="<?=APP_HOME?>/participants/staff_register.php" class="more hover link-primary">เข้าใช้งาน</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include(APP_FOOTER); ?>