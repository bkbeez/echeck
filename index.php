<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    $index['page'] = 'home';
?>
<?php include(APP_HEADER); ?>
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
                    <div id="qrcode"></div>
                  </div>
              </div>
            </div>
          <!-- /column -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container -->
    </section>



    <section class="wrapper bg-light">
      <div class="container py-14 py-md-17">
        <div class="row">
          <div class="col-lg-11 col-xxl-10 mx-auto text-center">
            <h3 class="display-4 mb-10 px-lg-12 px-xl-10 px-xxl-15"> วัตถุประสงค์ </h3>
          </div>
          <!--/column -->
        </div>
        <!--/.row -->
        <div class="row">
          <div class="col-lg-7 mx-auto">
            <div id="accordion-3" class="accordion-wrapper">
              <div class="card accordion-item shadow-lg">
                <div class="card-header" id="accordion-heading-3-1">
                  <button class="collapsed" data-bs-toggle="collapse" data-bs-target="#accordion-collapse-3-1" aria-expanded="false" aria-controls="accordion-collapse-3-1">How do I get my subscription receipt?</button>
                </div>
                <!-- /.card-header -->
                <div id="accordion-collapse-3-1" class="collapse" aria-labelledby="accordion-heading-3-1" data-bs-target="#accordion-3">
                  <div class="card-body">
                    <p>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Cras mattis consectetur purus sit amet fermentum. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec sed odio dui. Cras justo odio, dapibus ac facilisis.</p>
                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.collapse -->
              </div>
              <!-- /.card -->
              <div class="card accordion-item shadow-lg">
                <div class="card-header" id="accordion-heading-3-2">
                  <button class="collapsed" data-bs-toggle="collapse" data-bs-target="#accordion-collapse-3-2" aria-expanded="false" aria-controls="accordion-collapse-3-2">Are there any discounts for people in need?</button>
                </div>
                <!-- /.card-header -->
                <div id="accordion-collapse-3-2" class="collapse" aria-labelledby="accordion-heading-3-2" data-bs-target="#accordion-3">
                  <div class="card-body">
                    <p>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Cras mattis consectetur purus sit amet fermentum. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec sed odio dui. Cras justo odio, dapibus ac facilisis.</p>
                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.collapse -->
              </div>
              <!-- /.card -->
              <div class="card accordion-item shadow-lg">
                <div class="card-header" id="accordion-heading-3-3">
                  <button class="collapsed" data-bs-toggle="collapse" data-bs-target="#accordion-collapse-3-3" aria-expanded="false" aria-controls="accordion-collapse-3-3">Do you offer a free trial edit?</button>
                </div>
                <!-- /.card-header -->
                <div id="accordion-collapse-3-3" class="collapse" aria-labelledby="accordion-heading-3-3" data-bs-target="#accordion-3">
                  <div class="card-body">
                    <p>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Cras mattis consectetur purus sit amet fermentum. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec sed odio dui. Cras justo odio, dapibus ac facilisis.</p>
                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.collapse -->
              </div>
              <!-- /.card -->
              <div class="card accordion-item shadow-lg">
                <div class="card-header" id="accordion-heading-3-4">
                  <button class="collapsed" data-bs-toggle="collapse" data-bs-target="#accordion-collapse-3-4" aria-expanded="false" aria-controls="accordion-collapse-3-4">How do I reset my Account password?</button>
                </div>
                <!-- /.card-header -->
                <div id="accordion-collapse-3-4" class="collapse" aria-labelledby="accordion-heading-3-4" data-bs-target="#accordion-3">
                  <div class="card-body">
                    <p>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Cras mattis consectetur purus sit amet fermentum. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec sed odio dui. Cras justo odio, dapibus ac facilisis.</p>
                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.collapse -->
              </div>
              <!-- /.card -->
            </div>
            <!-- /.accordion-wrapper -->
          </div>
          <!-- /column -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container -->
    </section>
    <script>
      $(document).ready(function(){
          $("#qrcode").empty().qrcode({"render": 'image',
                                      "fill": '#0e2e96',
                                      "ecLevel": 'H',
                                      "text": 'ทดสอบ',
                                      "size": 160,
                                      "radius": 0,
                                      "quiet": 1,
                                      "mode": 2,
                                      "mSize": 0.12,
                                      "mPosX": 0.5,
                                      "mPosY": 0.5,
                                      "label": 'me',
                                      "fontname": 'Prompt Regular',
                                      "fontcolor": '#0e2e96',
                                      "background": '#FFFFFF'
          });
      });
    </script>
<?php include(APP_FOOTER); ?>