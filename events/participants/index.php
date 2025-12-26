<?php if(!isset($index['page'])||$index['page']!='events'){ header("location:".((isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']==443)?'https://':'http://').$_SERVER["HTTP_HOST"]); exit(); } ?>
<?php
    $events_id = ( (isset($_GET['edit'])&&$_GET['edit']) ? $_GET['edit'] : null );
    if( $events_id ){
        $data = DB::one("SELECT events.*
                        , DATE_FORMAT(events.start_date, '%H:%i') AS start_time
                        , DATE_FORMAT(events.end_date, '%H:%i') AS end_time
                        FROM events
                        WHERE events.events_id=:events_id
                        LIMIT 1;"
                        , array('events_id'=>$events_id)
        );
        //Helper::debug($data);
    }
?>
<section class="wrapper bg-sky angled lower-end">
    <div class="container pb-4">&nbsp;</div>
</section>
<section class="wrapper">
    <div class="container">
        <form name="ManageForm" action="<?=$form?>/scripts/update.php" method="POST" enctype="multipart/form-data" class="form-manage" target="_blank">
            <input type="hidden" name="events_id" value="<?=((isset($data['events_id'])&&$data['events_id'])?$data['events_id']:null)?>">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-10 mx-auto mt-n6 mb-n3">
                    <div class="card">
                        <div class="card-header text-center position-relative">
                            <h1 class="text-sky">แก้ไขกิจกรรม</h1>
                        </div>
                        <div class="card-body position-relative">
                            <p class="lead mb-1 text-start on-text-oneline">ข้อมูลกิจกรรม</p>
                            <div class="form-floating form-select-wrapper mb-1">
                                <select id="participant_type" name="participant_type" class="form-select" aria-label="...">
                                    <option value="ALL"<?=((!isset($data['participant_type'])||$data['participant_type']=='ALL')?' selected':null)?>>[ALL] ทั่วไป</option>
                                    <option value="LIST"<?=((isset($data['participant_type'])&&$data['participant_type']=='LIST')?' selected':null)?>>[LIST] เฉพาะผู้ที่มีรายชื่อ</option>
                                </select>
                                <label for="participant_type">ประเภทผู้เข้าร่วม <span class="text-red">*</span></label>
                            </div>
                            <div class="form-floating mb-1">
                                <input id="events_name" name="events_name" type="text" value="<?=((isset($data['events_name'])&&$data['events_name'])?$data['events_name']:null)?>" class="form-control" placeholder="...">
                                <label for="events_name">ชื่อ <span class="text-red">*</span></label>
                            </div>
                            <p class="lead mt-3 mb-1 text-start on-text-oneline">เริ่มต้นกิจกรรม</p>
                            <div class="row gx-1">
                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 mx-auto">
                                    <div class="form-floating mb-1">
                                        <input id="start_date" name="start_date" type="text" value="<?=((isset($data['start_date'])&&$data['start_date'])?Helper::date($data['start_date']):null)?>" class="form-control" data-provide="datepicker" data-date-language="th-th" pattern="\d{1,2}/\d{1,2}/\d{4}" autocomplete="off" placeholder="..." minlength="10" maxlength="10" onkeyup="this.value=this.value.replace(/[^0-9/:]/g,'');"/>
                                        <label for="start_date">วันที่ [dd/mm/yyyy] <span class=text-red>*</span></label>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 mx-auto">
                                    <div class="form-floating mb-1">
                                        <input id="start_time" name="start_time" value="<?=((isset($data['start_time'])&&$data['start_time'])?$data['start_time']:null)?>" type="text" class="form-control" placeholder="..." minlength="5" maxlength="5" onkeyup="this.value=this.value.replace(/[^0-9:]/g,'');"/>
                                        <label for="start_time">เวลา [hh:mm] <span class=text-red>*</span></label>
                                    </div>
                                </div>
                            </div>
                            <p class="lead mt-3 mb-1 text-start on-text-oneline">สิ้นสุดกิจกรรม</p>
                            <div class="row gx-1">
                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 mx-auto">
                                    <div class="form-floating mb-1">
                                        <input id="end_date" name="end_date" type="text" value="<?=((isset($data['end_date'])&&$data['end_date'])?Helper::date($data['end_date']):null)?>" class="form-control" data-provide="datepicker" data-date-language="th-th" pattern="\d{1,2}/\d{1,2}/\d{4}" autocomplete="off" placeholder="..." minlength="10" maxlength="10" onkeyup="this.value=this.value.replace(/[^0-9/:]/g,'');"/>
                                        <label for="end_date">วันที่ [dd/mm/yyyy] <span class=text-red>*</span></label>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 mx-auto">
                                    <div class="form-floating mb-1">
                                        <input id="end_time" name="end_time" value="<?=((isset($data['end_time'])&&$data['end_time'])?$data['end_time']:null)?>" type="text" class="form-control" placeholder="..." minlength="5" maxlength="5" onkeyup="this.value=this.value.replace(/[^0-9:]/g,'');"/>
                                        <label for="end_time">เวลา [hh:mm] <span class=text-red>*</span></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center position-relative">
                            <button type="submit" class="btn btn-lg btn-green btn-icon btn-icon-start rounded-pill mb-2"><span class="uil uil-save"></span>&nbsp;บันทึก</button>
                            <button type="button" class="btn btn-lg btn-danger btn-icon btn-icon-start rounded-pill mb-2" onclick="document.location='<?=$index['back']?>';"><span class="uil uil-times-circle"></span>&nbsp;ยกเลิก</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<script type="text/javascript">
    $(document).ready(function(){
        $("form[name='ManageForm']").ajaxForm({
            beforeSubmit: function (formData, jqForm, options) {
                runStart();
            },
            success: function(rs) {
                runStop();
                var data = JSON.parse(rs);
                if(data.status=='success'){
                    swal({
                        'type': data.status,
                        'title': '<span class="on-font">'+data.title+'</span>',
                        'html': data.text,
                        'showConfirmButton': false,
                        'timer': 1500
                    }).then(
                        function () {},
                        function (dismiss) {
                            if (dismiss === 'timer') {
                                swal.close();
                            }
                        }
                    );
                }else{
                    swal({
                        'type' : data.status,
                        'title': '<span class="on-font">'+data.title+'</span>',
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
                            if( $("form[name='ManageForm'] input[name='"+data.onfocus+"']").length>0 ){
                                $("form[name='ManageForm'] input[name='"+data.onfocus+"']").focus();
                            }else if( $("form[name='ManageForm'] select[name='"+data.onfocus+"']").length>0 ){
                                $("form[name='ManageForm'] select[name='"+data.onfocus+"']").focus();
                            }else if( $("form[name='ManageForm'] textarea[name='"+data.onfocus+"']").length>0 ){
                                $("form[name='ManageForm'] textarea[name='"+data.onfocus+"']").focus();
                            }
                            swal.close();
                        },
                        function (dismiss) {
                            if (dismiss === 'cancel') {
                                if( $("form[name='ManageForm'] input[name='"+data.onfocus+"']").length>0 ){
                                    $("form[name='ManageForm'] input[name='"+data.onfocus+"']").focus();
                                }else if( $("form[name='ManageForm'] select[name='"+data.onfocus+"']").length>0 ){
                                    $("form[name='ManageForm'] select[name='"+data.onfocus+"']").focus();
                                }else if( $("form[name='ManageForm'] textarea[name='"+data.onfocus+"']").length>0 ){
                                    $("form[name='ManageForm'] textarea[name='"+data.onfocus+"']").focus();
                                }
                                swal.close();
                            }
                        }
                    );
                }
            }
        });
    });
</script>