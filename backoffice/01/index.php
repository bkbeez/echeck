<?php if(!isset($index['page'])||$index['page']!='backoffice'){ header("location:".((isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']==443)?'https://':'http://').$_SERVER["HTTP_HOST"]); exit(); } ?>
<?php
    $filter_as = strtolower($index['page'].'_'.$tabby.'_as');
    $filter = ( isset($_SESSION['login']['filter'][$filter_as]) ? $_SESSION['login']['filter'][$filter_as] : null );
    $institutes = DB::sql("SELECT * FROM tbl_institute WHERE status_id=1 ORDER BY id;");
?>
<div class="table-filter">
    <form name="filter" action="<?=$form.'/'.$tabby?>/search.php" method="POST" enctype="multipart/form-data" target="_blank">
        <input type="hidden" name="state" value="loading" />
        <input type="hidden" name="filter_as" value="<?=$filter_as?>" />
        <input type="hidden" name="meeting_id" value="<?=$meeting_id?>" />
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
                        <button type="submit" class="btn btn-soft-ash btn-search" title="<?=Lang::get('Search')?>"><i class="uil uil-search"></i></button>
                        <button type="button" class="btn btn-soft-primary btn-clear" title="<?=Lang::get('Clear')?>"><i class="uil uil-sliders-v-alt"></i></button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-6">
                    <select name="condition[institute]" class="form-select mb-1">
                        <option value="ALL"<?=((!isset($filter['condition']['institute'])||$filter['condition']['institute']=='ALL')?' selected':null)?>><?=( (App::lang()=='en') ? 'All Institutes' : 'ทุกสถาบัน' )?>...</option>
                        <?php if( isset($institutes)&&count($institutes)>0 ){ ?>
                            <?php foreach($institutes as $seq => $item){ ?>
                                <option value="<?=$item['id']?>"<?=((isset($filter['condition']['institute'])&&$filter['condition']['institute']==$item['id'])?' selected':null)?>><?=$item['name_'.App::lang()]?></option>
                            <?php } ?>
                            <option value="OTHER"<?=((isset($filter['condition']['institute'])&&$filter['condition']['institute']=='OTHER')?' selected':null)?>><?=( (App::lang()=='en') ? 'Other' : 'สถาบันอื่นๆ' )?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3">
                    <select name="condition[participant]" class="form-select mb-1">
                        <option value="ALL"<?=((!isset($filter['condition']['participant'])||$filter['condition']['participant']=='ALL')?' selected':null)?>><?=( (App::lang()=='en') ? 'All Types' : 'ทุกประเภท' )?>...</option>
                        <option value="AAPN"<?=((isset($filter['condition']['participant'])&&$filter['condition']['participant']=='AAPN')?' selected':null)?>><?=( (App::lang()=='en') ? 'National Paper Presenters' : 'ผู้เสนอบทความระดับชาติ' )?></option>
                        <option value="AAPI"<?=((isset($filter['condition']['participant'])&&$filter['condition']['participant']=='AAPI')?' selected':null)?>><?=( (App::lang()=='en') ? 'International Paper Presenters' : 'ผู้เสนอบทความระดับนานาชาติ' )?></option>
                    </select>
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-3">
                    <select name="condition[status]" class="form-select mb-1">
                        <option value="ALL"<?=((!isset($filter['condition']['status'])||$filter['condition']['status']=='ALL')?' selected':null)?>><?=( (App::lang()=='en') ? 'All Status' : 'ทุกสถานะ' )?>...</option>
                        <option value="NOTP"<?=((isset($filter['condition']['status'])&&$filter['condition']['status']=='NOTP')?' selected':null)?>><?=( (App::lang()=='en') ? 'Not payment' : 'ยังไม่ได้ชำระเงิน' )?></option>
                        <option value="WAIT"<?=((isset($filter['condition']['status'])&&$filter['condition']['status']=='WAIT')?' selected':null)?>><?=( (App::lang()=='en') ? 'Wait for checking...' : 'รอตรวจชำระเงิน...' )?></option>
                        <option value="BACK"<?=((isset($filter['condition']['status'])&&$filter['condition']['status']=='BACK')?' selected':null)?>><?=( (App::lang()=='en') ? 'Returned, Wait for editing...' : 'ส่งคืนแล้ว, รอแก้ไข...' )?></option>
                    </select>
                </div>
            </div>
        </div>
        <div class="filter-result">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col" class="no">#</th>
                        <th scope="col" class="date"><?=( (App::lang()=='en') ? 'Regist Date' : 'วันที่ลงทะเบียน' )?></th>
                        <th scope="col" class="name"><?=( (App::lang()=='en') ? 'Register' : 'ผู้ลงทะเบียน' )?></th>
                        <th scope="col" class="institute"><?=Lang::get('Institute')?></th>
                        <th scope="col" class="amount"><?=( (App::lang()=='en') ? 'Regist Fee' : 'ค่าลงทะเบียน' )?></th>
                        <th scope="col" class="actions act-2">&nbsp;</th>
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
        <input type="hidden" name="pages" value="<?=((isset($filter['pages'])&&$filter['pages'])?$filter['pages']:0)?>" />
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $("form[name='filter'] .filter-search select").change(function(){
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