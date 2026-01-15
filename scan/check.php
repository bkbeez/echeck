<?php if(!$events_id){ header("location:".((isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']==443)?'https://':'http://').$_SERVER["HTTP_HOST"]); exit(); } ?>
<?php
    $event = DB::one("SELECT events.*
                    , CONCAT(DATE_FORMAT(events.start_date,'%d/%m/'), (YEAR(events.start_date)+543),' เวลา ',DATE_FORMAT(events.start_date, '%H:%i')) AS start_date_display
                    , CONCAT(DATE_FORMAT(events.end_date,'%d/%m/'), (YEAR(events.end_date)+543),' เวลา ',DATE_FORMAT(events.end_date, '%H:%i')) AS end_date_display
                    FROM events
                    WHERE events.events_id=:events_id
                    LIMIT 1;"
                    , array('events_id'=>$events_id)
    );
    $fullname = User::get('fullname');
    $email = ( (isset($data['email'])&&$data['email']) ? $data['email'] : User::get('email') );
    if( isset($event['events_id'])&&$event['events_id'] ){
        if( isset($event['start_date_display'])&&$event['start_date_display'] ){
            $start_end_display = '<div class="container">';
                $start_end_display .= '<div class="row row-date">';
                    $start_end_display .= '<div class="col-xs-12 col-sm-1 col-md-2 col-lg-2"></div>';
                    $start_end_display .= '<div class="col-xs-12 col-sm-10 col-md-8 col-lg-8">';
                        $start_end_display .= '<div class="card bg-primary mt-n4">';
                            $start_end_display .= '<div class="card-body text-white text-center" style="padding:5px 0 15px 0 !important;">';
                            if( isset($event['start_date_display'])&&$event['start_date_display'] ){
                                $start_end_display .= '<div class="from">วันที่</div>';
                                $start_end_display .= '<span class="badge bg-pale-primary text-primary rounded-pill" style="padding-top:3px;line-height:20px;">'.$event['start_date_display'].'</span>';
                            }
                            $start_end_display .= '<hr>';
                            if( isset($event['end_date_display'])&&$event['end_date_display'] ){
                                $start_end_display .= '<div class="to">ถึง</div>';
                                $start_end_display .= '<span class="badge bg-pale-primary text-primary rounded-pill" style="padding-top:3px;line-height:20px;">'.$event['end_date_display'].'</span>';
                            }
                            $start_end_display .= '</div>';
                        $start_end_display .= '</div>';
                    $start_end_display .= '</div>';
                    $start_end_display .= '<div class="col-xs-12 col-sm-1 col-md-2 col-lg-2"></div>';
                $start_end_display .= '</div>';
            $start_end_display .= '</div>';
        }
        if( $event['participant_type']=='LIST' ){
            $checks = array();
            $checks['events_id'] = $event['events_id'];
            $checks['email'] = User::get('email');
            $data = DB::one("SELECT events_lists.*
                , TRIM(CONCAT(COALESCE(events_lists.prefix,''),events_lists.firstname,' ',COALESCE(events_lists.lastname,''))) AS fullname
                FROM events_lists
                WHERE events_lists.events_id=:events_id
                AND events_lists.email=:email
                LIMIT 1;"
                , $checks
            );
            $events_lists_id = ( (isset($data['id'])&&$data['id']) ? $data['id'] : null );
            if( isset($data['fullname'])&&$data['fullname'] ){
                $fullname = $data['fullname'];
            }
            if( isset($data['organization'])&&$data['organization'] ){
                $organization = $data['organization'];
                if(isset($data['department'])&&$data['department']){
                    $organization .= '<br>&rang; '.$data['department'];
                }
            }
        }
    }
?>
<?php include(APP_HEADER); ?>
<style type="text/css">
    body { background:url('<?=THEME_IMG?>/map.png') center center; }
    .wrapper .widgets {
        width: 375px;
        padding: 35px 0 0 35px;
    }
    .wrapper .row-date div.from {
        width: 35px;
        display: inline-block;
        letter-spacing: -1.5px;
    }
    .wrapper .row-date div.to {
        width: 35px;
        display: inline-block;
        letter-spacing: 1.5px;
    }
    .wrapper .row-date hr {
        display: none;
    }
    @media only all and (max-width: 414px) {
        .wrapper .widgets {
            width: 100%;
        }
        .wrapper .row-date hr {
            display: block;
            margin:8px 0 5px 0;
        }
    }
</style>
<section class="wrapper bg-primary">
    <div class="container">
        <h3 class="on-font-primary text-white text-center pb-3"><?=( (isset($event['events_name'])&&$event['events_name']) ? $event['events_name'] : 'กิจกรรม' )?></h3>
    </div>
</section>
<section class="wrapper today-body">
    <?=( isset($start_end_display) ? $start_end_display : null )?>
    <div class="container pb-3">
        <div class="row">
            <div class="col d-flex justify-content-center align-items-center">
                <form name="CheckForm" action="<?=APP_PATH.'/scan/checking.php'?>" method="POST" enctype="multipart/form-data" class="form-manage" target="_blank">
                    <input type="hidden" name="participant_type" value="<?=( (isset($event['participant_type'])&&$event['participant_type']) ? $event['participant_type'] : 'ALL' )?>">
                    <input type="hidden" name="events_id" value="<?=( (isset($event['events_id'])&&$event['events_id']) ? $event['events_id'] : null )?>">
                    <input type="hidden" name="id" value="<?=( (isset($data['id'])&&$data['id']) ? $data['id'] : null )?>">
                    <input type="hidden" name="type" value="<?=( (isset($data['type'])&&$data['type']) ? $data['type'] : 'OTHER' )?>">
                    <input type="hidden" name="email" value="<?=$email?>">
                    <input type="hidden" name="prefix" value="<?=( (isset($data['prefix'])&&$data['prefix']) ? $data['prefix'] : User::get('title') )?>">
                    <input type="hidden" name="firstname" value="<?=( (isset($data['firstname'])&&$data['firstname']) ? $data['firstname'] : User::get('name') )?>">
                    <input type="hidden" name="lastname" value="<?=( (isset($data['lastname'])&&$data['lastname']) ? $data['lastname'] : User::get('surname') )?>">
                    <input type="hidden" name="organization" value="<?=( (isset($data['organization'])&&$data['organization']) ? $data['organization'] : null )?>">
                    <input type="hidden" name="department" value="<?=( (isset($data['department'])&&$data['department']) ? $data['department'] : null )?>">
                    <div class="widgets mb-4">
                        <div class="row gx-4 gy-0">
                            <div class="col-12">
                                <div class="row gx-0 gy-0 row-success">
                                    <div class="d-flex flex-row">
                                        <div><div class="icon text-primary me-2 mt-n3" style="font-size:52px;line-height:75px;"><i class="uil uil-user-square"></i></div></div>
                                        <div>
                                            <h5 class="mb-0 text-primary on-font-primary">ชื่อ-สกุล</h5>
                                            <p class="on-text-normal text-dark" style="margin-top:-6px;"><?=( $fullname ? $fullname : '-' )?></p>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-row">
                                        <div><div class="icon text-primary me-2 mt-n3" style="font-size:52px;line-height:75px;"><i class="uil uil-envelopes"></i></div></div>
                                        <div>
                                            <h5 class="mb-0 text-primary on-font-primary">อีเมล</h5>
                                            <p class="on-text-normal text-dark" style="margin-top:-6px;"><?=( $email ? $email : '-' )?></p>
                                        </div>
                                    </div>
                                    <?php if( isset($organization)&&$organization ){ ?>
                                    <div class="d-flex flex-row">
                                        <div><div class="icon text-primary me-2 mt-n3" style="font-size:52px;line-height:75px;"><i class="uil uil-university"></i></div></div>
                                        <div>
                                            <h5 class="mb-0 text-primary on-font-primary">สังกัด</h5>
                                            <p class="on-text-normal text-dark" style="margin-top:-2px;line-height:18px;"><?=$organization?></p>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row gx-1 row-button">
                        <div class="col-6 pt-1">
                            <button type="submit" class="btn btn-lg btn-success btn-gift rounded fs-20 w-100"><i class="uil uil-check-circle" style="float:left;font-size:32px;line-height:32px;margin:0 3px -3px 0;"></i> เข้าร่วม</button>
                        </div>
                        <div class="col-6 pt-1">
                            <button type="button" class="btn btn-lg btn-danger rounded fs-20 w-100" onclick="check_events('cancel');"><i class="uil uil-times-circle" style="float:left;font-size:32px;line-height:32px;margin:0 3px -3px 0;"></i> ยกเลิก</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    function check_events(action, params){
        if(action=="cancel"){
            document.location='<?=APP_PATH.'/scan'?>';
        }else{
            $("form[name='CheckForm'] .row-button").fadeOut(1500, function(){
                document.location='<?=APP_PATH.'/scan'?>';
            });
        }
    }
    $(document).ready(function() {
        $("form[name='CheckForm']").ajaxForm({
            beforeSubmit: function (formData, jqForm, options) {
                runStart();
            },
            success: function(rs) {
                runStop();
                var data = JSON.parse(rs);
                if(data.status=='success'){
                    $("form[name='CheckForm'] .row-success").append(data.htmls);
                    $("form[name='CheckForm'] .row-button").html('<div class="col-12 pt-1" style="display:none;"><button type="button" class="btn btn-lg btn-primary btn-gift rounded fs-20 w-100" onclick="check_events(\'back\');"><i class="uil uil-arrow-circle-left" style="float:left;font-size:32px;line-height:32px;margin:0 3px -3px 0;"></i> กลับสู่หน้าหลัก</button></div>');
                    $("form[name='CheckForm'] .row-success .on-success>div").fadeIn('slow', function(){
                        $("form[name='CheckForm'] .row-button>div").fadeIn(1000);
                    });
                }else{
                    swal({
                        'type' : data.status,
                        'title': '<span class=on-font-primary>'+data.title+'</span>',
                        'html' : data.text,
                        'showCloseButton': false,
                        'showCancelButton': false,
                        'focusConfirm': false,
                        'allowEscapeKey': false,
                        'allowOutsideClick': false,
                        'confirmButtonClass': 'btn btn-outline-danger',
                        'confirmButtonText':'<span>รับทราบ</span>',
                        'buttonsStyling': false
                    }).then(
                        function () {
                            swal.close();
                        },
                        function (dismiss) {
                            if (dismiss === 'cancel') {
                                swal.close();
                            }
                        }
                    );
                }
            }
        });
    });
</script>
<?php include(APP_FOOTER); ?>