<?php if(!isset($index['page'])||$index['page']!='manage'){ header("location:".((isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']==443)?'https://':'http://').$_SERVER["HTTP_HOST"]); exit(); } ?>
<style type="text/css">
    body { background:url('<?=THEME_IMG?>/map.png') top center; }
</style>
<section class="wrapper bg-sky angled lower-end">
    <div class="container pb-4">&nbsp;</div>
</section>
<section class="wrapper">
    <div class="container">
        <form name="MeetingForm" action="<?=$form?>/meeting/saving.php" method="POST" enctype="multipart/form-data" class="form-manage" target="_blank">
            <input type="hidden" name="id" value="<?=((isset($meeting['id'])&&$meeting['id'])?$meeting['id']:null)?>" />
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-10 mx-auto mt-n6 mb-n3">
                    <div class="card">
                        <div class="card-body">
                            <p class="lead mb-1 text-start on-text-oneline">ข้อมูล</p>
                            <div class="form-floating mb-1">
                                <input required name="name_th" value="<?=((isset($meeting['name_th'])&&$meeting['name_th'])?$meeting['name_th']:null)?>" type="text" class="form-control">
                                <label>ชื่อกิจกรรม <span class="text-red">*</span></label>
                            </div>
                            <div class="row gx-1">
                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 ">
                                    <div class="form-floating mb-1">
                                        <input required name="date_start" value="<?=((isset($meeting['date_start_date'])&&$meeting['date_start_date'])?$meeting['date_start_date']:null)?>" type="datetime-local" class="form-control" placeholder="...">
                                        <label>วันที่เริ่มต้น <span class="text-red">*</span></label>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 ">
                                    <div class="form-floating mb-1">
                                        <input name="early_bird_date" value="<?=((isset($meeting['early_bird_date_time'])&&$meeting['early_bird_date_time'])?$meeting['early_bird_date_time']:null)?>" type="datetime-local" class="form-control" placeholder="...">
                                        <label>วันที่สิ้นสุด <span class="text-red">*</span></label>
                                    </div>
                                </div>
                            </div>
                            <p class="lead mt-4 mb-1 text-start on-text-oneline">ประเภท</p>
                            <div class="row gx-1">
                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 ">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="participant_type" value="ALL" id="participant-type-n" checked onchange="manage_events('payslip', { 'self':this });">
                                        <label class="form-check-label form-payslip-select" for="participant-type-n"><span class=text-green>ALL</span><span class="desc fs-14 on-text-normal-i">ทั้งหมด</span></label>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 ">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="participant_type" value="LIST" id="participant-type-y" onchange="manage_events('payslip', { 'self':this });">
                                        <label class="form-check-label form-payslip-select" for="participant-type-y"><span class=text-dark>LIST</span><span class="desc fs-14 on-text-normal-i">เฉพาะผู้ที่มีรายชื่อ</span></label>
                                    </div>
                                </div>
                            </div>
                            <p class="lead mt-4 mb-1 text-start on-text-oneline">สถานะ</p>
                            <div class="row gx-1">
                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 ">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="status" value="1" id="status-1" checked onchange="manage_events('payslip', { 'self':this });">
                                        <label class="form-check-label form-payslip-select" for="status-1"><span class=text-green>OPEN</span><span class="desc fs-14 on-text-normal-i">เปิดลงทะเบียน</span></label>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 ">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="status" value="2" id="status-2" onchange="manage_events('payslip', { 'self':this });">
                                        <label class="form-check-label form-payslip-select" for="status-2"><span class=text-red>CLOSE</span><span class="desc fs-14 on-text-normal-i">ปิดลงทะเบียน</span></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center position-relative">
                            <button type="submit" class="btn btn-lg btn-sky btn-icon btn-icon-start rounded-pill mb-2"><span class="uil uil-plus-circle"></span>&nbsp;สร้าง</button>
                            <button type="button" class="btn btn-lg btn-danger btn-icon btn-icon-start rounded-pill mb-2"><span class="uil uil-times-circle"></span>&nbsp;ยกเลิก</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>