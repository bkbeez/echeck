<?php 
// generate_qr.php
// ตัวอย่าง: แสดง QR ของ participant_id ที่ส่งเป็นพารามิเตอร์ ?pid=...
    $pid = isset($_GET['pid']) ? $_GET['pid'] : null;
    if (!$pid) {
        echo "กรุณาส่ง ?pid=participant_id ตัวอย่าง: generate_qr.php?pid=U12345";
        exit;
    }
    // ใช้ Google Chart API สร้าง QR code image (ง่ายและไม่ต้องไลบรารีเพิ่มเติม)
    $qrText = urlencode($pid);
    $size = 300;
    $src = "https://chart.googleapis.com/chart?chs={$size}x{$size}&cht=qr&chl={$qrText}&choe=UTF-8";
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code สำหรับ <?php echo htmlspecialchars($pid); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: "Prompt", "Segoe UI", sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .qr-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1.5rem;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }
        .qr-container h1 {
            color: #667eea;
            font-weight: 700;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        .qr-image-wrapper {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            margin: 2rem 0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            display: inline-block;
        }
        .qr-image-wrapper img {
            display: block;
            border-radius: 0.5rem;
        }
        .participant-id {
            background: #f8f9fa;
            border-radius: 0.75rem;
            padding: 1rem 1.5rem;
            margin: 1.5rem 0;
            display: inline-block;
        }
        .participant-id code {
            font-size: 1.1rem;
            color: #667eea;
            font-weight: 600;
            background: transparent;
            padding: 0;
        }
        .info-text {
            color: #6c757d;
            margin-top: 1.5rem;
            line-height: 1.8;
        }
        .btn-print {
            margin-top: 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
    </style>
</head>
<body>
    <div class="qr-container">
        <h1><i class="bi bi-qr-code"></i> QR Code สำหรับเช็คอิน</h1>
        <div class="qr-image-wrapper">
            <img src="<?php echo $src ?>" alt="QR Code" />
        </div>
        <div class="participant-id">
            <i class="bi bi-tag-fill text-primary"></i> 
            <code><?php echo htmlspecialchars($pid); ?></code>
        </div>
        <p class="info-text">
            <i class="bi bi-info-circle"></i> 
            สแกน QR Code นี้ด้วยหน้าสแกนหรือพิมพ์ participant_id นี้ไปยังระบบ
        </p>
        <button class="btn btn-print" onclick="window.print()">
            <i class="bi bi-printer"></i> พิมพ์ QR Code
        </button>
    </div>
</body>
</html>