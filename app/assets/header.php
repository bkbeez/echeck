<!DOCTYPE html>
<html lang="<?=App::lang()?>">
    <head app-lang="<?=App::lang()?>" app-path="<?=APP_PATH?>">
        <meta charset="utf-8" />
        <meta name="keywords" content="<?=APP_CODE?>,EDU CMU">
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
        <link rel="apple-touch-icon-precomposed" href="<?=APP_PATH?>/favicon.png" />
        <link rel="stylesheet" href="<?=THEME_CSS?>/plugins.css">
        <link rel="stylesheet" href="<?=THEME_CSS?>/style.css">
        <link rel="stylesheet" href="<?=THEME_JS?>/sweetalert/sweetalert2.min.css" />
        <script type="text/javascript" src="<?=THEME_JS?>/jquery-2.1.1.js"></script>
        <script type="text/javascript" src="<?=THEME_JS?>/jquery.form.min.js"></script>
        <script type="text/javascript" src="<?=THEME_JS?>/sweetalert/sweetalert2.min.js"></script>
        <script type="text/javascript" src="<?=THEME_JS?>/qrcode/html5-qrcode.min.js?<?=time()?>"></script>
        <script type="text/javascript" src="<?=THEME_JS?>/qrcode/jquery.qrcode-0.12.0.min.js"></script>
        <script type="text/javascript" src="<?=THEME_JS?>/index.js?<?=time()?>"></script>
    </head>
    <body>
        <div class="page-loader"></div>
        <div class="content-wrapper on-font-primary">
        <!-- Body -->
        <header class="wrapper bg-soft-primary">
            <nav class="navbar navbar-expand-lg classic transparent navbar-light">
                <div class="container flex-lg-row flex-nowrap align-items-center">
                    <div class="navbar-brand w-100">
                        <a href="./index.html">
                            <img src="<?=THEME_IMG?>/logo/logo.png" srcset="<?=THEME_IMG?>/logo/logo@2x.png 2x" alt="" />
                        </a>
                    </div>
                    <div class="navbar-collapse offcanvas offcanvas-nav offcanvas-start">
                        <div class="offcanvas-header d-lg-none">
                            <h3 class="text-white fs-30 mb-0">Sandbox</h3>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body ms-lg-auto d-flex flex-column h-100">
                            <ul class="navbar-nav">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Pages</a>
                                    <ul class="dropdown-menu">
                                        <li class="dropdown dropdown-submenu dropend"><a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Services</a>
                                            <ul class="dropdown-menu">
                                                <li class="nav-item"><a class="dropdown-item" href="./services.html">Services I</a></li>
                                                <li class="nav-item"><a class="dropdown-item" href="./services2.html">Services II</a></li>
                                            </ul>
                                        </li>
                                        <li class="dropdown dropdown-submenu dropend"><a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">About</a>
                                            <ul class="dropdown-menu">
                                                <li class="nav-item"><a class="dropdown-item" href="./about.html">About I</a></li>
                                                <li class="nav-item"><a class="dropdown-item" href="./about2.html">About II</a></li>
                                            </ul>
                                        </li>
                                        <li class="dropdown dropdown-submenu dropend"><a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Shop</a>
                                            <ul class="dropdown-menu">
                                                <li class="nav-item"><a class="dropdown-item" href="./shop.html">Shop I</a></li>
                                                <li class="nav-item"><a class="dropdown-item" href="./shop2.html">Shop II</a></li>
                                                <li class="nav-item"><a class="dropdown-item" href="./shop-product.html">Product Page</a></li>
                                                <li class="nav-item"><a class="dropdown-item" href="./shop-cart.html">Shopping Cart</a></li>
                                                <li class="nav-item"><a class="dropdown-item" href="./shop-checkout.html">Checkout</a></li>
                                            </ul>
                                        </li>
                                        <li class="dropdown dropdown-submenu dropend"><a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Contact</a>
                                            <ul class="dropdown-menu">
                                                <li class="nav-item"><a class="dropdown-item" href="./contact.html">Contact I</a></li>
                                                <li class="nav-item"><a class="dropdown-item" href="./contact2.html">Contact II</a></li>
                                                <li class="nav-item"><a class="dropdown-item" href="./contact3.html">Contact III</a></li>
                                            </ul>
                                        </li>
                                        <li class="dropdown dropdown-submenu dropend"><a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Career</a>
                                            <ul class="dropdown-menu">
                                                <li class="nav-item"><a class="dropdown-item" href="./career.html">Job Listing I</a></li>
                                                <li class="nav-item"><a class="dropdown-item" href="./career2.html">Job Listing II</a></li>
                                                <li class="nav-item"><a class="dropdown-item" href="./career-job.html">Job Description</a></li>
                                            </ul>
                                        </li>
                                        <li class="dropdown dropdown-submenu dropend"><a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Utility</a>
                                            <ul class="dropdown-menu">
                                                <li class="nav-item"><a class="dropdown-item" href="./404.html">404 Not Found</a></li>
                                                <li class="nav-item"><a class="dropdown-item" href="./page-loader.html">Page Loader</a></li>
                                                <li class="nav-item"><a class="dropdown-item" href="./signin.html">Sign In I</a></li>
                                                <li class="nav-item"><a class="dropdown-item" href="./signin2.html">Sign In II</a></li>
                                                <li class="nav-item"><a class="dropdown-item" href="./signup.html">Sign Up I</a></li>
                                                <li class="nav-item"><a class="dropdown-item" href="./signup2.html">Sign Up II</a></li>
                                                <li class="nav-item"><a class="dropdown-item" href="./terms.html">Terms</a></li>
                                            </ul>
                                        </li>
                                        <li class="nav-item"><a class="dropdown-item" href="./pricing.html">Pricing</a></li>
                                        <li class="nav-item"><a class="dropdown-item" href="./onepage.html">One Page</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Projects</a>
                                <div class="dropdown-menu dropdown-lg">
                                    <div class="dropdown-lg-content">
                                        <div>
                                            <h6 class="dropdown-header">Project Pages</h6>
                                            <ul class="list-unstyled">
                                                <li><a class="dropdown-item" href="./projects.html">Projects I</a></li>
                                                <li><a class="dropdown-item" href="./projects2.html">Projects II</a></li>
                                                <li><a class="dropdown-item" href="./projects3.html">Projects III</a></li>
                                                <li><a class="dropdown-item" href="./projects4.html">Projects IV</a></li>
                                            </ul>
                                        </div>
                                        <div>
                                            <h6 class="dropdown-header">Single Projects</h6>
                                            <ul class="list-unstyled">
                                                <li><a class="dropdown-item" href="./single-project.html">Single Project I</a></li>
                                                <li><a class="dropdown-item" href="./single-project2.html">Single Project II</a></li>
                                                <li><a class="dropdown-item" href="./single-project3.html">Single Project III</a></li>
                                                <li><a class="dropdown-item" href="./single-project4.html">Single Project IV</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Blog</a>
                                    <ul class="dropdown-menu">
                                        <li class="nav-item"><a class="dropdown-item" href="./blog.html">Blog without Sidebar</a></li>
                                        <li class="nav-item"><a class="dropdown-item" href="./blog2.html">Blog with Sidebar</a></li>
                                        <li class="nav-item"><a class="dropdown-item" href="./blog3.html">Blog with Left Sidebar</a></li>
                                        <li class="dropdown dropdown-submenu dropend">
                                            <a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Blog Posts</a>
                                            <ul class="dropdown-menu">
                                                <li class="nav-item"><a class="dropdown-item" href="./blog-post.html">Post without Sidebar</a></li>
                                                <li class="nav-item"><a class="dropdown-item" href="./blog-post2.html">Post with Sidebar</a></li>
                                                <li class="nav-item"><a class="dropdown-item" href="./blog-post3.html">Post with Left Sidebar</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown dropdown-mega">
                                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Documentation</a>
                                    <ul class="dropdown-menu mega-menu">
                                        <li class="mega-menu-content">
                                            <div class="row gx-0 gx-lg-3">
                                                <div class="col-lg-4">
                                                    <h6 class="dropdown-header">Usage</h6>
                                                    <ul class="list-unstyled cc-2 pb-lg-1">
                                                        <li><a class="dropdown-item" href="./docs/index.html">Get Started</a></li>
                                                        <li><a class="dropdown-item" href="./docs/forms.html">Forms</a></li>
                                                        <li><a class="dropdown-item" href="./docs/faq.html">FAQ</a></li>
                                                        <li><a class="dropdown-item" href="./docs/changelog.html">Changelog</a></li>
                                                        <li><a class="dropdown-item" href="./docs/credits.html">Credits</a></li>
                                                    </ul>
                                                    <h6 class="dropdown-header mt-lg-6">Styleguide</h6>
                                                    <ul class="list-unstyled cc-2">
                                                        <li><a class="dropdown-item" href="./docs/styleguide/colors.html">Colors</a></li>
                                                        <li><a class="dropdown-item" href="./docs/styleguide/fonts.html">Fonts</a></li>
                                                        <li><a class="dropdown-item" href="./docs/styleguide/icons-svg.html">SVG Icons</a></li>
                                                        <li><a class="dropdown-item" href="./docs/styleguide/icons-font.html">Font Icons</a></li>
                                                        <li><a class="dropdown-item" href="./docs/styleguide/illustrations.html">Illustrations</a></li>
                                                        <li><a class="dropdown-item" href="./docs/styleguide/backgrounds.html">Backgrounds</a></li>
                                                        <li><a class="dropdown-item" href="./docs/styleguide/misc.html">Misc</a></li>
                                                    </ul>
                                                </div>
                                                <div class="col-lg-8">
                                                    <h6 class="dropdown-header">Elements</h6>
                                                    <ul class="list-unstyled cc-3">
                                                        <li><a class="dropdown-item" href="./docs/elements/accordion.html">Accordion</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/alerts.html">Alerts</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/animations.html">Animations</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/avatars.html">Avatars</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/background.html">Background</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/badges.html">Badges</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/buttons.html">Buttons</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/card.html">Card</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/carousel.html">Carousel</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/dividers.html">Dividers</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/form-elements.html">Form Elements</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/image-hover.html">Image Hover</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/image-mask.html">Image Mask</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/lightbox.html">Lightbox</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/player.html">Media Player</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/modal.html">Modal</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/pagination.html">Pagination</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/progressbar.html">Progressbar</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/shadows.html">Shadows</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/shapes.html">Shapes</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/tables.html">Tables</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/tabs.html">Tabs</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/text-animations.html">Text Animations</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/text-highlight.html">Text Highlight</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/tiles.html">Tiles</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/tooltips-popovers.html">Tooltips & Popovers</a></li>
                                                        <li><a class="dropdown-item" href="./docs/elements/typography.html">Typography</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                            <div class="offcanvas-footer d-lg-none">
                                <div>
                                    <a href="mailto:first.last@email.com" class="link-inverse">info@email.com</a>
                                    <br /> 00 (123) 456 78 90 <br />
                                    <nav class="nav social social-white mt-4">
                                        <a href="#"><i class="uil uil-twitter"></i></a>
                                        <a href="#"><i class="uil uil-facebook-f"></i></a>
                                        <a href="#"><i class="uil uil-dribbble"></i></a>
                                        <a href="#"><i class="uil uil-instagram"></i></a>
                                        <a href="#"><i class="uil uil-youtube"></i></a>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="navbar-other ms-lg-4">
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-info"><i class="uil uil-info-circle"></i></a></li>
                            <li class="nav-item d-none d-md-block">
                                <a href="/login" class="btn btn-sm btn-primary rounded-pill">Login</a>
                            </li>
                            <li class="nav-item d-lg-none">
                                <button class="hamburger offcanvas-nav-btn"><span></span></button>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="offcanvas offcanvas-end text-inverse" id="offcanvas-info" data-bs-scroll="true">
                <div class="offcanvas-header">
                    <h3 class="text-white fs-30 mb-0">Sandbox</h3>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body pb-6">
                    <div class="widget mb-8">
                        <p>Sandbox is a multipurpose HTML5 template with various layouts which will be a great solution for your business.</p>
                    </div>
                    <div class="widget mb-8">
                        <h4 class="widget-title text-white mb-3">Contact Info</h4>
                        <address> Moonshine St. 14/05 <br /> Light City, London </address>
                        <a href="mailto:first.last@email.com">info@email.com</a><br /> 00 (123) 456 78 90
                    </div>
                    <div class="widget mb-8">
                        <h4 class="widget-title text-white mb-3">Learn More</h4>
                        <ul class="list-unstyled">
                            <li><a href="#">Our Story</a></li>
                            <li><a href="#">Terms of Use</a></li>
                            <li><a href="#">Privacy Policy</a></li>
                            <li><a href="#">Contact Us</a></li>
                        </ul>
                    </div>
                    <div class="widget">
                        <h4 class="widget-title text-white mb-3">Follow Us</h4>
                        <nav class="nav social social-white">
                            <a href="#"><i class="uil uil-twitter"></i></a>
                            <a href="#"><i class="uil uil-facebook-f"></i></a>
                            <a href="#"><i class="uil uil-dribbble"></i></a>
                            <a href="#"><i class="uil uil-instagram"></i></a>
                            <a href="#"><i class="uil uil-youtube"></i></a>
                        </nav>
                    </div>
                </div>
            </div>
        </header>