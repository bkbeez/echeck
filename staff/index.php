<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    $error = '';
    $success = '';
    $participant = null;
    $message = '';
    
    $index = ['page' => 'staff'];
    
    // จัดการ GET request สำหรับเช็คอิน
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['participant_id'])) {
        $participant_id = trim($_GET['participant_id']);
        
        if (empty($participant_id)) {
            $error = 'กรุณาระบุรหัสผู้เข้าร่วม';
        } else {
            try {
                // ค้นหาข้อมูลผู้เข้าร่วม
                $participant = Participant::findByParticipantId($participant_id);
                
                if ($participant) {
                    if ($participant['status'] === 'เข้าร่วมแล้ว') {
                        $error = 'ผู้เข้าร่วมนี้ได้เช็คอินแล้วเมื่อ ' . ($participant['joined_at'] ? date('d/m/Y H:i', strtotime($participant['joined_at'])) : '');
                    } else {
                        // อัพเดทสถานะเป็น 'เข้าร่วมแล้ว' และอัพเดท joined_at
                        $updateData = [
                            'participant_id' => $participant['participant_id'],
                            'events_id' => $participant['events_id'],
                            'type' => $participant['type'] ?? '',
                            'prefix' => $participant['prefix'] ?? '',
                            'firstname' => $participant['firstname'] ?? '',
                            'lastname' => $participant['lastname'] ?? '',
                            'email' => $participant['email'] ?? '',
                            'organization' => $participant['organization'] ?? '',
                            'status' => 'เข้าร่วมแล้ว',
                            'note' => $participant['note'] ?? ''
                        ];
                        
                        // อัพเดท status และ joined_at โดยตรง
                        $sql = "UPDATE `participants` 
                                SET `status` = :status, 
                                    `joined_at` = NOW(),
                                    `participant_id` = :participant_id,
                                    `events_id` = :events_id,
                                    `type` = :type,
                                    `prefix` = :prefix,
                                    `firstname` = :firstname,
                                    `lastname` = :lastname,
                                    `email` = :email,
                                    `organization` = :organization,
                                    `note` = :note
                                WHERE `id` = :id
                                LIMIT 1;";
                        
                        $updateData['id'] = (int)$participant['id'];
                        $result = DB::update($sql, $updateData);
                        
                        if ($result) {
                            $success = 'เช็คอินสำเร็จ: ' . ($participant['prefix'] ?? '') . ' ' . ($participant['firstname'] ?? '') . ' ' . ($participant['lastname'] ?? '');
                            // ดึงข้อมูลใหม่
                            $participant = Participant::findByParticipantId($participant_id);
                        } else {
                            $error = 'ไม่สามารถเช็คอินได้ กรุณาลองใหม่อีกครั้ง';
                        }
                    }
                } else {
                    $error = 'ไม่พบข้อมูลผู้เข้าร่วมที่ระบุ';
                }
            } catch (Exception $e) {
                $error = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="<?=App::lang()?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบเช็คอินผู้เข้าร่วม(Staff) - <?=APP_CODE?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        body {
            background: radial-gradient(circle at 10% 20%, #e0f2ff 0%, #f3f4ff 40%, #ffffff 100%);
            min-height: 100vh;
            font-family: "Prompt", "Segoe UI", sans-serif;
        }
        .page-header {
            border-radius: 1.5rem;
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.95), rgba(111, 66, 193, 0.92));
            color: #fff;
            padding: 2.5rem;
            box-shadow: 0 20px 45px rgba(13, 110, 253, 0.2);
            position: relative;
            overflow: hidden;
        }
        .page-header::after {
            content: "";
            position: absolute;
            top: -30%;
            right: -10%;
            width: 45%;
            height: 160%;
            background: rgba(255, 255, 255, 0.15);
            transform: rotate(15deg);
            pointer-events: none;
        }
        .page-header h1 {
            font-weight: 600;
            letter-spacing: 0.02em;
        }
        .content-card {
            margin-top: -4rem;
            border-radius: 1.5rem;
            border: none;
            box-shadow: 0 16px 45px rgba(15, 23, 42, 0.1);
            overflow: hidden;
        }
        .content-card .card-body {
            padding: 2rem;
        }
        .qr-scanner-container {
            background: #f8fafc;
            border-radius: 1rem;
            padding: 2rem;
            margin: 2rem 0;
            text-align: center;
        }
        #reader {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }
        .participant-info {
            background: #f8fafc;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }
        .participant-info h5 {
            color: #475569;
            margin-bottom: 1rem;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #64748b;
        }
        .info-value {
            color: #1e293b;
        }
        .btn-nav {
            background: #fff;
            color: #0d6efd;
            border: none;
            font-weight: 600;
            border-radius: 999px;
            padding: 0.75rem 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-nav:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(255, 255, 255, 0.3);
            color: #0d6efd;
        }
        @media (max-width: 992px) {
            .page-header {
                padding: 2rem;
            }
            .page-header h1 {
                font-size: 1.9rem;
            }
            .content-card {
                margin-top: -3rem;
            }
        }
    </style>
</head>
<body>
    <div class="page-loader"></div>
    <div class="content-wrapper on-font-primary">
        <!-- Body -->
        <?=App::menus($index)?>

        <div class="container py-5 position-relative">
            <div class="page-header mb-5">
                <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-4">
                    <div>
                        <h1 class="display-6 mb-2">📱 ระบบเช็คอินผู้เข้าร่วม(STAFF    )</h1>
                        <p class="mb-0">สำหรับเจ้าหน้าที่ - สแกน QR Code เพื่อเช็คอิน</p>
                    </div>
                    <div class="text-lg-end d-flex gap-2 flex-wrap">
                        <a href="../participants/" class="btn btn-nav shadow-sm">
                            <i class="bi bi-people-fill me-2"></i>รายชื่อผู้เข้าร่วม
                        </a>
                        <a href="../event/" class="btn btn-nav shadow-sm">
                            <i class="bi bi-calendar-event me-2"></i>รายการกิจกรรม
                        </a>
                    </div>
                </div>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="card content-card mt-3">
                <div class="card-body">
                    <div class="qr-scanner-container">
                        <h4 class="mb-3"><i class="bi bi-qr-code-scan me-2"></i>สแกน QR Code</h4>
                        <div id="reader"></div>
                        <p class="text-muted mt-3 mb-0">นำ QR Code มาวางไว้หน้ากล้องเพื่อสแกน</p>
                    </div>
                    
                    <?php if ($participant): ?>
                        <div class="participant-info">
                            <h5><i class="bi bi-person-circle me-2"></i>ข้อมูลผู้เข้าร่วม</h5>
                            <div class="info-item">
                                <span class="info-label">รหัสผู้เข้าร่วม:</span>
                                <span class="info-value"><?= htmlspecialchars($participant['participant_id']) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">ชื่อ-นามสกุล:</span>
                                <span class="info-value"><?= htmlspecialchars(($participant['prefix'] ?? '') . ' ' . ($participant['firstname'] ?? '') . ' ' . ($participant['lastname'] ?? '')) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">อีเมล:</span>
                                <span class="info-value"><?= htmlspecialchars($participant['email'] ?? '-') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">กิจกรรม:</span>
                                <span class="info-value"><?= htmlspecialchars($participant['events_name'] ?? '-') ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">สถานะ:</span>
                                <span class="info-value">
                                    <?php if ($participant['status'] === 'เข้าร่วมแล้ว'): ?>
                                        <span class="badge bg-success">เข้าร่วมแล้ว</span>
                                    <?php elseif ($participant['status'] === 'ยกเลิก'): ?>
                                        <span class="badge bg-danger">ยกเลิก</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">รอเข้าร่วม</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <?php if ($participant['joined_at']): ?>
                                <div class="info-item">
                                    <span class="info-label">วันที่เข้าร่วม:</span>
                                    <span class="info-value"><?= date('d/m/Y H:i', strtotime($participant['joined_at'])) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function onScanSuccess(decodedText, decodedResult) {
            // ส่งข้อมูลไปยัง server
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'participant_id';
            input.value = decodedText;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }

        function onScanFailure(error) {
            // ข้อผิดพลาดในการสแกน (ไม่ต้องแสดงอะไร)
        }

        // เริ่มต้น QR Code Scanner
        let html5QrcodeScanner;
        
        document.addEventListener('DOMContentLoaded', function() {
            try {
                html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader",
                    { 
                        fps: 10, 
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.0
                    },
                    false // verbose
                );
                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            } catch (error) {
                console.error('Error initializing QR scanner:', error);
                document.getElementById('reader').innerHTML = 
                    '<div class="alert alert-warning">ไม่สามารถเปิดกล้องได้ กรุณาตรวจสอบสิทธิ์การเข้าถึงกล้อง</div>';
            }
        });
    </script>
    <!-- Body -->
    </div>
    <?=App::footer($index)?>
</body>
</html>
