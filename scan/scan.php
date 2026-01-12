<?php if(!isset($index['page'])||$index['page']!='scan'){ header("location:".((isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']==443)?'https://':'http://').$_SERVER["HTTP_HOST"]); exit(); } ?>
<?php include(APP_HEADER); ?>
<style type="text/css">
    body { background:url('<?=THEME_IMG?>/map.png') center center; }
    .on-hamburger,
    .navbar-collapse-wrapper {
        display: none !important;
    }
    .scan-header .profile-box {
        height: 90px;
        overflow: hidden;
        margin-top: -1rem;
    }
    .scan-header .profile-box h2 {
        color: white;
        font-size: 22px;
        line-height: 36px;
        font-weight: normal;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .qrcode-scanner {
        width: 320px;
        text-align: center;
    }
    .qrcode-scanner #reader {
        width: 100% !important;
        border: none !important;
    }
    .qrcode-scanner #reader img[alt='Info icon']{
        display: none;
    }
    .qrcode-scanner #reader button.html5-qrcode-element {
        width: 92%;
        color: #747ed1;
        outline: none;
        font-size: 16px;
        padding: 8px 12px;
        text-align: center;
        display: inline-block;
        cursor: pointer;
        margin:0 0 10px 0;
        border:2px solid #ebebeb;
        background-color: #ebebeb;
        -webkit-border-radius: 0.4rem;
        -moz-border-radius: 0.4rem;
        border-radius: 0.4rem;
    }
    .qrcode-scanner #reader #html5-qrcode-button-file-selection {
        padding-left: 12px;
        padding-right: 12px;
        font-weight: normal !important;
    }
    .qrcode-scanner #reader label[for='html5-qrcode-private-filescan-input'] {
        width: 100%;
        margin-bottom: -10px !important;
    }
    .qrcode-scanner #reader button.html5-qrcode-element:hover {
        color: #747ed1;
        border-color: #e6e5f4;
        background-color: #e6e5f4;
    }
    .qrcode-scanner #reader #html5-qrcode-button-camera-start,
    .qrcode-scanner #reader #html5-qrcode-button-camera-stop {
        margin-top: 10px;
    }
    .qrcode-scanner #reader a.html5-qrcode-element,
    .qrcode-scanner #reader span.html5-qrcode-element {
        color: white;
        outline: none;
        margin: 0;
        width: 98%;
        font-size: 20px;
        padding: 15px 0;
        cursor: pointer;
        text-align: center;
        display: inline-block;
        background-color: #747ed1;
        text-decoration: none !important;
        -webkit-border-radius: 0.4rem;
        -moz-border-radius: 0.4rem
        border-radius: 0.4rem
    }
    .qrcode-scanner #reader a.html5-qrcode-element:focus,
    .qrcode-scanner #reader a.html5-qrcode-element:hover,
    .qrcode-scanner #reader span.html5-qrcode-element:focus,
    .qrcode-scanner #reader span.html5-qrcode-element:hover {
        background-color: #615eba;
    }
    .qrcode-scanner #reader select.html5-qrcode-element {
        height: 35px;
        line-height: 24px;
        text-align: center;
        margin-top: 10px;
    }
    .qrcode-scanner #reader select.html5-qrcode-element option {
        padding: 6px;
        text-align: center;
        line-height: 24px;
    }
    .qrcode-scanner #reader #reader__scan_region {
        width: 100% !important;
        height: 320px !important;
    }
    .qrcode-scanner #reader #reader__scan_region video {
        height: 320px !important;
    }
    .qrcode-scanner #reader #qr-canvas-visible {
        overflow: hidden;
        width: auto !important;
        height: 320px !important;
    }
    .qrcode-scanner #reader #reader__header_message {
        width: 100% !important;
        border:none !important;
        margin: 0 !important;
        padding: 6px !important;
        text-align: center;
        display: inline-block;
        position: absolute;
    }
    .qrcode-scanner #reader #reader__dashboard_section>div>div:last-child {
        width: 100% !important;
        padding-left: 0!important;
        padding-right: 0!important;
        max-width: auto !important;
    }
    .qrcode-scanner #reader #reader__dashboard_section_csr {
        min-height: 72.14px;
        margin-bottom: 10px;
        border: 6px dashed rgb(235, 235, 235);
    }
    .qrcode-scanner #reader #reader__dashboard_section_csr>div:first-child {
        width: 100%;
        padding-top: 10px;
    }
    .qrcode-scanner .openlink {
        bottom: 152px;
        left: 130px;
        float: left;
        position: absolute;
        background: #FFF;
        border:1px solid #ddd;
        -webkit-border-radius: 25px;
        -moz-border-radius: 25px;
        border-radius: 25px;
        padding: 6px 12px;
        cursor: pointer;
    }
    .qrcode-scanner .openlink:hover {
        color: white;
        background: #2e6da4;
        border-color: #2e6da4;
    }
    @media only all and (max-width: 991px) {
        .scan-header .profile-box {
            margin-top: 1rem;
        }
        .scan-header .profile-box h1 {
            line-height: 68px;
        }
    }
