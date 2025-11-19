<?php if(!isset($index['page'])||$index['page']!='admin'){ header("location:".((isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']==443)?'https://':'http://').$_SERVER["HTTP_HOST"]); exit(); } ?>
<style type="text/css">
    body { background:url('<?=THEME_IMG?>/map.png') top center; }
</style>
<section class="wrapper bg-soft-primary">
    <div class="container pt-2 pb-2 text-center">
        <h3><?=( (App::lang()=='en') ? 'Sessions' : 'ข้อมูลผู้ใช้ระบบ' )?></h3>
    </div>
</section>
<section class="wrapper">
    <div class="container pt-1 pb-1">
        <div class="card mb-2">
            <div class="card-body p-1">
                <a class="collapse-link stretched-link collapsed" data-bs-toggle="collapse" href="#collapse-1" aria-expanded="false">All Sessions</a>
            </div>
            <div id="collapse-1" class="card-footer bg-dark p-5 accordion-collapse collapse">
                <div class="text-white"><?=Helper::debug($_SESSION);?></div>
            </div>
        </div>
        <div class="card mb-2">
            <div class="card-body p-1">
                <a class="collapse-link stretched-link" data-bs-toggle="collapse" href="#collapse-2" aria-expanded="false">User Sessions</a>
            </div>
            <div id="collapse-2" class="card-footer bg-dark p-5 accordion-collapse collapse show">
                <div class="text-white"><?=Helper::debug($_SESSION['login']['user']);?></div>
            </div>
        </div>
    </div>
</section>