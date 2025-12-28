<?php if(!isset($index['page'])||$index['page']!='admin'){ header("location:".((isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']==443)?'https://':'http://').$_SERVER["HTTP_HOST"]); exit(); } ?>
<?php
    $formby = $form.'/userlogs';
    $filter_as = strtolower($index['page'].'_userlog_as');
    $filter = ( isset($_SESSION['login']['filter'][$filter_as]) ? $_SESSION['login']['filter'][$filter_as] : null );
?>
<style type="text/css">
    .table-filter .filter-result {
        background: white;
    }
    .table-filter .filter-result .date {
        width: 185px;
        padding-left: 22px;
    }
    .table-filter .filter-result .mail {
        width: 25%;
    }
    .table-filter .filter-result .name {
        width: 25%;
    }
    .table-filter .filter-result .remark {
        width: auto;
    }
    .table-filter .filter-result .name>span {
        display: block;
        font-weight: normal;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .table-filter .filter-result .name>.date-o,
    .table-filter .filter-result .name>.mail-o,
    .table-filter .filter-result .name>.name-o,
    .table-filter .filter-result .name>.remark-o {
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
    @media only all and (max-width: 991px) {
        .table-filter .filter-result .mail {
            width: 35%;
        }
        .table-filter .filter-result .name {
            width: auto;
        }
        .table-filter .filter-result .remark {
            display: none;
        }
        .table-filter .filter-result .name>.remark-o {
            display: block;
        }
    }
    @media only all and (max-width: 768px) {
        .table-filter .filter-result .mail,
        .table-filter .filter-result .name>font {
            display: none;
        }
        .table-filter .filter-result .name>.name-o {
            display: block;
        }
        .table-filter .filter-result .name>.mail-o {
            display: inline;
        }
    }
    @media only all and (max-width: 450px) {
        .table-filter .filter-result .name {
            padding-left: 22px;
        }
        .table-filter .filter-result .date {
            display: none;
        }
        .table-filter .filter-result .name>.date-o {
            display: block;
        }
    }
</style>
<section class="table-filter">
    <form name="filter" action="<?=$formby?>/filter/search.php" method="POST" enctype="multipart/form-data" target="_blank">
        <input type="hidden" name="state" value="loading" />
        <input type="hidden" name="filter_as" value="<?=$filter_as?>" />
        <section class="wrapper bg-primary">
            <div class="container">
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
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <div class="form-floating mb-1">
                                <input name="condition[start_date]" type="text" value="<?=((isset($filter['condition']['start_date'])&&$filter['condition']['start_date'])?$filter['condition']['start_date']:null)?>" class="form-control" data-provide="datepicker" data-date-language="th-th" pattern="\d{1,2}/\d{1,2}/\d{4}" autocomplete="off" placeholder="..." minlength="10" maxlength="10" onkeyup="this.value=this.value.replace(/[^0-9/:]/g,'');"/>
                                <label>วันที่เริ่มต้น</label>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <div class="form-floating mb-1">
                                <input name="condition[end_date]" type="text" value="<?=((isset($filter['condition']['end_date'])&&$filter['condition']['end_date'])?$filter['condition']['end_date']:null)?>" class="form-control" data-provide="datepicker" data-date-language="th-th" pattern="\d{1,2}/\d{1,2}/\d{4}" autocomplete="off" placeholder="..." minlength="10" maxlength="10" onkeyup="this.value=this.value.replace(/[^0-9/:]/g,'');"/>
                                <label>วันที่สิ้นสุด</label>
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
                                <th scope="col" class="date col-first">วันที่/เวลา</th>
                                <th scope="col" class="mail">อีเมล</th>
                                <th scope="col" class="name">ชื่อผู้ใช้</th>
                                <th scope="col" class="remark">&nbsp;</th>
                                <th scope="col" class="actions col-last">&nbsp;</th>
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
        if(action=='detail'){
            params['form_as'] = '<?=$formby?>';
            $("#ManageDialog").load("<?=$formby?>/filter/detail.php", params, function(response, status, xhr){
                if(status=="error"){
                    $(this).html('<div class="modal-dialog modal-dialog-centered modal-sm"><div class="modal-content text-center">'+xhr.status + "<br>" + xhr.statusText+'<div class="modal-body"></div></div></div>');
                }else{
                    $("#ManageDialog").modal('show');
                }
            });
        }
    }
    $(document).ready(function(){
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