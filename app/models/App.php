<?php
/**
 * App Class
 */
class App {
    private $applications = array('home'=> array(
                                    'th'=>'หน้าแรก',
                                    'en'=>'Home',
                                    'icon'=>'uil uil-house-user',
                                    'link'=>'/'
                                )
                                , 'about' => array(
                                    'th'=>'เกี่ยวกับ',
                                    'en'=>'About',
                                    'icon'=>'uil uil-meeting-board',
                                    'link'=>'/about',
                                    'dropdown'=>array('type'=>'box'
                                                    , 'item'=>array('conference'=>array(
                                                                        'th'=>'เกี่ยวกับการประชุม',
                                                                        'en'=>'About of Conference',
                                                                        'icon'=>'uil uil-clipboard-alt',
                                                                        'link'=>'/conference'
                                                                    ),
                                                                    'programs'=>array(
                                                                        'th'=>'หัวข้อการประชุม',
                                                                        'en'=>'Programs',
                                                                        'icon'=>'uil uil-clipboard-alt',
                                                                        'link'=>'/programs'
                                                                    )
                                                    )
                                    )
                                )
                                , 'contact' => array(
                                    'th'=>'ติดต่อสอบถาม',
                                    'en'=>'Contact Us',
                                    'icon'=>'uil uil-phone-volume',
                                    'link'=>'/contact'
                                )
    );

    /**
     *  Get
     *  @param  key
     *  @return value
     */
    static function lang()
    {
        return (isset($_SESSION['NICE_LANGUAGE'])?$_SESSION['NICE_LANGUAGE']:'th');
    }

    /**
     *  Profile
     *  @param  void
     *  @return htmls
     */
    static function profile()
    {
        if( isset($_SESSION['login']) ){
            $htmls = '<div class="offcanvas offcanvas-end bg-light iceahe-profile on-font-primary" id="offcanvas-info" data-bs-scroll="true" style="background:url(\''.THEME_IMG.'/map.png\') repeat-y top center;">';
                $htmls .= '<div class="offcanvas-header">';
                    $htmls .= '<h3 class="fs-30 mb-0" style="color:#372770;">'.Lang::get('Profile').'</h3>';
                    $htmls .= '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>';
                $htmls .= '</div>';
                $htmls .= '<div class="offcanvas-body d-flex flex-column">';
                    $htmls .= '<div class="shopping-cart mb-2">';
                        $htmls .= '<div class="set-profile-picture" style="width:78px;height:78px;float:left;overflow:hidden;margin-right:10px;border:1px solid #e8ecf2;border-radius:50%;-moz-border-radius:50%;-webkit-border-radius:50%;">';
                            $htmls .= '<img src="'.User::get('picture').'" onerror="this.onerror=null;this.src=\''.THEME_IMG.'/avatar.png\';" style="width:100%;" />';
                        $htmls .= '</div>';
                        $htmls .= '<div class="w-100 ms-4">';
                            $htmls .= '<h3 class="post-title fs-18 lh-xs mt-4 on-text-oneline" style="font-weight:normal;">'.trim(User::get('name').' '.User::get('surname')).'</h3>';
                            $htmls .= '<p class="price fs-sm" style="margin-top:-10px;">'.User::get('email').'</p>';
                        $htmls .= '</div>';
                    $htmls .= '</div>';
                    $htmls .= '<div class="offcanvas-footer flex-column text-center">';
                        $htmls .= '<div class="row text-center gx-0">';
                            $htmls .= '<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="float:none;width:50%;">';
                                $htmls .= '<button type="button" class="btn btn-soft-ash rounded-pill iceahe-lang'.((App::lang()=='th')?' active':null).' w-100 mb-1" onclick="runLanguage(\'th\');"><span class="underline-3 style-1 primary">&nbsp;&nbsp;TH&nbsp;&nbsp;</span>&nbsp;&nbsp;'.Lang::get('Thai').'</button>';
                            $htmls .= '</div>';
                            $htmls .= '<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="float:none;width:50%;">';
                                $htmls .= '<button type="button" class="btn btn-soft-ash rounded-pill iceahe-lang'.((App::lang()=='en')?' active':null).' w-100 mb-1" onclick="runLanguage(\'en\');"><span class="underline-3 style-1 primary">&nbsp;&nbsp;EN&nbsp;&nbsp;</span>&nbsp;&nbsp;'.Lang::get('English').'</button>';
                            $htmls .= '</div>';
                        $htmls .= '</div>';
                        $htmls .= '<button type="button" class="btn btn-danger btn-icon btn-icon-start rounded-pill w-100 mb-0" onclick="runLogout();"><i class="uil uil-exit fs-18"></i> '.Lang::get('Logout').'</button>';
                    $htmls .= '</div>';
                $htmls .= '</div>';
            $htmls .= '</div>';

            return $htmls;
        }
        return null;
    }

