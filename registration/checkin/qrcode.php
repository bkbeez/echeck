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
        width: 100px;
        height: 100px;
        position: relative;
        animation: mymove 0.8s infinite;
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
    }
    .filter-title-white {
        color: #ffffff; 
        font-weight: bold;
        margin-bottom: 15px;
    }
</style>
<section class="table-filter">
    <div class="filter-card">
        <div class="filter-title filter-title-white">
            <i class="uil uil-qrcode-scan"></i> <?=Lang::get('QR Code Check-In')?>
        </div>
        <div class="filter-result">
            <div class="text-center" style="padding:30px 0;">
                <img src="<?=APP_PATH?>/assets/images/qrcode/qr-code-checkin.png" class="on-top-down" alt="QR Code Check-In">
                <h4 style="margin-top:20px;"><?=Lang::get('Scan QR Code to Check-In')?></h4>
            </div>
        </div>
    </div>
</section>