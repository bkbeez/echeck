<?php if(!isset($index['page'])||$index['page']!='events'){ header("location:".((isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']==443)?'https://':'http://').$_SERVER["HTTP_HOST"]); exit(); } ?>
<?php
    $events_id = ( (isset($_GET['list'])&&$_GET['list']) ? $_GET['list'] : null );
    if( $events_id ){
        $data = DB::one("SELECT events.*
                        , DATE_FORMAT(events.start_date, '%H:%i') AS start_time
                        , DATE_FORMAT(events.end_date, '%H:%i') AS end_time
                        FROM events
                        WHERE events.events_id=:events_id
                        LIMIT 1;"
                        , array('events_id'=>$events_id)
        );
    }
?>
<section class="wrapper bg-primary angled lower-end">
    <div class="container pb-4">&nbsp;</div>
</section>
<section class="wrapper">
    <div class="container">
        <form name="ManageForm" action="<?=$form?>" method="POST" enctype="multipart/form-data" class="form-manage" target="_blank">
            <input type="hidden" name="events_id" value="<?=((isset($data['events_id'])&&$data['events_id'])?$data['events_id']:null)?>">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-10 mx-auto mt-n6 mb-n3">
                    <div class="card">
                        <div class="card-header text-center position-relative">
                            <h1 class="text-sky">กิจกรรม</h1>
                        </div>
                        <div class="card-body position-relative">
                            <p class="lead mb-1 text-start on-text-oneline">ข้อมูลกิจกรรม</p>
                            <?php Helper::debug($data); ?>
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