    /**
     *  Menus
     *  @param index
     *  @return htmls
     */ 
    static function menus($index=array()){
        if( isset($index['page'])&& in_array($index['page'], array('login','deny')) ){
            return null;
        }
        /*$htmls = '<header class="wrapper bg-soft-primary">';
            $htmls .= '<nav class="navbar navbar-expand-lg extended navbar-light navbar-bg-light caret-none">';
                $htmls .= '<div class="container flex-lg-column">';
                    $htmls .= '<div class="topbar d-flex flex-row w-100 justify-content-between align-items-center">';
                        $htmls .= '<div class="navbar-brand">';
                            $htmls .= '<a href="'.APP_HOME.'">';
                                $htmls .= '<img class="logo-dark" src="'.THEME_IMG.'/logo/dark.png?'.time().'"/>';
                                $htmls .= '<img class="logo-light" src="'.THEME_IMG.'/logo/small.png?'.time().'" />';
                                $htmls .= '<img class="logo-small" src="'.THEME_IMG.'/logo/dark.png?'.time().'"/>';
                            $htmls .= '</a>';
                        $htmls .= '</div>';
                        $htmls .= '<div class="navbar-other ms-auto">';
                            $htmls .= '<ul class="navbar-nav flex-row align-items-center">';
                                $htmls .= '<li class="nav-item language">';
                                    $htmls .= '<span'.((App::lang()=='th')?' class=active':null).' onclick="runLanguage(\'th\');"><font class="fs-18" onclick="runLanguage(\'th\');">TH</font></span>';
                                    $htmls .= '<b> | </b>';
                                    $htmls .= '<span'.((App::lang()=='en')?' class=active':null).' onclick="runLanguage(\'en\');"><font class="fs-18" onclick="runLanguage(\'en\');">EN</font></span>';
                                $htmls .= '</li>';
                                if( isset($_SESSION['login']) ){
                                    $htmls .= '<li class="nav-item">';
                                        $htmls .= '<a class="nav-link position-relative d-flex flex-row align-items-center" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-cart">';
                                            $htmls .= '<div class="iceahe-profile-box set-profile-picture"><img src="'.User::get('picture').'" onerror="this.onerror=null;this.src=\''.THEME_IMG.'/avatar.png\';"/></div>';
                                        $htmls .= '</a>';
                                    $htmls .= '</li>';
                                }else{
                                    $htmls .= '<li class="nav-item">';
                                        $htmls .= '<a href="'.APP_PATH.'/login" class="btn btn-soft-blue rounded" style="padding-left:5px;padding-right:5px;">'.Lang::get('Login').'</a>';
                                    $htmls .= '</li>';
                                }
                                $htmls .= '<li class="nav-item d-lg-none on-hamburger">';
                                    $htmls .= '<button class="hamburger offcanvas-nav-btn"><span></span></button>';
                                $htmls .= '</li>';
                            $htmls .= '</ul>';
                        $htmls .= '</div>';
                    $htmls .= '</div>';
                    $htmls .= '<div class="navbar-collapse-wrapper bg-white d-flex flex-row align-items-center">';
                        $htmls .= '<div class="navbar-collapse offcanvas offcanvas-nav offcanvas-start">';
                            $htmls .= '<div class="offcanvas-header d-lg-none">';
                                $htmls .= '<img class="logo-light" src="'.THEME_IMG.'/logo/small.png?'.time().'" style="height:72px;margin-left:-15px;"/>';
                                $htmls .= '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>';
                            $htmls .= '</div>';
                            $htmls .= '<div id="iceahe-menu" class="offcanvas-body d-flex flex-column h-100">';
                                $htmls .= '<ul class="navbar-nav">';
                                foreach( (new App())->applications as $key => $value ){
                                    if( isset($value['dropdown']) ){
                                        $htmls .= '<li class="nav-item'.((isset($index['page'])&&$index['page']==$key) ? ' active':null).' dropdown">';
                                            $htmls .= '<a class="nav-link dropdown-toggle" href="javascript:void(0);" data-bs-toggle="dropdown"><span class="nav-name"><div class="m-box-top"><i class="'.$value['icon'].'"></i></div><div class="m-box"><i class="'.$value['icon'].'"></i></div>'.$value[App::lang()].'</span></a>';
                                            if( $value['dropdown']['type']=='box' ){
                                                $htmls .= '<div class="dropdown-menu dropdown-lg">';
                                                    $htmls .= '<div class="dropdown-lg-content">';
                                                    foreach( $value['dropdown']['item'] as $ikey => $item ){
                                                        $htmls .= '<div class="iceahe-dropdown-box">';
                                                            $htmls .= '<ul class="list-unstyled">';
                                                                $htmls .= '<li>';
                                                                    $htmls .= '<a class="dropdown-item" href="'.$value['link'].$item['link'].'">';
                                                                        $htmls .= '<font><div class="m-box"><i class="'.$item['icon'].'"></i></div><span class="nav-name">'.$item[App::lang()].'</span></font>';
                                                                        $htmls .= '<div class="i-box"><i class="'.$item['icon'].'"></i></div>';
                                                                    $htmls .= '</a>';
                                                                $htmls .= '</li>';
                                                            $htmls .= '</ul>';
                                                        $htmls .= '</div>';
                                                    }
                                                    $htmls .= '</div>';
                                                $htmls .= '</div>';
                                            }else{
                                                $htmls .= '<ul class="dropdown-menu iceahe-dropdown">';
                                                foreach( $value['dropdown']['item'] as $ikey => $item ){
                                                    $htmls .= '<li class="nav-item">';
                                                        $htmls .= '<a class="dropdown-item" href="'.$value['link'].$item['link'].'">';
                                                            $htmls .= '<span class="nav-name"><div class="m-box"><i class="'.$item['icon'].'"></i></div>'.$item[App::lang()].'</span>';
                                                        $htmls .= '</a>';
                                                    $htmls .= '</li>';
                                                }
                                                $htmls .= '</ul>';
                                            }
                                        $htmls .= '</li>';
                                    }else{
                                        $htmls .= '<li class="nav-item'.((isset($index['page'])&&$index['page']==$key) ? ' active':null).'">';
                                            $htmls .= '<a href="'.$value['link'].'" class="nav-link"><span class="nav-name"><div class="m-box-top"><i class="'.$value['icon'].'"></i></div><div class="m-box"><i class="'.$value['icon'].'"></i></div>'.$value[App::lang()].'</span></a>';
                                        $htmls .= '</li>';
                                    }
                                }
                                $htmls .= '</ul>';
                                $htmls .= '<div class="offcanvas-footer d-lg-none">';
                                    $htmls .= '<div class="fs-14">';
                                        $htmls .= '<i class="uil uil-phone-volume"></i> '.APP_PHONE.'<br/>';
                                        //$htmls .= '<i class="uil uil-envelopes"></i> Emal<br/>';
                                        //$htmls .= '<a class="text-white hover" target="_blank" href="https://www.facebook.com/EDU.CMU"><i class="uil uil-facebook"></i> EDU.CMU Facebook</a>';
                                    $htmls .= '</div>';
                                $htmls .= '</div>';
                            $htmls .= '</div>';
                        $htmls .= '</div>';
                        $htmls .= '<div class="navbar-other ms-auto w-100 d-none d-lg-block">';
                            $htmls .= '<nav class="nav social social-muted justify-content-end text-end">';
                                $htmls .= '<a href="javascript::void();"'.((App::lang()=='th')?' class=active':null).' onclick="runLanguage(\'th\');" style="padding-top:2px;"><font class="fs-18">TH</font></a>';
                                $htmls .= '<a href="javascript::void();" style="padding-top:2px;"><font>|</font></a>';
                                $htmls .= '<a href="javascript::void();"'.((App::lang()=='en')?' class=active':null).' onclick="runLanguage(\'en\');" style="padding-top:2px;"><font class="fs-18">EN</font></a>';
                                if( isset($_SESSION['login']) ){
                                    $htmls .= '<a class="navbar-other-profile" href="javascript::void()" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-cart" style="margin:0 0 0 5px;">';
                                        $htmls .= '<div class="iceahe-profile-box set-profile-picture"><img src="'.User::get('picture').'" onerror="this.onerror=null;this.src=\''.THEME_IMG.'/avatar.png\';"/></div>';
                                    $htmls .= '</a>';
                                }else{
                                    $htmls .= '<a href="'.APP_PATH.'/login" class="navbar-other-profile user-login"><span class="badge bg-blue rounded-pill fs-18">'.Lang::get('Login').'</span></a>';
                                }
                            $htmls .= '</nav>';
                        $htmls .= '</div>';
                    $htmls .= '</div>';
                $htmls .= '</div>';
            $htmls .= '</nav>';
            $htmls .= App::profile();
        $htmls .= '</header>';*/

        $htmls = '<header class="wrapper bg-soft-primary">';
            $htmls .= '<nav class="navbar navbar-expand-lg classic transparent navbar-light">';
                $htmls .= '<div class="container flex-lg-row flex-nowrap align-items-center">';
                    $htmls .= '<div class="navbar-brand w-100">';
                        $htmls .= '<a href="'.APP_HOME.'">';
                            $htmls .= '<img src="'.THEME_IMG.'/logo/logo.png" srcset="'.THEME_IMG.'/logo/logo@2x.png 2x" alt="" />';
                        $htmls .= '</a>';
                    $htmls .= '</div>';
                    $htmls .= '<div class="navbar-collapse offcanvas offcanvas-nav offcanvas-start">';
                        $htmls .= '<div class="offcanvas-header d-lg-none">';
                            $htmls .= '<h3 class="text-white fs-30 mb-0">Sandbox</h3>';
                            $htmls .= '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>';
                        $htmls .= '</div>';
                        $htmls .= '<div class="offcanvas-body ms-lg-auto d-flex flex-column h-100">';
                            $htmls .= '<ul class="navbar-nav">';
                                $htmls .= '<li class="nav-item dropdown">';
                                    $htmls .= '<a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Pages</a>';
                                    $htmls .= '<ul class="dropdown-menu">';
                                        $htmls .= '<li class="dropdown dropdown-submenu dropend"><a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Services</a>';
                                            $htmls .= '<ul class="dropdown-menu">';
                                                $htmls .= '<li class="nav-item"><a class="dropdown-item" href="./services.html">Services I</a></li>';
                                                $htmls .= '<li class="nav-item"><a class="dropdown-item" href="./services2.html">Services II</a></li>';
                                            $htmls .= '</ul>';
                                        $htmls .= '</li>';
                                        $htmls .= '<li class="dropdown dropdown-submenu dropend"><a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">About</a>';
                                            $htmls .= '<ul class="dropdown-menu">';
                                                $htmls .= '<li class="nav-item"><a class="dropdown-item" href="./about.html">About I</a></li>';
                                                $htmls .= '<li class="nav-item"><a class="dropdown-item" href="./about2.html">About II</a></li>';
                                            $htmls .= '</ul>';
                                        $htmls .= '</li>';
                                        $htmls .= '<li class="nav-item"><a class="dropdown-item" href="./pricing.html">Pricing</a></li>';
                                        $htmls .= '<li class="nav-item"><a class="dropdown-item" href="./onepage.html">One Page</a></li>';
                                    $htmls .= '</ul>';
                                $htmls .= '</li>';
                                $htmls .= '<li class="nav-item dropdown">';
                                    $htmls .= '<a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Projects</a>';
                                    $htmls .= '<div class="dropdown-menu dropdown-lg">';
                                        $htmls .= '<div class="dropdown-lg-content">';
                                            $htmls .= '<div>';
                                                $htmls .= '<h6 class="dropdown-header">Project Pages</h6>';
                                                $htmls .= '<ul class="list-unstyled">';
                                                    $htmls .= '<li><a class="dropdown-item" href="./projects.html">Projects I</a></li>';
                                                    $htmls .= '<li><a class="dropdown-item" href="./projects2.html">Projects II</a></li>';
                                                    $htmls .= '<li><a class="dropdown-item" href="./projects3.html">Projects III</a></li>';
                                                    $htmls .= '<li><a class="dropdown-item" href="./projects4.html">Projects IV</a></li>';
                                                $htmls .= '</ul>';
                                            $htmls .= '</div>';
                                            $htmls .= '<div>';
                                                $htmls .= '<h6 class="dropdown-header">Single Projects</h6>';
                                                $htmls .= '<ul class="list-unstyled">';
                                                    $htmls .= '<li><a class="dropdown-item" href="./single-project.html">Single Project I</a></li>';
                                                    $htmls .= '<li><a class="dropdown-item" href="./single-project2.html">Single Project II</a></li>';
                                                    $htmls .= '<li><a class="dropdown-item" href="./single-project3.html">Single Project III</a></li>';
                                                    $htmls .= '<li><a class="dropdown-item" href="./single-project4.html">Single Project IV</a></li>';
                                                $htmls .= '</ul>';
                                            $htmls .= '</div>';
                                        $htmls .= '</div>';
                                    $htmls .= '</div>';
                                $htmls .= '</li>';
                                $htmls .= '<li class="nav-item dropdown">';
                                    $htmls .= '<a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Blog</a>';
                                    $htmls .= '<ul class="dropdown-menu">';
                                        $htmls .= '<li class="nav-item"><a class="dropdown-item" href="./blog.html">Blog without Sidebar</a></li>';
                                        $htmls .= '<li class="nav-item"><a class="dropdown-item" href="./blog2.html">Blog with Sidebar</a></li>';
                                        $htmls .= '<li class="nav-item"><a class="dropdown-item" href="./blog3.html">Blog with Left Sidebar</a></li>';
                                        $htmls .= '<li class="dropdown dropdown-submenu dropend">';
                                            $htmls .= '<a class="dropdown-item dropdown-toggle" href="#" data-bs-toggle="dropdown">Blog Posts</a>';
                                            $htmls .= '<ul class="dropdown-menu">';
                                                $htmls .= '<li class="nav-item"><a class="dropdown-item" href="./blog-post.html">Post without Sidebar</a></li>';
                                                $htmls .= '<li class="nav-item"><a class="dropdown-item" href="./blog-post2.html">Post with Sidebar</a></li>';
                                                $htmls .= '<li class="nav-item"><a class="dropdown-item" href="./blog-post3.html">Post with Left Sidebar</a></li>';
                                            $htmls .= '</ul>';
                                        $htmls .= '</li>';
                                    $htmls .= '</ul>';
                                $htmls .= '</li>';
                                $htmls .= '<li class="nav-item dropdown dropdown-mega">';
                                    $htmls .= '<a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Documentation</a>';
                                    $htmls .= '<ul class="dropdown-menu mega-menu">';
                                        $htmls .= '<li class="mega-menu-content">';
                                            $htmls .= '<div class="row gx-0 gx-lg-3">';
                                                $htmls .= '<div class="col-lg-4">';
                                                    $htmls .= '<h6 class="dropdown-header">Usage</h6>';
                                                    $htmls .= '<ul class="list-unstyled cc-2 pb-lg-1">';
                                                        $htmls .= '<li><a class="dropdown-item" href="./docs/index.html">Get Started</a></li>';
                                                        $htmls .= '<li><a class="dropdown-item" href="./docs/forms.html">Forms</a></li>';
                                                        $htmls .= '<li><a class="dropdown-item" href="./docs/faq.html">FAQ</a></li>';
                                                        $htmls .= '<li><a class="dropdown-item" href="./docs/changelog.html">Changelog</a></li>';
                                                        $htmls .= '<li><a class="dropdown-item" href="./docs/credits.html">Credits</a></li>';
                                                    $htmls .= '</ul>';
                                                $htmls .= '</div>';
                                                $htmls .= '<div class="col-lg-8">';
                                                    $htmls .= '<h6 class="dropdown-header">Elements</h6>';
                                                    $htmls .= '<ul class="list-unstyled cc-3">';
                                                        $htmls .= '<li><a class="dropdown-item" href="./docs/elements/accordion.html">Accordion</a></li>';
                                                        $htmls .= '<li><a class="dropdown-item" href="./docs/elements/alerts.html">Alerts</a></li>';
                                                        $htmls .= '<li><a class="dropdown-item" href="./docs/elements/animations.html">Animations</a></li>';
                                                        $htmls .= '<li><a class="dropdown-item" href="./docs/elements/avatars.html">Avatars</a></li>';
                                                        $htmls .= '<li><a class="dropdown-item" href="./docs/elements/background.html">Background</a></li>';
                                                    $htmls .= '</ul>';
                                                $htmls .= '</div>';
                                            $htmls .= '</div>';
                                        $htmls .= '</li>';
                                    $htmls .= '</ul>';
                                $htmls .= '</li>';
                            $htmls .= '</ul>';
                            $htmls .= '<div class="offcanvas-footer d-lg-none">';
                                $htmls .= '<div>';
                                    $htmls .= '<a href="mailto:first.last@email.com" class="link-inverse">info@email.com</a>';
                                    $htmls .= '<br /> 00 (123) 456 78 90 <br />';
                                    $htmls .= '<nav class="nav social social-white mt-4">';
                                        $htmls .= '<a href="#"><i class="uil uil-twitter"></i></a>';
                                        $htmls .= '<a href="#"><i class="uil uil-facebook-f"></i></a>';
                                        $htmls .= '<a href="#"><i class="uil uil-dribbble"></i></a>';
                                        $htmls .= '<a href="#"><i class="uil uil-instagram"></i></a>';
                                        $htmls .= '<a href="#"><i class="uil uil-youtube"></i></a>';
                                    $htmls .= '</nav>';
                                $htmls .= '</div>';
                            $htmls .= '</div>';
                        $htmls .= '</div>';
                    $htmls .= '</div>';
                    $htmls .= '<div class="navbar-other ms-lg-4">';
                        $htmls .= '<ul class="navbar-nav flex-row align-items-center ms-auto">';
                            if( isset($_SESSION['login']) ){
                                $htmls .= '<li class="nav-item">';
                                    $htmls .= '<a class="nav-link" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-info">';
                                        $htmls .= '<div style="width:48px;overflow:hidden;border-radius:50%;-moz-border-radius:50%;-webkit-border-radius:50%;"><img src="'.User::get('picture').'" onerror="this.onerror=null;this.src=\''.THEME_IMG.'/avatar.png\';" style="width:100%;" /></div>';
                                    $htmls .= '</a>';
                                $htmls .= '</li>';
                            }else{
                                $htmls .= '<li class="nav-item d-none d-md-block">';
                                    $htmls .= '<a href="/login" class="btn btn-sm btn-primary rounded-pill">Login</a>';
                                $htmls .= '</li>';
                            }
                            $htmls .= '<li class="nav-item d-lg-none">';
                                $htmls .= '<button class="hamburger offcanvas-nav-btn"><span></span></button>';
                            $htmls .= '</li>';
                        $htmls .= '</ul>';
                    $htmls .= '</div>';
                $htmls .= '</div>';
            $htmls .= '</nav>';
            $htmls .= App::profile();
        $htmls .= '</header>';


        return $htmls;
    }

