<?php if(!isset($index['page'])||$index['page']!='admin'){ header("location:".((isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']==443)?'https://':'http://').$_SERVER["HTTP_HOST"]); exit(); } ?>
<?php
    $formby = $form.'/userlogs';
    $filter_as = strtolower($index['page'].'_userlogs_as');
    $filter = ( isset($_SESSION['login']['filter'][$filter_as]) ? $_SESSION['login']['filter'][$filter_as] : null );
?>
<style type="text/css">
    body { background:url('<?=THEME_IMG?>/map.png') top center; }
    .table-filter .filter-result {
        background: white;
    }
    .table-filter .filter-result .date {
        width: 165px;
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
    .table-filter .filter-result .name>font>i,
    .table-filter .filter-result .name>.date-o,
    .table-filter .filter-result .name>.remark-o {
        display: none;
    }
    @media only all and (max-width: 991px) {
        .table-filter .filter-result .name {
            width: auto;
        }
        .table-filter .filter-result .remark {
            display: none;
        }
        .table-filter .filter-result .name>.remark-o {
            display: block;
            overflow: initial;
            white-space: pre-wrap;
            text-overflow: ellipsis;
        }
    }
    @media only all and (max-width: 768px) {
        .table-filter .filter-result .date {
            display: none;
        }
        .table-filter .filter-result .name>font>i {
            display: inline;
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
                                <button type="submit" class="btn btn-soft-primary btn-search" title="<?=Lang::get('Search')?>"><i class="uil uil-search"></i></button>
                                <button type="button" class="btn btn-purple btn-clear" title="<?=Lang::get('Clear')?>"><i class="uil uil-filter-slash"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <div class="form-floating mb-1">
                                <input name="condition[start_date]" value="<?=((isset($filter['condition']['start_date'])&&$filter['condition']['start_date'])?$filter['condition']['start_date']:null)?>" type="datetime-local" class="form-control" placeholder="...">
                                <label><?=Lang::get('DateStart')?></label>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                            <div class="form-floating mb-1">
                                <input name="condition[end_date]" value="<?=((isset($filter['condition']['end_date'])&&$filter['condition']['end_date'])?$filter['condition']['end_date']:null)?>" type="datetime-local" class="form-control" placeholder="...">
                                <label><?=Lang::get('DateEnd')?></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="wrapper">
            <div class="container pt-1 pb-1">
                <div class="filter-result">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col" class="date"><?=Lang::get('Datetime')?></th>
                                <th scope="col" class="name"><?=( (App::lang()=='en') ? 'User' : 'ผู้ใช้งาน' )?></th>
                                <th scope="col" class="remark">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="filter-display"><span class="badge bg-pale-ash text-dark rounded-pill">- <?=Lang::get('NotFoundResult')?> -</span></div>
                <div class="filter-pagination">
                    <div class="row">
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 filter-prev">
                            <button type="button" class="btn btn-icon btn-icon-start<?=((isset($filter['page'])&&$filter['page']==1)?' btn-white':' btn-primary')?>"><i class="uil uil-angle-left-b"></i><span> <?=Lang::get('Prev')?></span></button>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 filter-page">
                            <center><select name="page" class="page-on form-select"><?php if(isset($filter['pages'])&&$filter['pages']){ ?><?php for($page=1;$page<=intval($filter['pages']);$page++){ ?><option value="<?=$page?>" <?=((isset($filter['page'])&&intval($filter['page'])==$page)?'selected':null)?>><?=$page?></option><?php } ?><?php }else{ ?><option value="1">1</option><?php } ?></select><div class="page-total">/<span>1</span></div></center>
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 filter-next">
                            <button type="button" class="btn btn-icon btn-icon-end<?=((isset($filter['page'])&&isset($filter['pages'])&&$filter['page']==$filter['pages'])?' btn-white':' btn-primary')?>"><span><?=Lang::get('Next')?> </span><i class="uil uil-angle-right-b"></i></button>
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
    $(document).ready(function(){
        $("form[name='filter'] .filter-search input[type='datetime-local']").change(function(){
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