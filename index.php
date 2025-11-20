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
            <div class="d-flex justify-content-center justify-content-lg-start" data-cues="slideInDown" data-group="page-title-buttons" data-delay="900">
              <span><a href="event" class="btn btn-lg btn-primary rounded me-2"> รายการกิจกรรม </a></span>
              <span><a href="participants" class="btn btn-lg btn-primary rounded me-2"> รายชื่อผู้เข้าร่วมกิจกรรม </a></span>

            </div>
            <div class="d-flex justify-content-center justify-content-lg-start mt-2" data-cues="slideInDown" data-group="page-title-buttons" data-delay="900">
              <span><a href="participants/staff_register.php" class="btn btn-lg btn-primary rounded me-2"> ลงทะเบียนเข้าร่วมสำหรับเจ้าหน้าที่ (Staff) </a></span>
            </div>
          </div>
          <!-- /column -->
          <div class="col-lg-7">
            <div class="row">
              <div class="col-3 offset-1 offset-lg-0 col-lg-4 d-flex flex-column" data-cues="zoomIn" data-group="col-start" data-delay="300">
                <div class="ms-auto mt-auto"><img class="img-fluid rounded shadow-lg" src="<?=THEME_IMG?>/photos/sa20.jpg" srcset="<?=THEME_IMG?>/photos/sa20@2x.jpg 2x" alt="" /></div>
                <div class="ms-auto mt-5 mb-10"><img class="img-fluid rounded shadow-lg" src="<?=THEME_IMG?>/photos/sa18.jpg" srcset="<?=THEME_IMG?>/photos/sa18@2x.jpg 2x" alt="" /></div>
              </div>
              <!-- /column -->
              <div class="col-4 col-lg-5" data-cue="zoomIn">
                <div><img class="w-100 img-fluid rounded shadow-lg" src="<?=THEME_IMG?>/photos/sa16.jpg" srcset="<?=THEME_IMG?>/photos/sa16@2x.jpg 2x" alt="" /></div>
              </div>
              <!-- /column -->
              <div class="col-3 d-flex flex-column" data-cues="zoomIn" data-group="col-end" data-delay="300">
                <div class="mt-auto"><img class="img-fluid rounded shadow-lg" src="<?=THEME_IMG?>/photos/sa21.jpg" srcset="<?=THEME_IMG?>/photos/sa21@2x.jpg 2x" alt="" /></div>
                <div class="mt-5"><img class="img-fluid rounded shadow-lg" src="<?=THEME_IMG?>/photos/sa19.jpg" srcset="<?=THEME_IMG?>/photos/sa19@2x.jpg 2x" alt="" /></div>
                <div class="mt-5 mb-10"><img class="img-fluid rounded shadow-lg" src="<?=THEME_IMG?>/photos/sa17.jpg" srcset="<?=THEME_IMG?>/photos/sa17@2x.jpg 2x" alt="" /></div>
              </div>
              <!-- /column -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /column -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container -->
    </section>

    <section class="wrapper bg-light">
      <div class="container py-14 py-md-17">
        <!--/.row -->
        <div class="row gy-6 align-items-center">
          <div class="col-lg-4">
            <h2 class="fs-15 text-uppercase text-muted mb-3">Our Pricing</h2>
            <h3 class="display-4 mb-5">We offer great and premium prices.</h3>
            <p class="mb-5">Enjoy a <a href="#" class="hover">free 30-day trial</a> and experience the full service. No credit card required!</p>
            <a href="#" class="btn btn-primary rounded mt-2">See All Prices</a>
          </div>
          <!--/column -->
          <div class="col-lg-7 offset-lg-1 pricing-wrapper">
            <div class="pricing-switcher-wrapper switcher justify-content-start justify-content-lg-end">
              <p class="mb-0 pe-3">Monthly</p>
              <div class="pricing-switchers">
                <div class="pricing-switcher pricing-switcher-active"></div>
                <div class="pricing-switcher"></div>
                <div class="switcher-button bg-primary"></div>
              </div>
              <p class="mb-0 ps-3">Yearly <span class="text-red">(Save 30%)</span></p>
            </div>
            <div class="row gy-6 mt-5">
              <div class="col-md-6">
                <div class="pricing card shadow-lg">
                  <div class="card-body pb-12">
                    <div class="prices text-dark">
                      <div class="price price-show justify-content-start"><span class="price-currency">$</span><span class="price-value">19</span> <span class="price-duration">mo</span></div>
                      <div class="price price-hide price-hidden justify-content-start"><span class="price-currency">$</span><span class="price-value">199</span> <span class="price-duration">yr</span></div>
                    </div>
                    <!--/.prices -->
                    <h4 class="card-title mt-2">Premium Plan</h4>
                    <ul class="icon-list bullet-bg bullet-soft-primary mt-7 mb-8">
                      <li><i class="uil uil-check"></i><span><strong>5</strong> Projects </span></li>
                      <li><i class="uil uil-check"></i><span><strong>100K</strong> API Access </span></li>
                      <li><i class="uil uil-check"></i><span><strong>200MB</strong> Storage </span></li>
                      <li><i class="uil uil-check"></i><span> Weekly <strong>Reports</strong></span></li>
                      <li><i class="uil uil-times bullet-soft-red"></i><span> 7/24 <strong>Support</strong></span></li>
                    </ul>
                    <a href="#" class="btn btn-primary rounded">Choose Plan</a>
                  </div>
                  <!--/.card-body -->
                </div>
                <!--/.pricing -->
              </div>
              <!--/column -->
              <div class="col-md-6 popular">
                <div class="pricing card shadow-lg">
                  <div class="card-body pb-12">
                    <div class="prices text-dark">
                      <div class="price price-show justify-content-start"><span class="price-currency">$</span><span class="price-value">49</span> <span class="price-duration">mo</span></div>
                      <div class="price price-hide price-hidden justify-content-start"><span class="price-currency">$</span><span class="price-value">499</span> <span class="price-duration">yr</span></div>
                    </div>
                    <!--/.prices -->
                    <h4 class="card-title mt-2">Corporate Plan</h4>
                    <ul class="icon-list bullet-bg bullet-soft-primary mt-7 mb-8">
                      <li><i class="uil uil-check"></i><span><strong>20</strong> Projects </span></li>
                      <li><i class="uil uil-check"></i><span><strong>300K</strong> API Access </span></li>
                      <li><i class="uil uil-check"></i><span><strong>500MB</strong> Storage </span></li>
                      <li><i class="uil uil-check"></i><span> Weekly <strong>Reports</strong></span></li>
                      <li><i class="uil uil-check"></i><span> 7/24 <strong>Support</strong></span></li>
                    </ul>
                    <a href="#" class="btn btn-primary rounded">Choose Plan</a>
                  </div>
                  <!--/.card-body -->
                </div>
                <!--/.pricing -->
              </div>
              <!--/column -->
            </div>
            <!--/.row -->
          </div>
          <!--/column -->
        </div>
        <!--/.row -->
      </div>
      <!-- /.container -->
    </section>


    <section class="wrapper bg-soft-primary">
      <div class="container py-14 py-md-17">
        <div class="row">
          <div class="col-lg-11 col-xxl-10 mx-auto text-center">
            <h2 class="fs-15 text-uppercase text-muted mb-3">FAQ</h2>
            <h3 class="display-4 mb-10 px-lg-12 px-xl-10 px-xxl-15">If you don't see an answer to your question, you can send us an email from our contact form.</h3>
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
<?php include(APP_FOOTER); ?>