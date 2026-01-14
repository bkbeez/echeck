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
    #html5-qrcode-button-camera-start { background: #747ed1 !important; color: white !important; box-shadow: 0 4px 15px rgba(116, 126, 209, 0.4); }
    #html5-qrcode-button-camera-start:hover { background: #615eba !important; transform: translateY(-2px); }
    #html5-qrcode-button-camera-stop { background: #ff5e5e !important; color: white !important; }
    .stat-card { 
        background: linear-gradient(135deg, #747ed1 0%, #615eba 100%); 
        color: white; border-radius: 25px; border: none; margin-bottom: 20px;
        position: relative; overflow: hidden;
    }
    .list-card { background: white; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: none; }
    .table-responsive { max-height: 480px; overflow-y: auto; }
    
    .pulse-live {
        width: 8px; height: 8px; background: #52f37a; border-radius: 50%;
        display: inline-block; margin-right: 8px; animation: pulse-green 2s infinite;
    }
    @keyframes pulse-green {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(82, 243, 122, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(82, 243, 122, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(82, 243, 122, 0); }
    }
</style>

<div class="container-fluid main-container">
    <div class="row g-4">
        
        <div class="col-lg-5 col-md-12">
            <div class="scanner-section">
                <div class="p-4 bg-primary text-white text-center">
                    <h4 class="mb-1 fw-bold"><i class="uil uil-qr-code"></i> สแกนเช็คอิน</h4>
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
                                <p class="text-white-50 mb-1 fw-medium">จำนวนผู้เข้าร่วมงานขณะนี้</p>
                                <h1 class="display-3 fw-bold mb-0" id="total-participants">0</h1>
                                <p class="mb-0 mt-2 small"><span class="pulse-live"></span> กำลังติดตามข้อมูลแบบ Real-time</p>
                            </div>
                            <div class="bg-white bg-opacity-20 p-3 rounded-circle">
                                <i class="uil uil-users-alt text-white fs-30"></i>
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
    function onScanError(errorMessage) { }
    $(document).ready(function() {
        onScanRender();
        if(typeof(EventSource) !== "undefined") {
            var source = new EventSource("<?=$form?>/scripts/counting.php");
            source.onmessage = function(event) {
                var result = JSON.parse(event.data);
                if(result.status === 'success') {
                    $('#total-participants').text(result.count);
                    let html = '';
                    if(result.list && result.list.length > 0) {
                        result.list.forEach(function(item) {
                            html += `<tr>
                                <td class="ps-4"><span class="badge bg-soft-primary text-primary rounded-pill px-3">${item.time}</span></td>
                                <td>
                                    <div class="fw-bold text-dark">${item.name}</div>
                                    <div class="small text-muted">${item.org || '-'}</div>
                                </td>
                                <td class="text-end pe-4">
                                    <span class="badge bg-soft-success text-success border-0"><i class="uil uil-check"></i> สำเร็จ</span>
                                </td>
                            </tr>`;
                        });
                    } else {
                        html = '<tr><td colspan="3" class="text-center py-5 text-muted">ยังไม่พบข้อมูลการเช็คอินในวันนี้</td></tr>';
                    }
                    $('#participant-list').html(html);
                }
            };
            source.onerror = function(err) {
                console.error("EventSource failed:", err);
            };
        } else {
            console.log('Error: Your browser does not support EventSource.');
            $('#participant-list').html('<tr><td colspan="3" class="text-center py-5 text-danger">เบราว์เซอร์ไม่รองรับการอัปเดตแบบ Real-time</td></tr>');
        }
    });
</script>
<?php include(APP_FOOTER); ?>