</style>
<section class="wrapper bg-primary">
    <div class="container">
        <h1 class="on-font-primary text-white text-center pb-3">สแกน <span class="underline-3 style-3 red">QR Code</span> ของกิจกรรม</h1>
    </div>
</section>
<section class="wrapper">
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3"></div>
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                <div class="card bg-primary mt-n4 mb-3">
                    <div class="card-body text-white text-center" style="padding:5px 0 15px 0 !important;">
                        <span class="badge bg-pale-primary text-primary rounded-pill" style="padding-top:3px;line-height:20px;">Choose - Camera</span> &rarr; <span class="badge bg-pale-primary text-primary rounded-pill" style="padding-top:3px;line-height:20px;">Start</span>
                        <br><small class="text-white">* Use main camera or back camera</small>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3"></div>
        </div>
    </div>
    <center>
        <div class="qrcode-scanner">
            <div id="reader"></div>
            <div id="result" style="display:none;"></div>
            <a id="OpenLink" href="javascript::void();" style="display:none;">OpenLink</a>
            <a id="OpenOuterLink" href="javascript::void();" target="_blank" style="display:none;">OpenOuterLink</a>
        </div>
    </center>
</section>
<script type="text/javascript">
    var html5QrcodeScanner = new Html5QrcodeScanner("reader", { qrbox:{ width:240, height:240 }, fps: 20 });
    function onScanRender() {
        html5QrcodeScanner.render(onScanSuccess, onScanError);
        $("#html5-qrcode-anchor-scan-type-change").click(function(){
            $("#result").html('');
            $(".qrcode-scanner .openlink").remove();
            document.getElementById('OpenOuterLink').href = 'javascript:void(0)';
        });
    }
    function onScanSuccess(decodedText, decodedResult) {
        $("#result").html(decodedText);
        $("#html5-qrcode-button-camera-stop").click();
        $.ajax({
            url: "<?=APP_PATH.'/scan/scanning.php'?>",
            type : 'POST',
            data:{ 'decoded_text':decodedText },
            dataType: "json",
            beforeSend: function( xhr ) {
                runStart();
            }
        }).done(function(rs) {
            runStop();
            if(rs.status=='success'){
                $("body").append(rs.htmls);
                $("form[name='OpenForm'] input[type='submit']").click();
            }else{
                swal({
                    'type' : rs.status,
                    'title': rs.title,
                    'html' : rs.text,
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
                        onScanRender();
                        swal.close();
                    },
                    function (dismiss) {
                        if (dismiss === 'cancel') {
                            onScanRender();
                            swal.close();
                        }
                    }
                );
            }
        });
    }
    function onScanError(errorMessage) {
        document.getElementById('OpenOuterLink').href = 'javascript:void(0)';
        $(".qrcode-scanner .openlink").remove();
    }
    $(document).ready(function(){
        onScanRender();
    });
</script>
<?php include(APP_FOOTER); ?>