<?php
    if(!isset($index['page'])||$index['page']!='registration'){ header("location:".((isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']==443)?'https://':'http://').$_SERVER["HTTP_HOST"]); exit(); } 
?>
<?php
    $filter_as = strtolower($index['page'].'_event_as');
    $filter = ( isset($_SESSION['login']['filter'][$filter_as]) ? $_SESSION['login']['filter'][$filter_as] : null );
?>
<style type="text/css">
    .table-filter .filter-result {
        background: white;
    }
    .table-filter .filter-result .type {
        width: 65px;
    }
    .table-filter .filter-result .name {
        width: auto;
    }
    .table-filter .filter-result .date {
        width: 120px;
    }
    .table-filter .filter-result .status {
        width: 120px;
    }
    .table-filter .filter-result .name>.type-o,
    .table-filter .filter-result .name>.date-o,
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
    .table-filter .filter-result .badge.badge-list {
        padding-left: 8px;
        padding-right: 6px;
    }
    .table-filter .filter-result .badge.badge-shared {
        min-width: 62px;
        cursor: pointer;
    }
    .table-filter .filter-result .badge.badge-status {
        cursor: pointer;
    }
    .table-filter .filter-result .badge.badge-shared:hover {
        background: #747ed1 !important;
    }
    .table-filter .filter-result .badge.badge-status.bg-orange:hover {
        background: orange !important;
    }
    .table-filter .filter-result .badge.badge-status.bg-green:hover {
        background: green !important;
    }
    .table-filter .filter-result .badge.badge-status.bg-red:hover {
        background: red !important;
    }
    .on-top-down {
        display: inline-block;
        font-weight: bold;
        color: #5d5fef;
        font-size: 22px;
        animation: bounce 1s infinite;
    }
    
    @keyframes mymove {
        0%   {top: 0px;}
        25%  {top: 3px;}
        75%  {top: 7px}
        100% {top: 9px;}
    }
    @media only all and (max-width: 991px) {
        .table-filter .filter-result .date {
            display: none;
        }
        .table-filter .filter-result .name>.date-o {
            display: block;
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
        .btn-primary.rounded-3 {
            background-color: #5d5fef;
            border: none;
        }
        .btn-primary.rounded-3:hover {
            background-color: #4a4cd9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(93, 95, 239, 0.4) !important;
        }
    }
    .btn-soft-orange {
        background-color: rgba(255, 165, 0, 0.1);
        color: #ff8c00;
        border: 1px solid rgba(255, 165, 0, 0.2);
    }
    .btn-soft-orange:hover {
        background-color: #ff8c00;
        color: #ffffff;
    }
    .me-2 { margin-right: 0.5rem !important; }
</style>
<section class="table-filter">
    <form name="filter" action="<?=$form?>/filter/search.php" method="POST" enctype="multipart/form-data" target="_blank">
        <input type="hidden" name="state" value="loading" />
        <input type="hidden" name="filter_as" value="<?=$filter_as?>" />
        <section class="wrapper bg-primary">
            <div class="container">
                <div class="filter-box">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h3 class="filter-title-white d-flex align-items-center justify-content-center text-white">
                                <i class="uil uil-calendar-alt me-2" style="font-size: 32px; color: #ffffff;"></i>
                                <span style="font-weight: 600; letter-spacing: 0.5px; color: #ffffff;">
                                    <?=Lang::get('เลือกกิจกรรม เพื่อลงทะเบียน')?>
                                </span>
                            </h3>
                        </div>
                        <div class="filter-search">
                            <div class="row">
                                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-6 filter-pageby">
                                    <select name="limit" class="form-select mb-1">
                                        <option value="50"<?=((!isset($filter['limit'])||intval($filter['limit'])==50)?' selected':null)?>>50</option>
                                        <option value="100"<?=((isset($filter['limit'])&&intval($filter['limit'])==100)?' selected':null)?>>100</option>
                                        <option value="250"<?=((isset($filter['limit'])&&intval($filter['limit'])==250)?' selected':null)?>>250</option>
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
                                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                    <select name="condition[participant_type]" class="form-select mb-1">
                                        <option value="ALL"<?=((!isset($filter['condition']['participant_type'])||$filter['condition']['participant_type']=='ALL')?' selected':null)?>>แสดงทุกประเภท...</option>
                                        <option value="ALLS"<?=((isset($filter['condition']['participant_type'])&&$filter['condition']['participant_type']=='ALLS')?' selected':null)?>>[ALL] ทั่วไป</option>
                                        <option value="LIST"<?=((isset($filter['condition']['participant_type'])&&$filter['condition']['participant_type']=='LIST')?' selected':null)?>>[LIST] เฉพาะผู้ที่มีรายชื่อ</option>
                                    </select>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
                                    <select name="condition[status]" class="form-select mb-1">
                                        <option value="ALL"<?=((!isset($filter['condition']['status'])||$filter['condition']['status']=='ALL')?' selected':null)?>>แสดงทุกสถานะ...</option>
                                        <option value="DRAFT"<?=((isset($filter['condition']['status'])&&$filter['condition']['status']=='DRAFT')?' selected':null)?>>[DRAFT] ร่าง</option>
                                        <option value="OPEN"<?=((isset($filter['condition']['status'])&&$filter['condition']['status']=='OPEN')?' selected':null)?>>[OPEN] เปิดลงทะเบียน</option>
                                        <option value="CLOSE"<?=((isset($filter['condition']['status'])&&$filter['condition']['status']=='CLOSE')?' selected':null)?>>[CLOSE] ปิดลงทะเบียน</option>
                                    </select>
                                </div>
                            </div>
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
                                <th scope="col" class="name" style="width: 250px;">กิจกรรม</th>
                                <th scope="col" class="status text-start" style="width: 120px;">
                                    <span class="on-top-down">&darr;</span> <?=Lang::get('จำนวนผู้ลงทะเบียน')?>
                                </th>
                                <th scope="col" class="actions text-strrt" style="width: 120px;">
                                    <span class="on-top-down">&darr;</span> <?=Lang::get('ลงทะเบียนที่นี่')?>
                                </th>
                                <th scope="col" class="status text-start" style="width: 180px;">
                                    <span class="on-top-down">&darr;</span> <?=Lang::get('คลิก')?>
                                </th>
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
<div id="ManageDialog" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="false" aria-modal="true"></div>
<script type="text/javascript">
function manage_events(action, params){
    if(action=='check'){
        params['form_as']='<?=$form?>';
        $("#ManageDialog").load("<?=$form?>/check/scan.php", params, function(response, status, xhr){
                if(status=="error"){
                    $(this).html('<div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content text-center">'+xhr.status + "<br>" + xhr.statusText+'<div class="modal-body"></div></div></div>');
                }else{
                    $("#ManageDialog").modal('show');
                }
            });
    }else if(action=='edit'){
        params['form_as']='<?=$form?>';
        $("#ManageDialog").load("<?=$form?>/filter/edit.php", params, function(response, status, xhr){
                if(status=="error"){
                    $(this).html('<div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content text-center">'+xhr.status + "<br>" + xhr.statusText+'<div class="modal-body"></div></div></div>');
                }else{
                    $("#ManageDialog").modal('show');
                }
        });
    }else if(action=='pdf'){
    window.open('<?=$form?>/filter/pdf.php?events_id=' + params['events_id'], '_blank');
    }else if(action=='excel'){
        window.open('<?=$form?>/filter/excel.php?events_id=' + params['events_id'], '_blank');
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