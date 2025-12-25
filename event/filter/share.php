<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/event'); ?>
<?php
    $form = ( (isset($_POST['form_as'])&&$_POST['form_as']) ? $_POST['form_as'] : null );
    $event = null;
    $shares = [];
    $error = '';
    $success = '';
    
    $eventId = isset($_POST['events_id']) ? intval($_POST['events_id']) : 0;
    
    $user_id = '';
    if (isset($_SESSION['login']) && isset($_SESSION['login']['user'])) {
        $user_id = isset($_SESSION['login']['user']['email']) ? $_SESSION['login']['user']['email'] : 
                    (isset($_SESSION['login']['user']['id']) ? $_SESSION['login']['user']['id'] : '');
    }
    
    // Handle POST requests for add/remove actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $action = isset($_POST['action']) ? trim($_POST['action']) : '';
        
        if ($action === 'add') {
            $shared_id = isset($_POST['shared_id']) ? trim($_POST['shared_id']) : '';
            
            if (empty($shared_id)) {
                $error = ( (App::lang()=='en') ? 'Please enter email or user ID' : 'กรุณากรอกอีเมลหรือ ID ของผู้ใช้ที่ต้องการแชร์' );
            } else {
                try {
                    $result = Event::addShare($eventId, $user_id, $shared_id);
                    
                    if ($result) {
                        $success = ( (App::lang()=='en') ? 'Share added successfully' : 'เพิ่มการแชร์สำเร็จ' );
                    } else {
                        $error = ( (App::lang()=='en') ? 'Cannot add share. May already be shared or user is event owner' : 'ไม่สามารถเพิ่มการแชร์ได้ อาจเป็นเพราะแชร์กับผู้ใช้นี้แล้วหรือผู้ใช้เป็นเจ้าของกิจกรรม' );
                    }
                } catch (Exception $e) {
                    $error = ( (App::lang()=='en') ? 'Error occurred' : 'เกิดข้อผิดพลาด' ) . ': ' . $e->getMessage();
                }
            }
        } elseif ($action === 'remove') {
            $shareId = isset($_POST['share_id']) ? intval($_POST['share_id']) : 0;
            
            if ($shareId <= 0) {
                $error = ( (App::lang()=='en') ? 'Share not found' : 'ไม่พบข้อมูลการแชร์' );
            } else {
                try {
                    $result = Event::removeShare($shareId, $eventId, $user_id);
                    
                    if ($result) {
                        $success = ( (App::lang()=='en') ? 'Share removed successfully' : 'ลบการแชร์สำเร็จ' );
                    } else {
                        $error = ( (App::lang()=='en') ? 'Cannot remove share. Please try again' : 'ไม่สามารถลบการแชร์ได้ กรุณาลองใหม่อีกครั้ง' );
                    }
                } catch (Exception $e) {
                    $error = ( (App::lang()=='en') ? 'Error occurred' : 'เกิดข้อผิดพลาด' ) . ': ' . $e->getMessage();
                }
            }
        }
    }
    
    if ($eventId <= 0) {
        $error = ( (App::lang()=='en') ? 'Event not found' : 'ไม่พบข้อมูลกิจกรรม' );
    } else {
        $event = Event::getOwnedEvent($eventId, $user_id);
        
        if (!$event) {
            $error = ( (App::lang()=='en') ? 'Event not found or you do not have permission to share this event' : 'ไม่พบกิจกรรมหรือคุณไม่มีสิทธิ์แชร์กิจกรรมนี้' );
        } else {
            $shares = Event::listShares($eventId, $user_id);
            if (!is_array($shares)) {
                $shares = [];
            }
        }
    }
