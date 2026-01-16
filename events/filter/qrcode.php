<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/events'); ?>
<?php
    $scanme = APP_HOST;
    if( isset($_POST['events_id'])&&$_POST['events_id'] ){
        $data = DB::one("SELECT events.*
                        , IF(events.participant_type='LIST'
                            ,'<span class=\"badge badge-sm bg-pale-orange text-orange rounded me-1 align-self-start\"><i class=\"uil uil-clipboard-alt\"></i>LIST</span>เฉพาะผู้ที่มีรายชื่อ'
                            ,'<span class=\"badge badge-sm bg-pale-blue text-blue rounded me-1 align-self-start\"><i class=\"uil uil-clipboard\"></i>ALL</span>ทั่วไป'
                        ) AS events_icon
                        , DATE_FORMAT(events.start_date, '%H:%i') AS start_time
                        , DATE_FORMAT(events.end_date, '%H:%i') AS end_time
                        FROM events
                        WHERE events.events_id=:events_id
                        LIMIT 1;"
                        , array('events_id'=>$_POST['events_id'])
        );
        $scanme .= '/?events_id='.$_POST['events_id'];
    }
?>
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-manage">
        <div class="modal-header" style="min-height:100px;background:#615eba;">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" style="margin-top:-30px;margin-bottom:-30px;padding-left:35px;padding-right:35px;">
            <div class="alert alert-primary alert-icon mb-0" style="padding:2px;">
                <center>
                    <mark class="doc fs-lg" style="font-family:'CMU Light';"><?=((isset($data['events_name'])&&$data['events_name'])?$data['events_name']:'กิจกรรม')?></mark>
                    <div class="qrcode mt-2 mb-2"></div>
                    <mark class="doc" style="font-family:'CMU Light';"><?=$scanme?></mark>
                </center>
            </div>
        </div>
        <div class="modal-footer" style="min-height:100px;background:#615eba;"></div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $(".modal-dialog .qrcode").empty().qrcode({ "render": 'image',
            "fill": '#000000',
            "ecLevel": 'H',
            "text": '<?=$scanme?>',
            "size": 256,
            "radius": 0,
            "quiet": 1,
            "mode": 2,
            "mSize": 0,
            "mPosX": 0,
            "mPosY": 0,
            "label": '',
            "fontname": 'RSU Regular',
            "fontcolor": '#000000',
            "background": '#FFFFFF'
        });
    });
</script>