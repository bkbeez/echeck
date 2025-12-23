<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    $index['page'] = 'home';
?>
<?php include(APP_HEADER); ?>
<?php if( isset($_SESSION['login']) ){ ?>
    <section class="wrapper bg-soft-primary">
      <div class="container pt-10 pb-12 pt-md-14 pb-md-17">
        <div class="row gx-lg-8 gx-xl-12 gy-10 align-items-center">
          <div class="col-md-10 offset-md-1 offset-lg-0 col-lg-5 mt-lg-n2 text-center text-lg-start order-2 order-lg-0" data-cues="slideInDown" data-group="page-title" data-delay="600">
            <div class="display-1 mb-5 mx-10 mx-lg-0">
              <img src="favicon.png" alt="logo_edu_cmu">
            </div>
            <h1 class="display-1 mb-5 mx-md-10 mx-lg-0">ลงทะเบียนกิจกรรม <br /><span class="typer text-primary text-nowrap" data-delay="100" data-words="คณะศึกษาศาสตร์,มหาวิทยาลัยเชียงใหม่"></span><span class="cursor text-primary" data-owner="typer"></span></h1>
            <p class="lead fs-lg mb-7"> ลงทะเบียนกิจกรรมสำหรับบุคคลภายนอกและภายใน สามารถเลือกดูและเข้าร่วมกิจกรรมที่ท่านสนใจได้ </p>
          </div>
          <!-- รูปภาพกิจกรรมของคณะศึกษาศาสตร์ -->
          <div class="col-lg-5 offset-lg-1 ">
            <div class="swiper-container dots-over shadow-lg" data-margin="5" data-nav="true" data-dots="true">
              <div class="swiper">
                <div class="swiper-wrapper">
                  <div class="swiper-slide"><img src="/public/img/586218702_1259550142867584_64333952542632195_n.jpg" srcset="/public/img/586218702_1259550142867584_64333952542632195_n.jpg 2x" class="rounded" alt="" /></div>
                  <div class="swiper-slide"><img src="/public/img/587131147_1259549976200934_6032345642036134148_n.jpg" srcset="/public/img/587131147_1259549976200934_6032345642036134148_n.jpg 2x" class="rounded" alt="" /></div>
                  <div class="swiper-slide"><img src="/public/img/587406621_1259534246202507_779494714266155295_n.jpg" srcset="/public/img/587406621_1259534246202507_779494714266155295_n.jpg 2x" class="rounded" alt="" /></div>
                  <div class="swiper-slide"><img src="./assets/img/photos/about23.jpg" srcset="./assets/img/photos/about23@2x.jpg 2x" class="rounded" alt="" /></div>
                  <div class="swiper-slide"><img src="./assets/img/photos/about23.jpg" srcset="./assets/img/photos/about23@2x.jpg 2x" class="rounded" alt="" /></div>
                </div>
                <!--/.swiper-wrapper -->
              </div>
              <!--/.swiper -->
            </div>
            <!-- /.swiper-container -->
          </div>
            <!---Menu--->
            <div class="container py-12">
              <div class="card shadow-sm p-5 ">
                  <h2 class="mb-4 text-center">เมนูหลัก</h2>
                  <div class="row g-4 text-center">

                      <!-- จัดการกิจกรรม -->
                      <div class="col-md-3">
                        <div class="card">
                          <div class="card-body">
                            <a href="event/index.php" class="btn btn-soft-ash ">
                              <div class="menu-box text-center">
                                <i class="bi bi-clipboard-data menu-icon"></i>
                                <div class="menu-title"> จัดการกิจกรรม </div>
                              </div>
                            </a>
                          </div>
                        </div>
                      </div>

                      <!-- ลงทะเบียน Staff -->
                      <div class="col-md-3">
                        <div class="card" >
                          <div class="card-body" >
                            <a href="participants/staff_register.php" class="btn btn-soft-ash ">
                              <div class="menu-box text-center">
                                <i class="bi bi-clipboard-check menu-icon"></i>
                                <div class="menu-title">ลงทะเบียน Staff</div>
                              </div>
                            </a>
                            </div>
                          </div>
                      </div>
                      <!-- ลงทะเบียน User -->
                      <div class="col-md-3">
                        <div class="card" >
                          <div class="card-body">
                            <a href="participants/user_register.php" class="btn btn-soft-ash ">
                              <div class="menu-box text-center">
                                <i class="bi bi-clipboard-check menu-icon"></i>
                                <div class="menu-title">ลงทะเบียน User</div>
                              </div>
                            </a>
                            </div>
                          </div>
                      </div>

                      <!-- รายชื่อผู้เข้าร่วม -->
                      <div class="col-md-3">
                        <div class="card" >
                          <div class="card-body" >
                            <a href="participants/index.php" class="btn btn-soft-ash "  >
                              <div class="menu-box text-center">
                                <i class="bi bi-people-fill menu-icon"></i>
                                <div class="menu-title">รายชื่อผู้เข้าร่วม</div>
                              </div>
                            </a>
                          </div>
                        </div>
                      </div>
                  </div>
              </div>
            </div>
          <!-- /column -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container -->
    </section>
<?php }else{ ?>
<section class="wrapper bg-grape angled lower-start">
    <div class="container pt-7 pt-md-11 pb-8">
        <div class="row gx-0 gy-10 align-items-center">
            <div class="col-lg-6" data-cues="slideInDown" data-group="page-title" data-delay="600">
                <h1 class="display-1 text-white mb-4">Sandbox focuses on <br /><span class="typer text-orange text-nowrap" data-delay="100" data-words="customer satisfaction,business needs,creative ideas"></span><span class="cursor text-orange" data-owner="typer"></span></h1>
                <p class="lead fs-24 lh-sm text-white mb-7 pe-md-18 pe-lg-0 pe-xxl-15">We carefully consider our solutions to support each and every stage of your growth.</p>
                <div>
                    <a class="btn btn-lg btn-primary rounded">Get Started</a>
                </div>
            </div>
            <div class="col-lg-5 offset-lg-1 mb-n18" data-cues="slideInDown">
                <div class="position-relative">
                    <figure class="rounded shadow-lg"><img src="<?=THEME_IMG?>/photos/cmu-clock-tower.png" srcset="<?=THEME_IMG?>/photos/cmu-clock-tower.png 2x" alt=""></figure>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="wrapper bg-light">
    <div class="container pt-18">
        <div class="row text-center">
            <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
                <h2 class="fs-15 text-uppercase text-primary mb-3">What We Do?</h2>
                <h3 class="display-4 mb-10 px-xl-10">The service we offer is specifically designed to meet your needs.</h3>
            </div>
        </div>
        <div class="position-relative mb-7">
            <div class="shape rounded-circle bg-soft-primary rellax w-16 h-16" data-rellax-speed="1" style="bottom: -0.5rem; right: -2.5rem; z-index: 0;"></div>
            <div class="shape bg-dot primary rellax w-16 h-17" data-rellax-speed="1" style="top: -0.5rem; left: -2.5rem; z-index: 0;"></div>
            <div class="row gx-md-5 gy-5 text-center">
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon btn btn-circle btn-lg btn-primary pe-none mb-3"> <i class="uil uil-phone-volume"></i> </div>
                            <h4>24/7 Support</h4>
                            <p class="mb-2">Nulla vitae elit libero, a pharetra augue. Donec id elit non mi porta gravida at eget metus cras justo.</p>
                            <a href="#" class="more hover link-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon btn btn-circle btn-lg btn-primary pe-none mb-3"> <i class="uil uil-shield-exclamation"></i> </div>
                            <h4>Secure Payments</h4>
                            <p class="mb-2">Nulla vitae elit libero, a pharetra augue. Donec id elit non mi porta gravida at eget metus cras justo.</p>
                            <a href="#" class="more hover link-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon btn btn-circle btn-lg btn-primary pe-none mb-3"> <i class="uil uil-laptop-cloud"></i> </div>
                            <h4>Daily Updates</h4>
                            <p class="mb-2">Nulla vitae elit libero, a pharetra augue. Donec id elit non mi porta gravida at eget metus cras justo.</p>
                            <a href="#" class="more hover link-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="icon btn btn-circle btn-lg btn-primary pe-none mb-3"> <i class="uil uil-chart-line"></i> </div>
                            <h4>Market Research</h4>
                            <p class="mb-2">Nulla vitae elit libero, a pharetra augue. Donec id elit non mi porta gravida at eget metus cras justo.</p>
                            <a href="#" class="more hover link-primary">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row gx-lg-8 gx-xl-12 gy-10 align-items-center mb-14 mb-md-17">
            <div class="col-lg-7">
                <figure><img class="w-auto" src="./assets/img/illustrations/i11.png" srcset="./assets/img/illustrations/i11@2x.png 2x" alt="" /></figure>
            </div>
        </div>
    </div>
</section>
<section class="wrapper bg-grape angled upper-end"></section>
<?php } ?>
<?php include(APP_FOOTER); ?>