<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::ajax(APP_PATH.'/admin/?users'); ?>
<?php
    $form = ( (isset($_POST['form_as'])&&$_POST['form_as']) ? $_POST['form_as'] : null );
?>
<style type="text/css">
    .modal-dialog .modal-header {
        min-height: 100px;
        background: #fef7ed;
    }
    .modal-dialog .modal-body {
        margin-top: -30px;
        padding-left: 35px;
        padding-right: 35px;
    }
    .modal-dialog .modal-body>.alert {
        padding: 5px 15px;
    }
    .modal-dialog .modal-footer button>i {
        float: left;
        font-size: 24px;
        line-height: 24px;
        margin-right: 3px;
    }
</style>
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-manage">
        <form name="RecordForm" action="<?=$form?>/scripts/register.php" method="POST" enctype="multipart/form-data" class="form-manage" target="_blank">
            <input type="hidden" name="events_id" value="<?=((isset($_POST['events_id'])&&$_POST['events_id'])?$_POST['events_id']:null)?>"/>
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <h2 class="mb-0 text-blue text-start on-text-oneline"><i class="uil uil-user-plus" style="float:left;font-size:36px;line-height:36px;margin-right:3px;"></i> ลงทะเบียนกิจกรรม</h2>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning alert-icon mb-2">
                    <div class="form-floating form-select-wrapper mb-1">
                        <select id="type" name="type" class="form-select" aria-label="...">
                            <option value="EMPLOYEE" selected>พนักงาน</option>
                            <option value="STUDENT">นักศึกษา</option>
                            <option value="OTHER">บุคคลทั่วไป</option>
                        </select>
                        <label for="type">ประเภทผู้เข้าร่วม <span class="text-red">*</span></label>
                    </div>
                </div>
                <div class="alert alert-warning alert-icon mb-2">
                    <p class="lead text-dark mb-1 text-start on-text-oneline">ข้อมูลผู้เข้าร่วม</p>
                    <div class="form-floating mb-1">
                        <input id="email" name="email" value="<?=User::get('email')?>" type="email" class="form-control" placeholder="...">
                        <label for="email">อีเมล <span class="text-red">*</span></label>
                    </div>
                    <div class="form-floating mb-1">
                        <input id="prefix" name="prefix" value="" type="text" class="form-control" placeholder="...">
                        <label for="prefix">คำนำหน้า</label>
                    </div>
                    <div class="form-floating mb-1">
                        <input id="firstname" name="firstname" value="<?=User::get('firstname')?>" type="text" class="form-control" placeholder="...">
                        <label for="firstname">ชื่อ <span class="text-red">*</span></label>
                    </div>
                    <div class="form-floating mb-1">
                        <input id="lastname" name="lastname" value="<?=User::get('lastname')?>" type="text" class="form-control" placeholder="...">
                        <label for="lastname">สกุล</label>
                    </div>
                    <div class="form-floating mb-1">
                        <input id="organization" name="organization" value="<?=User::get('organization')?>" type="text" class="form-control" placeholder="...">
                        <label for="organization">สังกัด <span class="text-red">*</span></label>
                    </div>
                    <div class="form-floating mb-1">
                        <input id="department" name="department" value="" type="text" class="form-control" placeholder="...">
                        <label for="department">ฝ่าย/แผนก</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-center">
                <div class="row gx-1 row-button">
                    <div class="col-lg-6 col-md-6 pt-1">
                        <button type="submit" class="btn btn-lg btn-blue rounded-pill w-100"><i class="uil uil-check-circle"></i>ลงทะเบียน</button>
                    </div>
                    <div class="col-lg-6 col-md-6 pt-1">
                        <button type="button" class="btn btn-lg btn-outline-danger rounded-pill w-100" data-bs-dismiss="modal"><i class="uil uil-times-circle"></i>ยกเลิก</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $("form[name='RecordForm']").ajaxForm({
            beforeSubmit: function (formData, jqForm, options) {
                $("form[name='RecordForm'] label>span>font").remove();
                runStart();
            },
            success: function(rs) {
                runStop();
                var data = JSON.parse(rs);
                if(data.status=='success'){
                    $("form[name='RecordForm'] .modal-header, form[name='RecordForm'] .modal-body, hr").hide();
                    var htmls ='<div class="d-flex flex-row text-start" style="margin-top:15px;">';
                        htmls +='<div style="margin-top:-5px;"><span class="icon btn btn-circle btn-lg btn-success pe-none me-4"><i class="uil uil-check"></i></span></div>';
                        htmls +='<div class="text-primary" style="font-weight:normal;">';
                            htmls += data.title;
                        htmls +='</div>';
                    htmls +='</div>';
                    $("form[name='RecordForm'] .modal-footer").html(htmls);
                    setTimeout(function(){
                        $("#ManageDialog").modal('hide');
                        // Reload the page or update the table
                        location.reload();
                    }, 2000);
                }else{
                    if(data.onfocus){
                        $("#"+data.onfocus).focus();
                        $("#"+data.onfocus).parent().find("label").append("<font class='text-red'> "+data.message+"</font>");
                    }else if(data.onselect){
                        $("#"+data.onselect).parent().find("label").append("<font class='text-red'> "+data.message+"</font>");
                    }else{
                        alert(data.message);
                    }
                }
            },
            error: function() {
                runStop();
                alert("เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง");
            }
        });
    });
</script>