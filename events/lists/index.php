<?php if(!isset($index['page'])||$index['page']!='events'){ header("location:".((isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']==443)?'https://':'http://').$_SERVER["HTTP_HOST"]); exit(); } ?>
<?php
    $filter_as = strtolower($index['page'].'_eventlist_as');
    $filter = ( isset($_SESSION['login']['filter'][$filter_as]) ? $_SESSION['login']['filter'][$filter_as] : null );
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
<style type="text/css">
    .display-6 button {
        padding-left: 6px;
        padding-right: 12px;
    }
    .display-6 button>i {
        float: left;
        font-size: 32px;
        line-height: 24px;
        margin-right: 3px;
    }
    .table-filter .filter-result {
        background: white;
    }
    .table-filter .filter-result .type {
        width: 100px;
    }
    .table-filter .filter-result .name {
        width: 25%;
    }
    .table-filter .filter-result .organize {
        width: auto;
    }
    .table-filter .filter-result .status {
        width: 25%;
    }
    .table-filter .filter-result .name>.type-o,
    .table-filter .filter-result .name>.organize-o,
    .table-filter .filter-result .name>.status-o {
        display: none;
    }
    .table-filter .filter-result .badge {
        padding-left: 4px;
        padding-right: 4px;
    }
    .table-filter .filter-result .badge>i {
        float: left;
        font-size: 16px;
        line-height: 12px;
        margin:0 2px 0 -2px;
    }
    .table-filter .filter-result table tr td {
        line-height: 18px;
    }
    @media only all and (max-width: 991px) {
        .table-filter .filter-result .name {
            width: auto;
        }
        .table-filter .filter-result .organize {
            display: none;
        }
        .table-filter .filter-result .name>.organize-o {
            display: block;
        }
        .table-filter .filter-result table tr td {
            line-height: 20px;
        }
    }
    @media only all and (max-width: 768px) {
        .table-filter .filter-result .type,
        .table-filter .filter-result .status {
            display: none;
        }
        .table-filter .filter-result .name>.type-o,
        .table-filter .filter-result .name>.status-o {
            display: block;
        }
        .table-filter .filter-result .actions.act-3 {
            width: 75px;
        }
        .table-filter .filter-result .actions .btn-box.delete {
            margin-top: -4px;
        }
    }
    @media only all and (max-width: 585px) {
        .table-filter .filter-pageby button {
            padding-right: 5px;
        }
        .table-filter .filter-pageby button>span {
            display: none;
        }
    }
</style>
<section class="wrapper bg-primary">
    <div class="container pt-1 pb-1">
        <h2 class="display-6 text-yellow mb-2"> <button type="button" class="btn btn-navy" onclick="document.location='<?=$index['back']?>';"><i class="uil uil-arrow-circle-left"></i><span>กลับ</span></button> <?=((isset($data['events_name'])&&$data['events_name'])?$data['events_name']:'ไม่ทราบ... .. .')?></h2>
        <div class="row-tabs row gx-2 gy-2">
            <div class="col-4">
                <div class="card bg-blue active shadow-lg">
                    <a href="javascript:void(0);" onclick="manage_events('employee', { 'link':'<?=$link?>' });">
                        <div class="card-body p-2">
                            <div class="d-flex flex-row">
                                <div><span class="icon btn btn-circle btn-lg bg-white pe-none me-3"><i class="uil uil-plus text-blue"></i></span></div>
                                <div class="info-box">
                                    <h4 class="text-white mt-1 mb-0">Employee</h4>
                                    <p class="text-white mb-0">เพิ่มข้อมูลจากรายชื่อบุคลากร</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-4">
                <div class="card bg-orange text-white shadow-lg">
                    <a href="javascript:void(0);" onclick="manage_events('student', { 'link':'<?=$link?>' });">
                        <div class="card-body p-2">
                            <div class="d-flex flex-row">
                                <div><span class="icon btn btn-circle btn-lg bg-white pe-none me-3"><i class="uil uil-plus text-orange"></i></span></div>
                                <div class="info-box">
                                    <h4 class="text-white mt-1 mb-0">Student</h4>
                                    <p class="text-white mb-0">เพิ่มข้อมูลจากรายชื่อนักศึกษา</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-4">
                <div class="card bg-navy text-white shadow-lg">
                    <a href="javascript:void(0);" onclick="manage_events('new', { 'link':'<?=$link?>' });">
                        <div class="card-body p-2">
                            <div class="d-flex flex-row">
                                <div><span class="icon btn btn-circle btn-lg bg-white pe-none me-3"><i class="uil uil-plus text-navy"></i></span></div>
                                <div class="info-box">
                                    <h4 class="text-white mt-1 mb-0">Person</h4>
                                    <p class="text-white mb-0">เพิ่มรายชื่อรายบุคคล</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="table-filter">
    <form name="filter" action="<?=$form?>/lists/search.php" method="POST" enctype="multipart/form-data" target="_blank">
        <input type="hidden" name="state" value="loading" />
        <input type="hidden" name="filter_as" value="<?=$filter_as?>" />
        <input type="hidden" name="events_id" value="<?=$events_id?>" />
        <section class="wrapper bg-primary">
            <div class="container">
                <div class="filter-search">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-6 filter-pageby">
                            <select name="limit" class="form-select mb-1">
                                <option value="100"<?=((!isset($filter['limit'])||intval($filter['limit'])==100)?' selected':null)?>>100</option>
                                <option value="200"<?=((isset($filter['limit'])&&intval($filter['limit'])==200)?' selected':null)?>>200</option>
                                <option value="300"<?=((isset($filter['limit'])&&intval($filter['limit'])==300)?' selected':null)?>>300</option>
                                <option value="500"<?=((isset($filter['limit'])&&intval($filter['limit'])==500)?' selected':null)?>>500</option>
                                <option value="750"<?=((isset($filter['limit'])&&intval($filter['limit'])==750)?' selected':null)?>>750</option>
                                <option value="1000"<?=((isset($filter['limit'])&&intval($filter['limit'])==1000)?' selected':null)?>>1000</option>
                            </select>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-6 filter-keyword">
                            <div class="mc-field-group input-group form-floating mb-1">
                                <input id="keyword" name="keyword" type="text" value="<?=((isset($filter['keyword'])&&$filter['keyword'])?$filter['keyword']:null)?>" class="form-control" placeholder="...">
                                <label for="keyword"><?=Lang::get('Keyword')?></label>
                                <button type="submit" class="btn btn-soft-violet btn-search" title="<?=Lang::get('Search')?>"><i class="uil uil-search"></i></button>
                                <button type="button" class="btn btn-soft-primary btn-clear" title="<?=Lang::get('Clear')?>"><i class="uil uil-filter-slash"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <select name="condition[type]" class="form-select mb-1">
                                <option value="ALL"<?=((!isset($filter['condition']['type'])||$filter['condition']['type']=='ALL')?' selected':null)?>>แสดงทุกประเภท...</option>
                                <option value="EMPLOYEE"<?=((isset($filter['condition']['type'])&&$filter['condition']['type']=='EMPLOYEE')?' selected':null)?>>[EMPLOYEE] พนักงาน</option>
                                <option value="STUDENT"<?=((isset($filter['condition']['type'])&&$filter['condition']['type']=='STUDENT')?' selected':null)?>>[STUDENT] นักศึกษา</option>
                                <option value="OTHER"<?=((isset($filter['condition']['type'])&&$filter['condition']['type']=='OTHER')?' selected':null)?>>[OTHER] บุคคลทั่วไป</option>
                            </select>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <select name="condition[status]" class="form-select mb-1">
                                <option value="ALL"<?=((!isset($filter['condition']['status'])||$filter['condition']['status']=='ALL')?' selected':null)?>>แสดงทุกสถานะ...</option>
                                <option value="CHECKED"<?=((isset($filter['condition']['status'])&&$filter['condition']['status']=='CHECKED')?' selected':null)?>>ลงทะเบียนแล้ว</option>
                                <option value="UNCHECK"<?=((isset($filter['condition']['status'])&&$filter['condition']['status']=='UNCHECK')?' selected':null)?>>ยังไมไ่ด้ลงทะเบียน</option>
                                <option value="CANCELLED"<?=((isset($filter['condition']['status'])&&$filter['condition']['status']=='CANCELLED')?' selected':null)?>>ยกเลิกการลงทะเบียน</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="wrapper">
            <div class="container">
                <div class="filter-result">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col" class="no col-first">#</th>
                                <th scope="col" class="type">ประเภท</th>
                                <th scope="col" class="name">ชื่อ-สกุล</th>
                                <th scope="col" class="organize">สังกัด</th>
                                <th scope="col" class="status">สถานะ</th>
                                <th scope="col" class="actions act-2 col-last">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="filter-display"><span class="badge bg-pale-ash text-dark rounded-pill">- <?=Lang::get('NotFoundResult')?> -</span></div>
                <div class="filter-pagination">
                    <div class="row">
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 filter-prev">
                            <button type="button" class="btn btn-sm<?=((isset($filter['page'])&&$filter['page']==1)?' btn-soft-ash':' btn-primary')?>"><i class="uil uil-angle-left-b"></i><span> <?=Lang::get('Prev')?></span></button>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 filter-page">
                            <center><select name="page" class="page-on form-select"><?php if(isset($filter['pages'])&&$filter['pages']){ ?><?php for($page=1;$page<=intval($filter['pages']);$page++){ ?><option value="<?=$page?>" <?=((isset($filter['page'])&&intval($filter['page'])==$page)?'selected':null)?>><?=$page?></option><?php } ?><?php }else{ ?><option value="1">1</option><?php } ?></select><div class="page-total gb">/<span>1</span></div></center>
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 filter-next">
                            <button type="button" class="btn btn-sm btn-icon btn-icon-end<?=((isset($filter['page'])&&isset($filter['pages'])&&$filter['page']==$filter['pages'])?' btn-soft-ash':' btn-primary')?>"><span><?=Lang::get('Next')?> </span><i class="uil uil-angle-right-b"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <input type="hidden" name="pages" value="<?=((isset($filter['pages'])&&$filter['pages'])?$filter['pages']:0)?>" />
    </form>
</section>
<div id="ManageDialog" class="modal fade" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="false" aria-modal="true"></div>
<script type="text/javascript">
    function manage_events(action, params){
        if(action=='employee'){
            params['form_as'] = '<?=$form?>';
            params['events_id'] = $("form[name='filter'] input[name='events_id']").val();
            $("#ManageDialog").load("<?=$form?>/lists/employee.php", params, function(response, status, xhr){
                if(status=="error"){
                    $(this).html('<div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content text-center">'+xhr.status + "<br>" + xhr.statusText+'<div class="modal-body"></div></div></div>');
                }else{
                    $("#ManageDialog").modal('show');
                }
            });
        }else if(action=='new'){
            params['form_as'] = '<?=$form?>';
            params['events_id'] = $("form[name='filter'] input[name='events_id']").val();
            $("#ManageDialog").load("<?=$form?>/lists/new.php", params, function(response, status, xhr){
                if(status=="error"){
                    $(this).html('<div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content text-center">'+xhr.status + "<br>" + xhr.statusText+'<div class="modal-body"></div></div></div>');
                }else{
                    $("#ManageDialog").modal('show');
                }
            });
        }else if(action=='edit'){
            params['form_as'] = '<?=$form?>';
            $("#ManageDialog").load("<?=$form?>/lists/edit.php", params, function(response, status, xhr){
                if(status=="error"){
                    $(this).html('<div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content text-center">'+xhr.status + "<br>" + xhr.statusText+'<div class="modal-body"></div></div></div>');
                }else{
                    $("#ManageDialog").modal('show');
                }
            });
        }else if(action=='delete'){
            swal({
                'title':'<b class="text-red" style="font-size:100px;"><i class="uil uil-trash-alt"></i></b>',
                'html' : '<div class="fs-24 text-red on-font-primary mb-2">'+params.fullname+'</div><div>ยืนยันลบรายชื่อนี้ ใช่ หรือ ไม่ ?</div>',
                'showCloseButton': false,
                'showConfirmButton': true,
                'showCancelButton': true,
                'focusConfirm': false,
                'allowEscapeKey': false,
                'allowOutsideClick': false,
                'confirmButtonClass': 'btn btn-icon btn-icon-start btn-success rounded-pill',
                'confirmButtonText':'<font class="fs-16"><i class="uil uil-check-circle"></i>ใช่</font>',
                'cancelButtonClass': 'btn btn-icon btn-icon-start btn-outline-danger rounded-pill',
                'cancelButtonText':'<font class="fs-16"><i class="uil uil-times-circle"></i>ไม่</font>',
                'buttonsStyling': false
            }).then(
                function () {
                    $.ajax({
                        url : "<?=$form?>/scripts/lists/delete.php",
                        type: 'POST',
                        data: params,
                        dataType: "json",
                        beforeSend: function( xhr ) {
                            runStart();
                        }
                    }).done(function(data) {
                        runStop();
                        if(data.status=='success'){
                            $("form[name='filter'] button[type='submit']").click();
                        }else{
                            swal({
                                'type' : data.status,
                                'title': '<span class="on-font-primary">'+data.title+'</span>',
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
    $(document).ready(function(){
        $("form[name='filter'] .filter-search select").change(function(){
            $("form[name='filter'] button[type='submit']").click();
        });
        $("form[name='filter'] .filter-search input[name='condition[start_date]']").change(function(){
            $("form[name='filter'] button[type='submit']").click();
        });
        $("form[name='filter'] .filter-search input[name='condition[end_date]']").change(function(){
            $("form[name='filter'] button[type='submit']").click();
        });
        $("form[name='filter'] .filter-search .btn-clear").click(function(){
            $("form[name='filter'] input[name='pages']").val(0);
            $("form[name='filter'] input[name='keyword']").val(null);
            $("form[name='filter'] .filter-search select").val('ALL');
            $("form[name='filter'] .filter-search .form-control").val(null);
            $("form[name='filter'] .filter-pagination select").val(1);
            $("form[name='filter'] button[type='submit']").click();
        });
        $(".table-filter").tablefilter({'keyword':'auto'});
    });
</script>