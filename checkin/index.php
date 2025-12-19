<?php
// รับค่าจาก QR
$event_id     = $_GET['event_id'] ?? '';
$prefix       = $_GET['prefix'] ?? '';
$firstname    = $_GET['firstname'] ?? '';
$lastname     = $_GET['lastname'] ?? '';
$organization = $_GET['organization'] ?? '';
$email        = $_GET['email'] ?? '';
$student_id   = $_GET['student_id'] ?? '';

// ป้องกันการเปิดหน้าเปล่า
if (!$event_id || !$student_id) {
    die("ข้อมูลไม่ครบ ไม่สามารถเช็คอินได้");
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สแกน QR เช็คอิน</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="../app/assets/js/jquery-2.1.1.js"></script>
    <script src="../app/assets/js/qrcode/jquery.qrcode-0.12.0.min.js"></script>
    <script src="../app/assets/js/qrcode/html5-qrcode.min.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: "Prompt", "Segoe UI", sans-serif;
            padding: 2rem 0;
        }
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        .page-header {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1.5rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }
        .page-header h1 {
            color: #667eea;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .scanner-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1.5rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }
        .scanner-section h2 {
            color: #343f52;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        #reader {
            background: #f8f9fa;
            border-radius: 1rem;
            padding: 1rem;
            margin: 0 auto;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .stats-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }
        .stats-section h2 {
            color: #343f52;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .count-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            color: white;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 2rem;
        }
        .count-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }
        .count-card h1 {
            font-size: 4rem;
            font-weight: 700;
            margin: 0;
            color: white;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        .count-card p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }
        .list-section {
            margin-top: 2rem;
        }
        .list-section h3 {
            color: #343f52;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .checkin-item {
            background: white;
            border: none;
            border-radius: 0.75rem;
            padding: 1.25rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 1rem;
            animation: slideInRight 0.5s ease-out;
        }
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        .checkin-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }
        .checkin-item .icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        .checkin-item .info {
            flex: 1;
        }
        .checkin-item .name {
            font-weight: 600;
            color: #343f52;
            margin-bottom: 0.25rem;
        }
        .checkin-item .details {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .alert {
            border-radius: 0.75rem;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        .btn-nav {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            font-weight: 600;
            border-radius: 0.75rem;
            padding: 0.75rem 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .btn-nav:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        @media (max-width: 768px) {
            body {
                padding: 1rem 0;
            }
            .page-header, .scanner-section, .stats-section {
                padding: 1.5rem;
                border-radius: 1rem;
            }
            .page-header h1 {
                font-size: 1.5rem;
            }
            .count-card {
                padding: 1.5rem;
            }
            .count-card h1 {
                font-size: 3rem;
            }
            .checkin-item {
                padding: 1rem;
            }
            .checkin-item .icon {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="page-header mt-10 mb-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <h1 class="mb-0"><i class="bi bi-qr-code-scan"></i> ระบบเช็คอินด้วย QR Code</h1>
                <div class="text-md-end">
                    <a href="../event/" class="btn btn-nav shadow-sm">
                        <i class="bi bi-arrow-left-circle me-2"></i>กลับไปรายการกิจกรรม
                    </a>
                </div>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="scanner-section">
                    <h2><i class="bi bi-camera-video"></i> สแกน QR Code</h2>
                    <div class="mt-3 text-center">
                        <div id="qrcode"></div>
                    </div>
                    <div id="result" class="mt-3"></div>
                    
                </div>
            </div>
            
            <div class="col-lg-5">
                <div class="stats-section">
                    <h2><i class="bi bi-graph-up-arrow"></i> สถิติการเช็คอิน</h2>
                    <div class="count-card">
                        <h1 id="cont">0</h1>
                        <p><i class="bi bi-arrow-repeat"></i> อัปเดตอัตโนมัติทุก 1 วินาที</p>
                    </div>

                    <div class="list-section">
                        <h3><i class="bi bi-clock-history"></i> รายชื่อผู้เช็คอินล่าสุด</h3>
                        <div id="list"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const resultEl = document.getElementById('result');
        const countEl = document.getElementById('cont');
        const listEl = document.getElementById('list');
        
        // ฟังก์ชันแสดงข้อความสถานะ
        function showMessage(message, type) {
            const alertClass = type === 'success' ? 'alert-success' : 
                                type === 'danger' ? 'alert-danger' : 
                                type === 'secondary' ? 'alert-secondary' : 'alert-info';
            resultEl.innerHTML = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
            setTimeout(() => {
                resultEl.innerHTML = '';
            }, 5000);
        }

        function onScanSuccess(decodedText, decodedResult) {
            showMessage('กำลังส่งข้อมูลเช็คอิน: ' + decodedText, 'secondary');

            // ดึง event_id จาก URL parameter (ถ้ามี)
            const urlParams = new URLSearchParams(window.location.search);
            const eventId = urlParams.get('event_id');

            // สร้าง request body
            const requestBody = { participant_id: decodedText };
            if (eventId) {
                requestBody.event_id = eventId;
            }

            fetch('../api/checkin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestBody)
            })
            .then(r => r.json())
            .then(data => {
                if (data.ok) {
                    showMessage(`เช็คอินสำเร็จ: ${data.name || data.participant_id}`, 'success');
                    // รีเฟรชข้อมูลทันที
                    fetchCheckinData();
                } else {
                    showMessage(`ข้อผิดพลาด: ${data.error}`, 'danger');
                }
            })
            .catch(err => {
                showMessage('ไม่สามารถเชื่อมต่อ server: ' + err, 'danger');
            });
        }
        
        function onScanFailure(error) {
            // ไม่ต้องแสดง error ทุกครั้งที่สแกนไม่เจอ
            // console.warn(`Code scan error = ${error}`);
        }
        
        // เริ่มต้น QR Scanner
        const html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", 
            { 
                fps: 10, 
                qrbox: 250,
                aspectRatio: 1.0
            }
        );
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);

        // ฟังก์ชันดึงข้อมูลเช็คอิน
        function fetchCheckinData() {
            fetch('../api/dashboard_checkin.php')
                .then(response => response.json())
                .then(data => {
                    if (data.ok) {
                        // อัปเดตจำนวนด้วย animation
                        const currentCount = parseInt(countEl.textContent) || 0;
                        const newCount = data.count;
                        if (currentCount !== newCount) {
                            animateCount(currentCount, newCount);
                        }
                        
                        listEl.innerHTML = '';
                        if (data.latest && data.latest.length > 0) {
                            data.latest.forEach((item, index) => {
                                const div = document.createElement('div');
                                div.className = 'checkin-item';
                                div.style.animationDelay = `${index * 0.1}s`;
                                div.innerHTML = `
                                    <div class="icon">
                                        <i class="bi bi-person-check"></i>
                                    </div>
                                    <div class="info">
                                        <div class="name">${item.name || item.participant_id}</div>
                                        <div class="details">
                                            <i class="bi bi-tag"></i> ${item.participant_id} 
                                            <span class="ms-2"><i class="bi bi-clock"></i> ${item.checkin_time || ''}</span>
                                        </div>
                                    </div>
                                `;
                                listEl.appendChild(div);
                            });
                        } else {
                            const div = document.createElement('div');
                            div.className = 'empty-state';
                            div.innerHTML = `
                                <i class="bi bi-inbox"></i>
                                <p>ยังไม่มีผู้เช็คอิน</p>
                            `;
                            listEl.appendChild(div);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching check-in data:', error);
                });
        }

        // ฟังก์ชัน animate ตัวเลข
        function animateCount(from, to) {
            const duration = 500;
            const startTime = performance.now();
            const difference = to - from;
            
            function update(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const easeOutQuart = 1 - Math.pow(1 - progress, 4);
                const current = Math.round(from + (difference * easeOutQuart));
                countEl.textContent = current;
                
                if (progress < 1) {
                    requestAnimationFrame(update);
                } else {
                    countEl.textContent = to;
                }
            }
            
            requestAnimationFrame(update);
        }

        // ดึงข้อมูลทุก 1 วินาที
        setInterval(fetchCheckinData, 1000);
        // ดึงข้อมูลครั้งแรกเมื่อโหลดหน้า
        fetchCheckinData();
    </script>
    <script>
        $(document).ready(function(){
            $("#qrcode").empty().qrcode({"render": 'image',
                                        "fill": '#0e2e96',
                                        "ecLevel": 'H',
                                        "text": 'https:://checkin.edu.cmu.ac.th/?events_id=EVT-20251212000001-ABCDEFG',
                                        "size": 160,
                                        "radius": 0,
                                        "quiet": 1,
                                        "mode": 2,
                                        "mSize": 0.12,
                                        "mPosX": 0.5,
                                        "mPosY": 0.5,
                                        "label": 'me',
                                        "fontname": 'Prompt Regular',
                                        "fontcolor": '#0e2e96',
                                        "background": '#FFFFFF'
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>