    /**
     *  Footer
     *  @param  index
     *  @return htmls
     */
    static function footer($index=array())
    {
        if( isset($index['page'])&& in_array($index['page'], array('login','deny')) ){
            return null;
        }else if( isset($_SESSION['deny']) ){
            unset($_SESSION['deny']);
        }
        $htmls = '<footer class="bg-dark text-inverse">';
            $htmls .= '<div class="container py-7 py-md-7">';
                $htmls .= '<div class="row gy-6 gy-lg-0">';
                    $htmls .= '<div class="col-md-4 col-lg-3">';
                        $htmls .= '<div class="widget">';
                            $htmls .= '<img class="mb-4" src="'.THEME_IMG.'/logo/logo-light.png" srcset="'.THEME_IMG.'/logo/logo-light@2x.png 2x" alt="" />';
                            $htmls .= '<p class="mb-4">© 2022 Sandbox. <br class="d-none d-lg-block" />All rights reserved.</p>';
                        $htmls .= '</div>';
                    $htmls .= '</div>';
                    $htmls .= '<div class="col-md-4 col-lg-6">';
                        $htmls .= '<div class="widget">';
                            $htmls .= '<h4 class="widget-title text-white mb-3">Contact Us</h4>';
                            $htmls .= '<address class="pe-xl-15 pe-xxl-17">';
                                $htmls .= '<div class="on-text-oneline">'.( (App::lang()=='en') ? APP_FACT_EN : APP_FACT_TH ).'</div>';
                                $htmls .= '<div style="padding-left:18px;text-indent:-21px;">'.( (App::lang()=='en') ? '<i class="uil uil-location-pin-alt"></i> '.APP_ADDR_EN : '<i class="uil uil-location-pin-alt"></i> '.APP_ADDR_TH ).'</div>';
                            $htmls .= '</address>';
                        $htmls .= '</div>';
                    $htmls .= '</div>';
                    $htmls .= '<div class="col-md-12 col-lg-3">';
                        $htmls .= '<div class="widget">';
                            $htmls .= '<h4 class="widget-title text-white mb-3">Telephone</h4>';
                            $htmls .= '<p class="mb-5"><i class="uil uil-phone-volume"></i> '.APP_PHONE.'</p>';
                        $htmls .= '</div>';
                    $htmls .= '</div>';
                $htmls .= '</div>';
            $htmls .= '</div>';
        $htmls .= '</footer>';

        return $htmls;
    }

}
?>