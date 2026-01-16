<?php if(!isset($index['page'])||$index['page']!='scan'){ header("location:".((isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']==443)?'https://':'http://').$_SERVER["HTTP_HOST"]); exit(); } ?>
<?php include(APP_HEADER); ?>

<style type="text/css">
    body {
        background-color: #f4f7f6;
        background-image: url('<?=THEME_IMG?>/map.png');
        background-position: center;
        background-repeat: no-repeat;
    }
    .on-hamburger, .navbar-collapse-wrapper { display: none !important; }
    .card-custom {
        border: none !important;
        border-radius: 15px !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05) !important;
        background: #ffffff;
        margin-bottom: 1.5rem;
    }
    .qr-header {
        background: #615eba;
        color: #fff;
        padding: 20px;
        border-radius: 15px 15px 0 0;
        text-align: center;
    }
    .qr-body {
        padding: 30px;
        text-align: center;
    }
    .qr-container {
        background: #fff;
        padding: 10px;
        display: inline-block;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .list-header {
        padding: 20px 25px;
        border-bottom: 1px solid #f0f0f0;
    }
    .status-live {
        color: #2ecc71;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
    }
    .dot-live {
        height: 8px; width: 8px;
        background-color: #2ecc71;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
        animation: pulse 2s infinite;
    }
    .search-wrapper {
        display: flex;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: 1px solid #e0e0e0;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .search-wrapper:focus-within {
        border-color: #00bcd4;
        box-shadow: 0 4px 15px rgba(0, 188, 212, 0.15);
    }
    .search-group {
        padding: 0 25px 20px;
    }
    .input-custom {
        border: none !important;
        background: transparent !important;
        padding: 12px 20px !important;
        font-size: 0.95rem;
    }
    .btn-custom-search {
        background: #00bcd4 !important;
        color: white !important;
        border: none !important;
        width: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .btn-custom-search:hover {
        background: #00acc1 !important;
        width: 80px; /* ขยายปุ่มเล็กน้อย */
        transform: scale(1.05);
    }
    .btn-custom-search i {
        font-size: 1.2rem;
        transition: transform 0.4s ease;
    }
    .btn-custom-search:hover i {
        transform: rotate(15deg) scale(1.1); /* หมุนไอคอนเล็กน้อย */
    }
    .btn-custom-search::after {
        content: "";
        position: absolute;
        top: -50%;
        left: -60%;
        width: 20%;
        height: 200%;
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(30deg);
        transition: all 0.5s;
    }
    .btn-custom-search:hover::after {
        left: 120%;
    }
    .table-custom thead th {
        background-color: #9fa8da !important;
        color: #ffffff !important;
        font-weight: 500;
        padding: 15px;
        border: none;
    }
    .table-custom tbody td {
        padding: 15px 20px;
        vertical-align: middle;
        border-bottom: 1px solid #f8f8f8;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.4; }
        100% { opacity: 1; }
    }
</style>

<div class="container-fluid py-4">
    <div class="row g-4">
        
        <div class="col-lg-4 col-md-12">
            <div class="card card-custom">
                <div class="qr-header">
                    <h5 class="fw-bold mb-1">สแกนเพื่อลงทะเบียน</h5>
                    <p class="small mb-0 opacity-75">SCAN TO REGISTER</p>
                </div>
                <div class="qr-body">
                    <div class="qr-container">
                        <div id="qrcode"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-12">
            <div class="card card-custom h-100">
                <div class="list-header">
                    <h5 class="fw-bold mb-1 text-dark">รายชื่อผู้ลงทะเบียนล่าสุด</h5>
                    <div class="status-live">
                        <span class="dot-live"></span> อัปเดตข้อมูลแบบ Real-time
                    </div>
                </div>
                <div class="search-group mt-3">
                    <div class="col-md-7">
                        <div class="search-wrapper">
                            <input id="keyword" type="text" class="form-control input-custom" placeholder="ค้นหารายชื่อ...">
                            <button class="btn-custom-search" title="คลิกเพื่อค้นหา">
                                <i class="uil uil-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4" width="30%">เวลา</th>
                                <th width="70%">ผู้ลงทะเบียน</th>
                            </tr>
                        </thead>
                        <tbody id="participant-list">
                            <tr><td colspan="2" class="text-center py-5 text-muted">กำลังโหลดข้อมูล...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#qrcode").empty().qrcode({ "render": 'image',
                        "fill": '#000000',
                        "ecLevel": 'H',
                        "text": '<?=APP_HOST.'/?events_id='.$_GET['events_id']?>',
                        "size": 256,
                        "radius": 0,
                        "quiet": 1,
                        "mode": 2,
                        "mSize": 0,
                        "mPosX": 0,
                        "mPosY": 0,
                        "label": '',
                        "fontname": 'RSU Regular',
                        "fontcolor": '#000000',
                        "background": '#FFFFFF'
        });
        if(typeof(EventSource) !== "undefined") {
            var source = new EventSource("<?=APP_PATH?>/scripts/check/counting.php");
            source.onmessage = function(event) {
                var result = JSON.parse(event.data);
                var listHtml = '';
                $.each(result.list, function(i, item) {
                    listHtml += `<tr>
                        <td class="ps-4"><span class="text-muted small">${item.time}</span></td>
                        <td>
                            <div class="fw-bold" style="color:#444;">${item.name}</div>
                            <div class="small text-muted">${item.org || '-'}</div>
                        </td>
                    </tr>`;
                });
                $('#participant-list').html(listHtml || '<tr><td colspan="2" class="text-center py-4">ยังไม่มีข้อมูล</td></tr>');
            };
        }
    });
</script>

<?php include(APP_FOOTER); ?>