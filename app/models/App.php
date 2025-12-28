<?php
/**
 * App Class
 */
class App {

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
                    $htmls .= '<h3 class="fs-30 mb-0 text-primary">'.Lang::get('Profile').'</h3>';
                    $htmls .= '<button type="button" class="btn-close btn-close-dark" data-bs-dismiss="offcanvas" aria-label="Close"></button>';
                $htmls .= '</div>';
                $htmls .= '<div class="offcanvas-body d-flex flex-column">';
                    $htmls .= '<div class="shopping-cart">';
                        $htmls .= '<div class="set-profile-picture" style="width:64px;height:64px;float:left;overflow:hidden;margin-right:10px;border:1px solid #e8ecf2;border-radius:50%;-moz-border-radius:50%;-webkit-border-radius:50%;">';
                            $htmls .= '<img src="'.User::get('picture').'" onerror="this.onerror=null;this.src=\''.THEME_IMG.'/avatar.png\';" style="width:100%;" />';
                        $htmls .= '</div>';
                        $htmls .= '<div class="w-100 ms-4">';
                            $htmls .= '<h3 class="post-title fs-18 lh-xs mt-2 on-text-oneline" style="font-weight:normal;">'.trim(User::get('name').' '.User::get('surname')).'</h3>';
                            $htmls .= '<p class="price fs-sm" style="margin-top:-10px;">'.User::get('email').'</p>';
                        $htmls .= '</div>';
                    $htmls .= '</div>';
                    $htmls .= '<div class="offcanvas-footer flex-column text-center">';
                        /*$htmls .= '<div class="row text-center gx-0">';
                            $htmls .= '<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="float:none;width:50%;">';
                                $htmls .= '<button type="button" class="btn btn-soft-ash rounded-pill iceahe-lang'.((App::lang()=='th')?' active':null).' w-100 mb-1" onclick="runLanguage(\'th\');"><span class="underline-3 style-1 primary">&nbsp;&nbsp;TH&nbsp;&nbsp;</span>&nbsp;&nbsp;'.Lang::get('Thai').'</button>';
                            $htmls .= '</div>';
                            $htmls .= '<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="float:none;width:50%;">';
                                $htmls .= '<button type="button" class="btn btn-soft-ash rounded-pill iceahe-lang'.((App::lang()=='en')?' active':null).' w-100 mb-1" onclick="runLanguage(\'en\');"><span class="underline-3 style-1 primary">&nbsp;&nbsp;EN&nbsp;&nbsp;</span>&nbsp;&nbsp;'.Lang::get('English').'</button>';
                            $htmls .= '</div>';
                        $htmls .= '</div>';*/
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
        $htmls = '<header class="wrapper bg-primary">';
            $htmls .= '<nav class="navbar navbar-expand-lg classic transparent navbar-light">';
                $htmls .= '<div class="container flex-lg-row flex-nowrap align-items-center">';
                    $htmls .= '<div class="navbar-brand w-100">';
                        $htmls .= '<a href="'.APP_HOME.'">';
                            $htmls .= '<img class="on-light"src="'.THEME_IMG.'/logo/logo-light.png" srcset="'.THEME_IMG.'/logo/logo-light@2x.png 2x" alt=""/>';
                            $htmls .= '<img class="on-color" src="'.THEME_IMG.'/logo/logo.png" srcset="'.THEME_IMG.'/logo/logo@2x.png 2x" alt=""/>';
                        $htmls .= '</a>';
                    $htmls .= '</div>';
                    $htmls .= '<div class="navbar-collapse offcanvas offcanvas-nav offcanvas-start bg-primary">';
                        $htmls .= '<div class="offcanvas-header d-lg-none">';
                            $htmls .= '<img src="'.THEME_IMG.'/logo/logo-light.png" srcset="'.THEME_IMG.'/logo/logo-light@2x.png 2x" alt="" style="height:72px;"/>';
                            $htmls .= '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>';
                        $htmls .= '</div>';
                        $htmls .= '<div id="mainsite-navbar" class="offcanvas-body ms-lg-auto d-flex flex-column h-100">';
                            $htmls .= '<ul class="navbar-nav">';
                            if( Auth::check() ){
                                    $htmls .= '<li class="nav-item'.((isset($index['page'])&&$index['page']=='events') ? ' active':null).' dropdown">';
                                        $htmls .= '<a class="nav-link dropdown-toggle" href="javascript:void(0);" data-bs-toggle="dropdown"><span class="nav-name"><div class="m-box-top"><i class="uil uil-user-nurse"></i></div><div class="m-box"><i class="uil uil-user-nurse"></i></div>'.Lang::get('Officer').'</span></a>';
                                        $htmls .= '<ul class="dropdown-menu mainsite-dropdown'.((isset($index['page'])&&$index['page']=='events') ? ' show':null).'">';
                                            $htmls .= '<li class="nav-item'.((isset($index['view'])&&$index['view']=='lists') ? ' active':null).'">';
                                                $htmls .= '<a class="dropdown-item" href="'.APP_HOME.'/events">';
                                                    $htmls .= '<span class="nav-name"><div class="m-box"><i class="uil uil-edit"></i></div>จัดการกิจกรรม</span>';
                                                $htmls .= '</a>';
                                            $htmls .= '</li>';
                                            $htmls .= '<li class="nav-item'.((isset($index['view'])&&$index['view']=='participants') ? ' active':null).'">';
                                                $htmls .= '<a class="dropdown-item" href="'.APP_HOME.'/participants">';
                                                    $htmls .= '<span class="nav-name"><div class="m-box"><i class="uil uil-users-alt"></i></div>รายชื่อผู้เข้าร่วม</span>';
                                                $htmls .= '</a>';
                                            $htmls .= '</li>';
                                            $htmls .= '<li class="nav-item'.((isset($index['view'])&&$index['view']=='user_register') ? ' active':null).'">';
                                                $htmls .= '<a class="dropdown-item" href="'.APP_HOME.'/participants/user_register.php">';
                                                    $htmls .= '<span class="nav-name"><div class="m-box"><i class="uil uil-qrcode-scan"></i></div>ลงทะเบียน USER</span>';
                                                $htmls .= '</a>';
                                            $htmls .= '</li>';
                                            $htmls .= '<li class="nav-item'.((isset($index['view'])&&$index['view']=='staff_register') ? ' active':null).'">';
                                                $htmls .= '<a class="dropdown-item" href="'.APP_HOME.'/participants/staff_register.php">';
                                                    $htmls .= '<span class="nav-name"><div class="m-box"><i class="uil uil-desktop"></i></div>ลงทะเบียน STAFF</span>';
                                                $htmls .= '</a>';
                                            $htmls .= '</li>';
                                        $htmls .= '</ul>';
                                    $htmls .= '</li>';
                                    if( Auth::admin() ){
                                        $htmls .= '<li class="nav-item'.((isset($index['page'])&&$index['page']=='admin') ? ' active':null).' dropdown">';
                                            $htmls .= '<a class="nav-link dropdown-toggle" href="javascript:void(0);" data-bs-toggle="dropdown"><span class="nav-name"><div class="m-box-top"><i class="uil uil-user-md"></i></div><div class="m-box"><i class="uil uil-user-md"></i></div>'.Lang::get('Administrator').'</span></a>';
                                            $htmls .= '<ul class="dropdown-menu mainsite-dropdown'.((isset($index['page'])&&$index['page']=='admin') ? ' show':null).'">';
                                                $htmls .= '<li class="nav-item'.((isset($index['view'])&&$index['view']=='users') ? ' active':null).'">';
                                                    $htmls .= '<a class="dropdown-item" href="'.APP_HOME.'/admin/?users">';
                                                        $htmls .= '<span class="nav-name"><div class="m-box"><i class="uil uil-users-alt"></i></div>จัดการบัญชีผู้ใช้</span>';
                                                    $htmls .= '</a>';
                                                $htmls .= '</li>';
                                                /*$htmls .= '<li class="nav-item'.((isset($index['view'])&&$index['view']=='sessions') ? ' active':null).'">';
                                                    $htmls .= '<a class="dropdown-item" href="'.APP_HOME.'/admin/?sessions">';
                                                        $htmls .= '<span class="nav-name"><div class="m-box"><i class="uil uil-transaction"></i></div>View Sessions</span>';
                                                    $htmls .= '</a>';
                                                $htmls .= '</li>';*/
                                                $htmls .= '<li class="nav-item'.((isset($index['view'])&&$index['view']=='logs') ? ' active':null).'">';
                                                    $htmls .= '<a class="dropdown-item" href="'.APP_HOME.'/admin/?logs">';
                                                        $htmls .= '<span class="nav-name"><div class="m-box"><i class="uil uil-transaction"></i></div>ตรวจสอบ Logs</span>';
                                                    $htmls .= '</a>';
                                                $htmls .= '</li>';
                                            $htmls .= '</ul>';
                                        $htmls .= '</li>';
                                    }
                            }
                            $htmls .= '</ul>';
                            $htmls .= '<div class="offcanvas-footer d-lg-none">';
                                $htmls .= '<div>';
                                    $htmls .= '<i class="uil uil-phone-volume"></i> '.APP_PHONE;
                                    //$htmls .= '<br/><a href="mailto:'.APP_EMAIL.'" class="link-inverse"><i class="uil uil-envelopes"></i> '.APP_EMAIL.'</a><br />';
                                    $htmls .= '<nav class="nav social social-white">';
                                        $htmls .= '<a href="https://www.facebook.com/edu.cmu.ac.th" target="_blank"><i class="uil uil-facebook-f"></i></a>';
                                        $htmls .= '<a href="https://www.edu.cmu.ac.th" target="_blank"><i class="uil uil-browser"></i></a>';
                                        $htmls .= '<a href="https://www.youtube.com/@predcmu4451" target="_blank"><i class="uil uil-youtube" target="_blank"></i></a>';
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
                            $htmls .= '<li class="nav-item d-lg-none">';
                                $htmls .= '<button class="hamburger offcanvas-nav-btn"><span></span></button>';
                            $htmls .= '</li>';
                        }else{
                            $htmls .= '<li class="nav-item d-md-block on-font-primary">';
                                $htmls .= '<a href="'.APP_HOME.'/login" class="btn btn-login btn-sm btn-soft-primary rounded-pill">เข้าสู่ระบบ</a>';
                            $htmls .= '</li>';
                        }
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
        $htmls = '';
        if( isset($index['addfooter']) ){
            $htmls .= '<section class="wrapper bg-primary angled upper-start"></section>';
        }
        $htmls .= '<footer class="bg-primary text-white">';
            $htmls .= '<div class="container pt-9 pb-1">';
                $htmls .= '<div class="row gy-2 gy-lg-0">';
                    $htmls .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-3 on-copyright">';
                        $htmls .= '<div class="widget on-logo">';
                            $htmls .= '<img src="'.THEME_IMG.'/logo/logo-light.png" srcset="'.THEME_IMG.'/logo/logo-light@2x.png 2x" alt="" />';
                        $htmls .= '</div>';
                        $htmls .= '<p class="mb-0">© '.date("Y").' '.APP_CODE.'. <br class="d-none d-lg-block">All rights reserved.</p>';
                    $htmls .= '</div>';
                    $htmls .= '<div class="col-xs-12 col-sm-12 col-md-9 col-lg-6">';
                        $htmls .= '<div class="widget">';
                            $htmls .= '<h4 class="widget-title text-white mb-3"><i class="uil uil-map-marker"></i> ติดต่อเรา</h4>';
                            $htmls .= '<address class="pe-xl-15 pe-xxl-17">';
                                $htmls .= '<div class="on-text-oneline">'.APP_FACT_TH.'</div>';
                                $htmls .= '<div>'.APP_ADDR_TH.'</div>';
                            $htmls .= '</address>';
                        $htmls .= '</div>';
                    $htmls .= '</div>';
                    $htmls .= '<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">';
                        $htmls .= '<div class="widget widget-phone">';
                            $htmls .= '<h4 class="widget-title text-white mb-3"><i class="uil uil-phone-volume"></i> โทรศัพท์</h4>';
                            $htmls .= '<p class="mb-0 on-text-oneline">';
                                $htmls .= '<i class="uil uil-forwaded-call"></i> '.APP_PHONE;
                            $htmls .= '</p>';
                            $htmls .= '<nav class="nav social social-white" style="margin-top:-5px;">';
                                $htmls .= '<a href="https://www.edu.cmu.ac.th" target="_blank"><i class="uil uil-globe"></i></a>';
                                $htmls .= '<a href="https://www.facebook.com/edu.cmu.ac.th" target="_blank"><i class="uil uil-facebook"></i></a>';
                                $htmls .= '<a href="https://www.youtube.com/@predcmu4451" target="_blank"><i class="uil uil-youtube" target="_blank"></i></a>';
                            $htmls .= '</nav>';
                        $htmls .= '</div>';
                    $htmls .= '</div>';
                $htmls .= '</div>';
            $htmls .= '</div>';
        $htmls .= '</footer>';

        return $htmls;
    }

    /**
     *  Get
     *  @param  key
     *  @return value
     */
    static function lang()
    {
        return (isset($_SESSION['NICE_LANGUAGE'])?$_SESSION['NICE_LANGUAGE']:'th');
    }

}
?>