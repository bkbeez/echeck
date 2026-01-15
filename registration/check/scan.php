<?php if(!isset($index['page'])||$index['page']!='scan'){ header("location:".((isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']==443)?'https://':'http://').$_SERVER["HTTP_HOST"]); exit(); } ?>
<?php include(APP_HEADER); ?>
<style type="text/css">
    body { 
        background: #f4f7f6 url('<?=THEME_IMG?>/map.png') center center;
        background-attachment: fixed;
    }
    .on-hamburger, .navbar-collapse-wrapper {
        display: none !important;
    }
    .main-container {
        padding-top: 30px;
        padding-bottom: 30px;
    }
    .scanner-section {
        background: #ffffff;
        border-radius: 25px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        overflow: hidden; height: 100%;
        border: 1px solid #eee;
    }
    .qrcode-scanner {
        width: 100%;
        padding: 20px;
        text-align: center;
        position: relative;
    }
    .start-card .card-body {
        text-align: left;
    }
    .start-card{
        background: linear-gradient(135deg, #747ed1 0%,#5b66c4 100%);
        border-radius: 20 px
    }
    
    #reader {
        width: 100% !important;
        border: none !important;
        position: relative;
    }
    #reader__scan_region {
        border-radius: 20px;
        background: transparent !important;
        overflow: hidden;
    }
    #reader__dashboard_section_csr,
    #reader__header_message,
    #reader img[alt='Info icon'] { display: none !important; }
    .html5-qrcode-element {
        border-radius: 12px !important;
        padding: 12px 25px !important;
        font-weight: 600 !important;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        border: none !important;
        margin-top: 15px !important;
    }
    #html5-qrcode-button-camera-start {
        background: #747ed1 !important;
        color: white !important;
        box-shadow: 0 4px 15px rgba(116, 126, 209, 0.4);
    }
    #html5-qrcode-button-camera-start:hover {
        background: #615eba !important;
        transform: translateY(-2px);
    }
    #html5-qrcode-button-camera-stop {
        background: #ff5e5e !important;
        color: white !important;
    }
    .stat-card {
        background: linear-gradient(135deg, #747ed1 0%, #615eba 100%); 
        color: white;
        border-radius: 25px;
        border: none;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }
    .list-card {
        background: white;
        border-radius: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: none; }
    .table-responsive {
        max-height: 480px;
        overflow-y: auto;
    }
    .pulse-live {
        width: 8px;
        height: 8px;
        background: #52f37a;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
        animation: pulse-green 2s infinite;
    }
    @keyframes pulse-green {
        0% { transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(82, 243, 122, 0.7);
    }
        70% { transform: scale(1);
        box-shadow: 0 0 0 10px rgba(82, 243, 122, 0);
    }
        100% { transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(82, 243, 122, 0);
    }
    }
    @keyframes highlight-count {
        0% { color: #52f37a; transform: scale(1.1); } /* สีเขียวสว่างและขยายใหญ่ขึ้นเล็กน้อย */
        100% { color: white; transform: scale(1); }    /* กลับเป็นสีขาวและขนาดปกติ */
    }
    .count-updated {
        animation: highlight-count 1s ease-out;
    }
    

</style>

<div class="container-fluid main-container">
    <div class="row g-4">
        
        <div class="col-lg-5 col-md-12">
            <div class="scanner-section">
                <div class="p-4 bg-primary text-white text-center">
                    <h4 class="mb-1 fw-bold"><i class="uil uil-qr-code"></i> สแกนลงทะบียนกิจกรรม</h4>
                    <p class="small mb-0 opacity-75">วาง QR Code ให้อยู่ในกรอบเพื่อสแกน</p>
                </div>
                
                <div class="qrcode-scanner">
                    <div id="reader"></div>
                    <div id="result" style="display:none;"></div>
                </div>

                <div class="p-4 text-center bg-light border-top">
                    <div class="d-flex align-items-center justify-content-center text-muted">
                        <i class="uil uil-shield-check text-success me-2 fs-20"></i>
                        <span class="small">ระบบบันทึกข้อมูลอัตโนมัติเมื่อสแกนสำเร็จ</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7 col-md-12">
            <div class="dashboard-section">
                
                <div class="card stat-card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-white-50 mb-1 fw-medium">จำนวนผู้เข้าร่วมกิจกรรมขณะนี้</p>
                                <h1 class="display-3 fw-bold mb-0" id="total-participants">0</h1>
                                <p class="mb-0 mt-2 small"><span class="pulse-live"></span> กำลังติดตามข้อมูลแบบ Real-time</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card list-card shadow-sm">
                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">ผู้เช็คอินล่าสุด</h5>
                        <button onclick="fetchDashboardData()" class="btn btn-sm btn-ghost-secondary rounded-circle">
                            <i class="uil uil-refresh"></i>
                        </button>
                    </div>
                    <div class="search">
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-6 filter-keyword">
                            <div class="mc-field-group input-group form-floating mb-1">
                                <input id="keyword" name="keyword" type="text" value="<?=((isset($filter['keyword'])&&$filter['keyword'])?$filter['keyword']:null)?>" class="form-control" placeholder="...">
                                <label for="keyword"><?=Lang::get('Keyword')?></label>
                                <button type="submit" class="btn btn-soft-violet btn-search" title="<?=Lang::get('Search')?>"><i class="uil uil-search"></i></button>
                                <button type="button" class="btn btn-soft-primary btn-clear" title="<?=Lang::get('Clear')?>"><i class="uil uil-filter-slash"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4 border-0">เวลา</th>
                                        <th class="border-0">ข้อมูลผู้เข้าร่วม</th>
                                        <th class="text-end pe-4 border-0">สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody id="participant-list">
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">กำลังโหลดข้อมูล...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var html5QrcodeScanner = new Html5QrcodeScanner("reader", { 
        qrbox: { width: 250, height: 250 }, 
        fps: 25,
        aspectRatio: 1.0,
        rememberLastUsedCamera: true,
        supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
    });
    function onScanRender() {
        html5QrcodeScanner.render(onScanSuccess, onScanError);
    }
    function onScanSuccess(decodedText, decodedResult) {
        if (navigator.vibrate) navigator.vibrate(100);
        html5QrcodeScanner.clear(); 
        $.ajax({
            url: "<?=APP_PATH.'/scan/scanning.php'?>",
            type : 'POST',
            data:{ 'decoded_text':decodedText },
            dataType: "json",
            beforeSend: function() { runStart(); }
        }).done(function(rs) {
            runStop();
            if(rs.status=='success'){
                var currentCount = parseInt($('#total-participants').text());
                $('#total-participants').text(currentCount + 1);
                $("body").append(rs.htmls);
                $("form[name='OpenForm'] input[type='submit']").click();
            } else {
                swal({
                    type: rs.status,
                    title: rs.title,
                    text: rs.text,
                    confirmButtonColor: '#747ed1',
                    confirmButtonText: 'ลองอีกครั้ง'
                }).then(() => { onScanRender(); });
            }
        });
    }
    //scan
    $(document).ready(function() {
        if(typeof(EventSource) !== "undefined") {
            var source = new EventSource("<?=APP_PATH?>/scripts/counting.php");
            source.onmessage = function(event) {
                var result = JSON.parse(event.data);
                $('#total-participants').text(result.count);
                var listHtml = '';
                if(result.list.length > 0) {
                    $.each(result.list, function(i, item) {
                        listHtml += `
                            <tr>
                                <td class="ps-4"><span class="badge bg-light text-muted">${item.time}</span></td>
                                <td>
                                    <div class="fw-bold text-dark">${item.name}</div>
                                    <div class="small text-muted">${item.org || '-'}</div>
                                </td>
                                <td class="text-end pe-4">
                                    <span class="badge bg-soft-success text-success"><i class="uil uil-check-circle"></i> สำเร็จ</span>
                                </td>
                            </tr>`;
                    });
                } else {
                    listHtml = '<tr><td colspan="3" class="text-center py-4">ยังไม่มีข้อมูลการเช็คอิน</td></tr>';
                }
                $('#participant-list').html(listHtml);
            };
            source.onerror = function() {
                console.log("EventSource failed. Retrying...");
            };
        } else {
            console.log('Browser does not support Server-Sent Events');
            setInterval(fetchDashboardData, 5000);
        }
        
        onScanRender();
    });
    function fetchDashboardData() {
        $.getJSON("<?=APP_PATH?>/scan/get_dashboard.php", function(rs) {
            if(rs.status == 'success') {
                $('#total-participants').text(rs.count);
            }
        });
    }
</script>
<?php include(APP_FOOTER); ?>