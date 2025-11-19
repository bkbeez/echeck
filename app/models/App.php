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
                                , 'menu' => array(
                                    'th'=>'เมนู 1',
                                    'en'=>'Menu 1',
                                    'icon'=>'uil uil-server',
                                    'link'=>'/menu1',
                                    'dropdown'=>array('type'=>'item'
                                                    , 'item'=>array('conference'=>array(
                                                                        'th'=>'เมนูย่อย 1',
                                                                        'en'=>'Sub menu 1',
                                                                        'icon'=>'uil uil-server',
                                                                        'link'=>'#'
                                                                    ),
                                                                    'programs'=>array(
                                                                        'th'=>'เมนูย่อย 2',
                                                                        'en'=>'Sub menu 2',
                                                                        'icon'=>'uil uil-server',
                                                                        'link'=>'#'
                                                                    )
                                                    )
                                    )
                                )
                                , 'about' => array(
                                    'th'=>'เมนู 2',
                                    'en'=>'Menu 2',
                                    'icon'=>'uil uil-browser',
                                    'link'=>'/menu2',
                                    'dropdown'=>array('type'=>'box'
                                                    , 'item'=>array('conference'=>array(
                                                                        'th'=>'เมนูย่อย 1',
                                                                        'en'=>'Sub menu 1',
                                                                        'icon'=>'uil uil-window',
                                                                        'link'=>'#'
                                                                    ),
                                                                    'programs'=>array(
                                                                        'th'=>'เมนูย่อย 2',
                                                                        'en'=>'Sub menu 2',
                                                                        'icon'=>'uil uil-window',
                                                                        'link'=>'#'
                                                                    )
                                                    )
                                    )
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
        $htmls = '<header class="wrapper bg-soft-primary">';
            $htmls .= '<nav class="navbar navbar-expand-lg classic transparent navbar-light">';
                $htmls .= '<div class="container flex-lg-row flex-nowrap align-items-center">';
                    $htmls .= '<div class="navbar-brand w-100">';
                        $htmls .= '<a href="'.APP_HOME.'">';
                            $htmls .= '<img src="'.THEME_IMG.'/logo/logo.png" srcset="'.THEME_IMG.'/logo/logo@2x.png 2x" alt="" style="height:72px;"/>';
                        $htmls .= '</a>';
                    $htmls .= '</div>';
                    $htmls .= '<div class="navbar-collapse offcanvas offcanvas-nav offcanvas-start">';
                        $htmls .= '<div class="offcanvas-header d-lg-none">';
                            $htmls .= '<img src="'.THEME_IMG.'/logo/logo-light.png" srcset="'.THEME_IMG.'/logo/logo-light@2x.png 2x" alt="" style="height:72px;"/>';
                            $htmls .= '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>';
                        $htmls .= '</div>';
                        $htmls .= '<div id="mainsite-navbar" class="offcanvas-body ms-lg-auto d-flex flex-column h-100">';
                            $htmls .= '<ul class="navbar-nav">';
                            /*foreach( (new App())->applications as $key => $value ){
                                if( isset($value['dropdown']) ){
                                    $htmls .= '<li class="nav-item'.((isset($index['page'])&&$index['page']==$key) ? ' active':null).' dropdown">';
                                        $htmls .= '<a class="nav-link dropdown-toggle" href="javascript:void(0);" data-bs-toggle="dropdown"><span class="nav-name"><div class="m-box-top"><i class="'.$value['icon'].'"></i></div><div class="m-box"><i class="'.$value['icon'].'"></i></div>'.$value[App::lang()].'</span></a>';
                                        if( $value['dropdown']['type']=='box' ){
                                            $htmls .= '<div class="dropdown-menu dropdown-lg">';
                                                $htmls .= '<div class="dropdown-lg-content">';
                                                foreach( $value['dropdown']['item'] as $ikey => $item ){
                                                    $htmls .= '<div class="mainsite-dropdown-box">';
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
                                            $htmls .= '<ul class="dropdown-menu mainsite-dropdown">';
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
                            }*/
                            if( isset($_SESSION['login'])&&Auth::admin() ){
                                $htmls .= '<li class="nav-item'.((isset($index['page'])&&$index['page']=='admin') ? ' active':null).' dropdown">';
                                    $htmls .= '<a class="nav-link dropdown-toggle" href="javascript:void(0);" data-bs-toggle="dropdown"><span class="nav-name"><div class="m-box-top"><i class="uil uil-create-dashboard"></i></div><div class="m-box"><i class="uil uil-create-dashboard"></i></div>'.Lang::get('Administrator').'</span></a>';
                                    $htmls .= '<ul class="dropdown-menu mainsite-dropdown">';
                                        $htmls .= '<li class="nav-item">';
                                            $htmls .= '<a class="dropdown-item" href="'.APP_HOME.'/admin/?users">';
                                                $htmls .= '<span class="nav-name"><div class="m-box"><i class="uil uil-users-alt"></i></div>User Accounts</span>';
                                            $htmls .= '</a>';
                                        $htmls .= '</li>';
                                        $htmls .= '<li class="nav-item">';
                                            $htmls .= '<a class="dropdown-item" href="'.APP_HOME.'/admin/?sessions">';
                                                $htmls .= '<span class="nav-name"><div class="m-box"><i class="uil uil-desktop"></i></div>View Sessions</span>';
                                            $htmls .= '</a>';
                                        $htmls .= '</li>';
                                        $htmls .= '<li class="nav-item">';
                                            $htmls .= '<a class="dropdown-item" href="'.APP_HOME.'/admin/?logs">';
                                                $htmls .= '<span class="nav-name"><div class="m-box"><i class="uil uil-monitor-heart-rate"></i></div>View Logs</span>';
                                            $htmls .= '</a>';
                                        $htmls .= '</li>';
                                    $htmls .= '</ul>';
                                $htmls .= '</li>';
                            }
                            $htmls .= '</ul>';
                            $htmls .= '<div class="offcanvas-footer d-lg-none">';
                                $htmls .= '<div>';
                                    $htmls .= '<i class="uil uil-phone-volume"></i> '.APP_PHONE;
                                    $htmls .= '<br/><a href="mailto:'.APP_EMAIL.'" class="link-inverse"><i class="uil uil-envelopes"></i> '.APP_EMAIL.'</a><br />';
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
                            }else{
                                $htmls .= '<li class="nav-item d-none d-md-block">';
                                    $htmls .= '<a href="'.APP_HOME.'/login" class="btn btn-sm btn-primary rounded-pill">Login</a>';
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
            $htmls .= '<div class="container py-2 py-md-7">';
                $htmls .= '<div class="row gy-2 gy-lg-0">';
                    $htmls .= '<div class="col-md-12 col-lg-2">';
                        $htmls .= '<div class="widget">';
                            $htmls .= '<img class="mb-4" src="'.THEME_IMG.'/logo/logo-light.png" srcset="'.THEME_IMG.'/logo/logo-light@2x.png 2x" alt="" />';
                        $htmls .= '</div>';
                    $htmls .= '</div>';
                    $htmls .= '<div class="col-md-8 col-lg-7">';
                        $htmls .= '<div class="widget">';
                            $htmls .= '<h4 class="widget-title text-white mb-3">© All rights reserved.</h4>';
                            $htmls .= '<address class="pe-xl-15 pe-xxl-17">';
                                $htmls .= '<div class="on-text-oneline">'.APP_FACT_EN.'</div>';
                                $htmls .= '<div>'.APP_ADDR_EN.'</div>';
                            $htmls .= '</address>';
                        $htmls .= '</div>';
                    $htmls .= '</div>';
                    $htmls .= '<div class="col-md-4 col-lg-3">';
                        $htmls .= '<div class="widget">';
                            $htmls .= '<h4 class="widget-title text-white mb-3">Contact Us</h4>';
                            $htmls .= '<p class="on-text-oneline">';
                                $htmls .= '<i class="uil uil-phone-volume"></i> '.APP_PHONE;
                                $htmls .= '<br/><i class="uil uil-envelopes"></i> '.APP_EMAIL;
                            $htmls .= '</p>';
                            $htmls .= '<nav class="nav social social-white">';
                                $htmls .= '<a href="https://www.facebook.com/edu.cmu.ac.th" target="_blank"><i class="uil uil-facebook-f"></i></a>';
                                $htmls .= '<a href="https://www.edu.cmu.ac.th" target="_blank"><i class="uil uil-browser"></i></a>';
                                $htmls .= '<a href="https://www.youtube.com/@predcmu4451" target="_blank"><i class="uil uil-youtube" target="_blank"></i></a>';
                            $htmls .= '</nav>';
                        $htmls .= '</div>';
                    $htmls .= '</div>';
                $htmls .= '</div>';
            $htmls .= '</div>';
        $htmls .= '</footer>';

        return $htmls;
    }

}
?>