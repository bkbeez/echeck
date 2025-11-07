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
            $htmls = '<div id="offcanvas-cart" class="offcanvas offcanvas-end bg-light iceahe-profile on-font-primary" data-bs-scroll="true" style="background:url(\''.THEME_IMG.'/map.png\') repeat-y top center;">';
                $htmls .= '<div class="offcanvas-header">';
                    $htmls .= '<h3 class="mb-0" style="color:#372770;">'.Lang::get('Profile').'</h3>';
                    $htmls .= '<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>';
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
                    $htmls .= '<div class="card shadow-lg bg-primary mt-1 mb-0 card-open-link" onclick="document.location=\''.APP_PATH.'/profile\';" style="cursor:pointer;">';
                        $htmls .= '<div class="card-body" style="padding:10px 0 0 10px;">';
                            $htmls .= '<div class="d-flex flex-row">';
                                $htmls .= '<div><span class="icon btn btn-circle btn-lg btn-soft-primary pe-none me-4"><i class="uil uil-pen"></i></span></div>';
                                $htmls .= '<div class="text-primary on-text-oneline" style="font-weight:normal;">';
                                    $htmls .= '<h4 class="mb-0 on-text-oneline">'.Lang::get('Profile').'</h4>';
                                    if( User::meeting('registered')=='Y' ){
                                        if(User::meeting('is_presenter')=='Y'){
                                            $htmls .= '<p class="fs-14 on-text-oneline">'.( (App::lang()=='en') ? 'Submission/Send Pay-In slip' : 'ส่งบทความ/แจ้งชำระเงิน' ).'</p>';
                                        }else{
                                            $htmls .= '<p class="fs-14 on-text-oneline">'.( (App::lang()=='en') ? "Registration's information" : 'ข้อมูลการลงทะเบียน' ).'</p>';
                                        }
                                    }else{
                                        $htmls .= '<p class="fs-14 on-text-oneline">'.( (App::lang()=='en') ? 'You are <span class="underline-3 style-2 red">not registered</span>' : 'ท่านยัง<span class="underline-3 style-2 red">ไม่ได้ลงทะเบียน</span>' ).'</p>';
                                    }
                                $htmls .= '</div>';
                            $htmls .= '</div>';
                        $htmls .= '</div>';
                    $htmls .= '</div>';
                    if( Auth::staff() ){
                        $htmls .= '<div class="card shadow-lg bg-primary mt-1 mb-0 card-open-link" onclick="document.location=\''.APP_PATH.'/backoffice\';" style="cursor:pointer;">';
                            $htmls .= '<div class="card-body" style="padding:10px 0 0 10px;">';
                                $htmls .= '<div class="d-flex flex-row">';
                                    $htmls .= '<div><span class="icon btn btn-circle btn-lg btn-soft-primary pe-none me-4"><i class="uil uil-apps"></i></span></div>';
                                    $htmls .= '<div class="text-primary on-text-oneline" style="font-weight:normal;">';
                                        $htmls .= '<h4 class="mb-0 on-text-oneline" style="color:#FFF;">'.((App::lang()=='en')?'Management Systems':'ระบบบริหารจัดการ').'</h4>';
                                        $htmls .= '<p class="fs-14 on-text-oneline" style="color:#FFF;">'.( (App::lang()=='en') ? 'Editors and Officers' : 'บรรณาธิการและเจ้าหน้าที่' ).'</p>';
                                    $htmls .= '</div>';
                                $htmls .= '</div>';
                            $htmls .= '</div>';
                        $htmls .= '</div>';

                        $htmls .= '<div class="card shadow-lg bg-primary mt-1 mb-0 card-open-link" onclick="document.location=\''.APP_PATH.'/check\';" style="cursor:pointer;">';
                            $htmls .= '<div class="card-body" style="padding:10px 0 0 10px;">';
                                $htmls .= '<div class="d-flex flex-row">';
                                    $htmls .= '<div><span class="icon btn btn-circle btn-lg btn-soft-primary pe-none me-4"><i class="uil uil-user-square"></i></span></div>';
                                    $htmls .= '<div class="text-primary on-text-oneline" style="font-weight:normal;">';
                                        $htmls .= '<h4 class="mb-0 on-text-oneline" style="color:#FFF;">'.((App::lang()=='en')?'Check-in to join':'Check-in เข้าร่วมงาน').'</h4>';
                                        $htmls .= '<p class="fs-14 on-text-oneline" style="color:#FFF;">'.( (App::lang()=='en') ? 'List fo Check-in to conference' : 'รายการ Check-in เข้าร่วมประชุม' ).'</p>';
                                    $htmls .= '</div>';
                                $htmls .= '</div>';
                            $htmls .= '</div>';
                        $htmls .= '</div>';
                    }
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
        if( isset($index['page'])&& in_array($index['page'], array('mobile','onlineregister','register','backoffice','login','deny')) ){
            return null;
        }
        $htmls = '<header class="wrapper bg-soft-primary">';
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
        if( isset($index['page'])&& in_array($index['page'], array('website','mobile','scan','check','login','deny')) ){
            return null;
        }else if( isset($_SESSION['deny']) ){
            unset($_SESSION['deny']);
        }
        if( isset($index['page'])&&!in_array($index['page'], array('onlineregister','register','backoffice','profile','login')) ){
            $cntchk = DB::one("SELECT (SELECT COALESCE(COUNT(DISTINCT date_at,device,platform,browser,ip_client,ip_server),0) FROM xlg_visits) AS visits
                                    , (SELECT COALESCE(COUNT(member_id),0) FROM meeting_participant WHERE meeting_id=:meeting_id AND status_id<=2 AND is_test='N' AND participant_id IN ('NONE','ONLINE') ) AS participants
                                    , (SELECT COALESCE(COUNT(member_id),0) FROM meeting_participant WHERE meeting_id=:meeting_id AND status_id<=2 AND meeting_participant.fullpaper_status IS NOT NULL AND is_test='N' AND participant_id NOT IN ('NONE','ONLINE') ) AS presenters;"
                                    , array('meeting_id'=>'20250614')
            );
            $totals['visits'] = ((isset($cntchk['visits'])&&$cntchk['visits'])?intval($cntchk['visits']):0);
            $totals['presenters'] = ((isset($cntchk['presenters'])&&$cntchk['presenters'])?intval($cntchk['presenters']):0);
            $totals['participants'] = ((isset($cntchk['participants'])&&$cntchk['participants'])?intval($cntchk['participants']):0);
            $counters = '<div class="row mt-n15">';
                $counters .= '<div class="col-xl-8 mx-auto">';
                    $counters .= '<div class="card image-wrapper bg-full bg-image bg-overlay bg-overlay-400" data-image-src="'.THEME_IMG.'/bg/bg-blue.jpg" style="background-image: url('.THEME_IMG.'/bg/bg-blue.jpg);border:2px solid #fefefe;">';
                        $counters .= '<div class="card-body">';
                            $counters .= '<div class="row align-items-center counter-wrapper gy-8 text-center text-white">';
                                $counters .= '<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">';
                                    $counters .= '<h3 class="counter counter-sm text-white" style="visibility:visible;">'.number_format($totals['visits'],0).'</h3>';
                                    $counters .= '<p class="on-text-oneline">'.Lang::get('Visits').'</p>';
                                $counters .= '</div>';
                                $counters .= '<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">';
                                    $counters .= '<h3 class="counter counter-sm text-white" style="visibility:visible;">'.number_format($totals['participants'],0).'</h3>';
                                    $counters .= '<p class="on-text-oneline">'.Lang::get('Participants').'</p>';
                                $counters .= '</div>';
                                $counters .= '<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">';
                                    $counters .= '<h3 class="counter counter-sm text-white" style="visibility:visible;">'.number_format($totals['presenters'],0).'</h3>';
                                    $counters .= '<p class="on-text-oneline">'.Lang::get('Presenters').'</p>';
                                $counters .= '</div>';
                            $counters .= '</div>';
                        $counters .= '</div>';
                    $counters .= '</div>';
                $counters .= '</div>';
            $counters .= '</div>';
        }
        $htmls = '<footer id="iceahe-footer" class="image-wrapper bg-full bg-image bg-overlay bg-overlay-400 pt-4 text-white on-font-primary" data-image-src="'.THEME_IMG.'/bg/bg-blue.jpg" style="background-image: url('.THEME_IMG.'/bg/bg-blue.jpg);">';
            $htmls .= '<div class="container">';
                $htmls .= ( isset($counters) ? $counters : null );
                $htmls .= '<div class="row gy-6 gy-lg-0 mt-5">';
                    $htmls .= '<div class="col-md-12 col-lg-3">';
                        $htmls .= '<div class="widget on-logo">';
                            $htmls .= '<img src="'.THEME_IMG.'/logo/small.png?'.time().'"/>';
                            $htmls .= '<p class="mb-4 on-text-oneline">Copyright &copy; 2025<span>All rights reserved</span></p>';
                        $htmls .= '</div>';
                    $htmls .= '</div>';
                    $htmls .= '<div class="col-md-12 col-lg-9">';
                        $htmls .= '<div class="widget on-widget-footer">';
                            $htmls .= '<div class="row">';
                                $htmls .= '<div class="col-md-12 col-lg-8">';
                                    $htmls .= '<h4 class="widget-title text-white mb-3 hide-991-display">&nbsp;</h4>';
                                    $htmls .= '<address class="pe-xl-15 pe-xxl-17">';
                                        $htmls .= '<div class="on-text-oneline">'.( (App::lang()=='en') ? APP_FACT_EN : APP_FACT_TH ).'</div>';
                                        $htmls .= '<div style="padding-left:18px;text-indent:-21px;">'.( (App::lang()=='en') ? '<i class="uil uil-location-pin-alt"></i> '.APP_ADDR_EN : '<i class="uil uil-location-pin-alt"></i> '.APP_ADDR_TH ).'</div>';
                                    $htmls .= '</address>';
                                $htmls .= '</div>';
                                $htmls .= '<div class="col-md-12 col-lg-4">';
                                    $htmls .= '<h4 class="widget-title text-white mb-3 hide-991-display">&nbsp;</h4>';
                                    $htmls .= '<p class="on-text-oneline">';
                                        $htmls .= '<i class="uil uil-phone-volume"></i> '.APP_PHONE;
                                        //$htmls .= '<br/><i class="uil uil-envelopes"></i> Email: ';
                                        //$htmls .= '<br/><a class="text-white hover" target="_blank" href="https://www.facebook.com/EDU.CMU"><i class="uil uil-facebook"></i> EDU.CMU Facebook</a>';
                                    $htmls .= '</p>';
                                $htmls .= '</div>';
                            $htmls .= '</div>';
                        $htmls .= '</div>';
                    $htmls .= '</div>';
                $htmls .= '</div>';
            $htmls .= '</div>';
        $htmls .= '</footer>';

        return $htmls;
    }

}
?>