?>
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-manage">
        <form name="RecordForm" action="<?=$form?>/filter/share.php" method="POST" enctype="multipart/form-data" class="form-manage" target="_blank">
            <input type="hidden" name="events_id" value="<?=((isset($event['events_id'])&&$event['events_id'])?$event['events_id']:null)?>">
            <input type="hidden" name="form_as" value="<?=$form?>">
            <div class="modal-header" style="min-height:100px;background:#eef6f9;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h2 class="mb-0 text-start on-text-oneline"><i class="uil uil-share-alt fs-32"></i> <?=( (App::lang()=='en') ? 'Share Event' : 'แชร์กิจกรรม' )?></h2>
            </div>
            <div class="modal-body" style="margin-top:-30px;padding-left:35px;padding-right:35px;">
                <div class="on-status"></div>
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-icon mb-2" style="padding:5px 15px;">
                        <p class="mb-0 on-text-normal"><?= htmlspecialchars($error) ?></p>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success alert-icon mb-2" style="padding:5px 15px;">
                        <p class="mb-0 on-text-normal"><?= htmlspecialchars($success) ?></p>
                    </div>
                <?php endif; ?>
                <?php if ($event): ?>
                    <div class="alert alert-info alert-icon mb-2" style="padding:5px 15px;">
                        <div class="row gx-1">
                            <div class="col-lg-12 col-md-12">
                                <div class="form-floating mb-1">
                                    <div class="form-control on-text-display"><?=((isset($event['events_id'])&&$event['events_id'])?$event['events_id']:'-')?></div>
                                    <label><?=((isset($event['events_name'])&&$event['events_name'])?htmlspecialchars($event['events_name']):'-')?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info alert-icon mb-2" style="padding:5px 15px;">
                        <p class="lead text-dark mb-1 text-start on-text-oneline"><?=( (App::lang()=='en') ? 'Add Share' : 'เพิ่มการแชร์' )?></p>
                        <div class="form-floating mb-1">
                            <input name="shared_id" type="text" class="form-control" placeholder="<?=( (App::lang()=='en') ? 'Email or User ID' : 'อีเมลหรือ ID ของผู้ใช้' )?>" id="shared_id" required>
                            <label for="shared_id"><?=( (App::lang()=='en') ? 'Email or User ID' : 'อีเมลหรือ ID ของผู้ใช้' )?> *<span></span></label>
                            <div class="on-shared_id"></div>
                        </div>
                        <div class="row gx-1">
                            <div class="col-lg-12 col-md-12">
                                <button type="button" class="btn btn-lg btn-icon btn-icon-start btn-blue rounded-pill w-100" onclick="record_events('add');"><i class="uil uil-plus-circle"></i><?=( (App::lang()=='en') ? 'Add Share' : 'เพิ่มการแชร์' )?></button>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info alert-icon mb-2" style="padding:5px 15px;">
                        <p class="lead text-dark mb-1 text-start on-text-oneline"><?=( (App::lang()=='en') ? 'Shared Users' : 'ผู้ใช้ที่ถูกแชร์' )?> (<?= count($shares) ?>)</p>
                        <div class="share-list" style="max-height:300px;overflow-y:auto;">
                            <?php if (count($shares) > 0): ?>
                                <?php foreach ($shares as $share): ?>
                                    <div class="mb-2 p-2" style="border:1px solid #e2e8f0;border-radius:0.5rem;background:#f8fafc;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="uil uil-user me-2"></i>
                                                <strong><?= htmlspecialchars($share['shared_id']) ?></strong>
                                                <?php if (isset($share['date_shared'])): ?>
                                                    <small class="text-muted ms-2">(<?= ( (App::lang()=='en') ? 'Shared on' : 'แชร์เมื่อ' )?>: <?= Helper::datetimeDisplay($share['date_shared'], App::lang()) ?>)</small>
                                                <?php endif; ?>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="record_events('remove', { 'share_id': '<?= htmlspecialchars($share['id']) ?>' });">
                                                <i class="uil uil-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-ash p-3">
                                    <i class="uil uil-info-circle fs-32 mb-2 d-block"></i>
                                    <p class="mb-0 on-text-normal"><?=( (App::lang()=='en') ? 'No shared users yet' : 'ยังไม่มีผู้ใช้ที่ถูกแชร์กิจกรรมนี้' )?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer text-center">
                <div class="confirm-box"></div>
                <div class="row gx-1 row-button">
                    <div class="col-lg-12 col-md-12 pt-1">
                        <button type="button" class="btn btn-lg btn-icon btn-icon-start btn-soft-ash rounded-pill w-100" data-bs-dismiss="modal"><i class="uil uil-times-circle"></i><?=Lang::get('Close')?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    function record_events(action, params){
        $("form[name='RecordForm'] .on-status, form[name='RecordForm'] .on-focus").html('');
        if(action=="add"){
            if( params!=undefined ){
                $("form[name='RecordForm'] .confirm-box").html('').css('margin-top','0');
                $("form[name='RecordForm'] .row-button").show();
            }else{
                var shared_id = $("form[name='RecordForm'] input[name='shared_id']").val();
                if(!shared_id || shared_id.trim()==''){
                    $("form[name='RecordForm'] .on-shared_id").html('<font class="fs-12 on-text-normal-i text-red"><?=( (App::lang()=='en') ? 'Please enter email or user ID' : 'กรุณากรอกอีเมลหรือ ID ของผู้ใช้' )?></font>');
                    $("form[name='RecordForm'] input[name='shared_id']").focus();
                    return;
                }
                var htmls  = '<div class="fs-19 mb-2 text-center on-text-normal"><?=( (App::lang()=='en') ? 'Are you sure to add share ?' : 'ยืนยันการเพิ่มการแชร์ ?' )?></div>';                    
                    htmls += '<button type="button" class="btn btn-lg btn-icon btn-icon-start btn-success rounded-pill" onclick="record_events(\'add\', { \'confirm\':\'Y\' });"><i class="uil uil-check-circle"></i><?=Lang::get('Yes')?></button>';
                    htmls += '&nbsp;';
                    htmls += '<button type="button" class="btn btn-lg btn-icon btn-icon-start btn-outline-danger rounded-pill" onclick="record_events(\'add\', { \'on\':\'N\' });"><i class="uil uil-times-circle"></i><?=Lang::get('No')?></button>';
                $("form[name='RecordForm'] .confirm-box").html(htmls).css('margin-top','-15px');
                $("form[name='RecordForm'] .row-button").hide();
            }
            if( params!=undefined && params.confirm=='Y' ){
                $.ajax({
                    url : "<?=$form?>/filter/share.php",
                    type: 'POST',
                    data: {
                        'action': 'add',
                        'events_id': $("form[name='RecordForm'] input[name='events_id']").val(),
                        'shared_id': $("form[name='RecordForm'] input[name='shared_id']").val(),
                        'form_as': '<?=$form?>'
                    },
                    dataType: "html",
                    beforeSend: function( xhr ) {
                        runStart();
                    }
                }).done(function(html) {
                    runStop();
                    $("#ManageDialog").html(html);
                });
            }
        }else if(action=="remove"){
            if( params!=undefined && params.share_id ){
                swal({
                    'title':'<b class="text-red" style="font-size:100px;"><i class="uil uil-trash-alt"></i></b>',
                    'html' :'<?=( (App::lang()=='en') ? 'Confirm to remove share ?' : 'ยืนยันการลบการแชร์ ?' )?>',
                    'showCloseButton': false,
                    'showConfirmButton': true,
                    'showCancelButton': true,
                    'focusConfirm': false,
                    'allowEscapeKey': false,
                    'allowOutsideClick': false,
                    'confirmButtonClass': 'btn btn-icon btn-icon-start btn-success rounded-pill',
                    'confirmButtonText':'<font class="fs-16"><i class="uil uil-check-circle"></i> <?=Lang::get('Yes')?></font>',
                    'cancelButtonClass': 'btn btn-icon btn-icon-start btn-outline-danger rounded-pill',
                    'cancelButtonText':'<font class="fs-16"><i class="uil uil-times-circle"></i> <?=Lang::get('No')?></font>',
                    'buttonsStyling': false
                }).then(
                    function () {
                        $.ajax({
                            url : "<?=$form?>/filter/share.php",
                            type: 'POST',
                            data: {
                                'action': 'remove',
                                'events_id': $("form[name='RecordForm'] input[name='events_id']").val(),
                                'share_id': params.share_id,
                                'form_as': '<?=$form?>'
                            },
                            dataType: "html",
                            beforeSend: function( xhr ) {
                                runStart();
                            }
                        }).done(function(html) {
                            runStop();
                            $("#ManageDialog").html(html);
                        });
                    },
                    function (dismiss) {
                        if (dismiss === 'cancel') {
                            swal.close();
                        }
                    }
                );
            }
        }
    }
    $(document).ready(function() {
        <?php if ($success): ?>
            $("form[name='RecordForm'] input[name='shared_id']").val('');
        <?php endif; ?>
    });
</script>
