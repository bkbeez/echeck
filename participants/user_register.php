<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    $index = [];
    $error = '';
    $success = '';
    
    // ดึงรายการกิจกรรมที่เปิดให้ลงทะเบียนได้ (status = 1)
    try {
        $events = DB::query(
            "SELECT * FROM `events` 
                WHERE `status` = 1 
                ORDER BY `start_date` DESC, `id` DESC"
        );
        if (!is_array($events)) {
            $events = [];
        }
    } catch (Exception $e) {
        $error = 'เกิดข้อผิดพลาดในการดึงข้อมูลกิจกรรม: ' . $e->getMessage();
        $events = [];
    }
    
    // จัดการ GET request
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['submit'])) {
        // Validate ข้อมูล
        $events_id = isset($_GET['events_id']) ? trim($_GET['events_id']) : '';
        $prefix = isset($_GET['prefix']) ? trim($_GET['prefix']) : '';
        $firstname = isset($_GET['firstname']) ? trim($_GET['firstname']) : '';
        $lastname = isset($_GET['lastname']) ? trim($_GET['lastname']) : '';
        $email = isset($_GET['email']) ? trim($_GET['email']) : '';
        $organization = isset($_GET['organization']) ? trim($_GET['organization']) : '';
        
        // ตรวจสอบข้อมูลที่จำเป็น
        if (empty($events_id)) {
            $error = 'กรุณาเลือกกิจกรรม';
        } else {
            // ตรวจสอบว่า events_id มีอยู่ใน events table หรือไม่
            try {
                $event = Event::findByid($events_id);
                if (!$event) {
                    $error = 'ไม่พบกิจกรรมที่เลือก กรุณาเลือกกิจกรรมใหม่';
                } elseif ($event['status'] != 1) {
                    $error = 'กิจกรรมนี้ยังไม่เปิดให้ลงทะเบียน';
                }
            } catch (Exception $e) {
                $error = 'เกิดข้อผิดพลาดในการตรวจสอบกิจกรรม: ' . $e->getMessage();
            }
        }
        
        if (empty($error)) {
            if (empty($firstname)) {
                $error = 'กรุณากรอกชื่อ';
            } elseif (empty($lastname)) {
                $error = 'กรุณากรอกนามสกุล';
            } elseif (empty($email)) {
                $error = 'กรุณากรอกอีเมล';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'รูปแบบอีเมลไม่ถูกต้อง';
            }
        }
        
        // ตรวจสอบว่าอีเมลนี้ลงทะเบียนในกิจกรรมนี้แล้วหรือยัง
        if (empty($error)) {
            try {
                $existingParticipant = DB::one(
                    "SELECT * FROM `participants` 
                        WHERE `events_id` = :events_id AND `email` = :email 
                        LIMIT 1",
                    [
                        'events_id' => $events_id,
                        'email' => $email
                    ]
                );
                
                if ($existingParticipant) {
                    $error = 'อีเมลนี้ได้ลงทะเบียนในกิจกรรมนี้แล้ว';
                }
            } catch (Exception $e) {
                $error = 'เกิดข้อผิดพลาดในการตรวจสอบข้อมูล: ' . $e->getMessage();
            }
        }
        
        if (empty($error)) {
            // เตรียมข้อมูลสำหรับบันทึก
            // type = "User" และ status = "รอเข้าร่วม" สำหรับผู้ใช้ทั่วไป
            $participantData = [
                'participant_id' => '', // จะถูกสร้างอัตโนมัติใน createParticipant
                'events_id' => Helper::stringSave($events_id),
                'type' => 'User',
                'prefix' => Helper::stringSave($prefix),
                'firstname' => Helper::stringSave($firstname),
                'lastname' => Helper::stringSave($lastname),
                'email' => Helper::stringSave($email),
                'organization' => Helper::stringSave($organization),
                'status' => 'รอเข้าร่วม',
                'note' => ''
            ];
            
            // ใช้ Participant model เพื่อบันทึกข้อมูล
            try {
                $participantId = Participant::createParticipant($participantData);
                
                if ($participantId) {
                    $success = 'ลงทะเบียนเข้าร่วมกิจกรรมสำเร็จ! ขอบคุณที่สนใจเข้าร่วมกิจกรรม';
                    // รีเซ็ตฟอร์ม
                    $_GET = [];
                } else {
                    $error = 'ไม่สามารถลงทะเบียนได้ กรุณาลองใหม่อีกครั้ง';
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
    <title>ลงทะเบียนเข้าร่วมกิจกรรม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
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
        .page-header p {
            margin-bottom: 0;
            opacity: 0.85;
        }
        .content-card {
            margin-top: -4rem;
            border-radius: 1.5rem;
            border: none;
            box-shadow: 0 16px 45px rgba(15, 23, 42, 0.1);
            overflow: hidden;
        }
        .form-label {
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.5rem;
        }
        .form-control, .form-select {
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        .alert {
            border-radius: 0.5rem;
            border: none;
        }
        .btn-submit {
            background: linear-gradient(135deg, #0d6efd, #6f42c1);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.3);
            color: #fff;
        }
        .event-info {
            background: #f8f9fa;
            border-left: 4px solid #0d6efd;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .event-info small {
            color: #64748b;
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
                <h1 class="display-6 mb-2">📝 ลงทะเบียนเข้าร่วมกิจกรรม</h1>
                <p class="mb-0 opacity-75">กรอกข้อมูลเพื่อลงทะเบียนเข้าร่วมกิจกรรมที่คุณสนใจ</p>
            </div>
            
            <div class="card content-card mt-3">
                <div class="card-body p-5">
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
                    
                    <?php if (count($events) === 0): ?>
                        <div class="alert alert-warning mb-4" role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i>ขณะนี้ยังไม่มีกิจกรรมที่เปิดให้ลงทะเบียน
                        </div>
                    <?php else: ?>
                        <form method="GET" class="mt-4">
                            <div class="mb-4">
                                <label class="form-label">เลือกกิจกรรมที่ต้องการเข้าร่วม <span class="text-danger">*</span></label>
                                <select name="events_id" class="form-select" required>
                                    <option value="">-- กรุณาเลือกกิจกรรม --</option>
                                    <?php foreach ($events as $event): ?>
                                        <option value="<?= htmlspecialchars($event['events_id']) ?>" <?= (isset($_GET['events_id']) && $_GET['events_id'] === $event['events_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($event['events_name']) ?> 
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($_GET['events_id']) && $_GET['events_id']): ?>
                                    <?php
                                        $selectedEvent = null;
                                        foreach ($events as $event) {
                                            if ($event['events_id'] === $_GET['events_id']) {
                                                $selectedEvent = $event;
                                                break;
                                            }
                                        }
                                    ?>
                                    <?php if ($selectedEvent): ?>
                                        <div class="event-info mt-2">
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars($selectedEvent['events_name']) ?></div>
                                            <?php if ($selectedEvent['start_date'] || $selectedEvent['end_date']): ?>
                                                <small>
                                                    <?php if ($selectedEvent['start_date']): ?>
                                                        วันที่เริ่ม: <?= Helper::dateDisplay($selectedEvent['start_date']) ?>
                                                    <?php endif; ?>
                                                    <?php if ($selectedEvent['end_date'] && $selectedEvent['end_date'] !== $selectedEvent['start_date']): ?>
                                                        - วันที่สิ้นสุด: <?= Helper::dateDisplay($selectedEvent['end_date']) ?>
                                                    <?php endif; ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">คำนำหน้า</label>
                                    <select name="prefix" class="form-select">
                                        <option value="นาย" <?= (isset($_GET['prefix']) && $_GET['prefix'] === 'นาย') ? 'selected' : '' ?>>นาย</option>
                                        <option value="นางสาว" <?= (isset($_GET['prefix']) && $_GET['prefix'] === 'นางสาว') ? 'selected' : '' ?>>นางสาว</option>
                                        <option value="นาง" <?= (isset($_GET['prefix']) && $_GET['prefix'] === 'นาง') ? 'selected' : '' ?>>นาง</option>
                                    </select>
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label class="form-label">ชื่อ <span class="text-danger">*</span></label>
                                    <input type="text" name="firstname" class="form-control" value="<?= isset($_GET['firstname']) ? htmlspecialchars($_GET['firstname']) : '' ?>" required>
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label class="form-label">นามสกุล <span class="text-danger">*</span></label>
                                    <input type="text" name="lastname" class="form-control" value="<?= isset($_GET['lastname']) ? htmlspecialchars($_GET['lastname']) : '' ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">อีเมล <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="<?= isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '' ?>" required placeholder="example@email.com">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">สังกัด / องค์กร</label>
                                <input type="text" name="organization" class="form-control" value="<?= isset($_GET['organization']) ? htmlspecialchars($_GET['organization']) : '' ?>" placeholder="เช่น คณะศึกษาสตร์ มหาวิทยาลัยเชียงใหม่">
                            </div>
                            
                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" name="submit" class="btn btn-submit">
                                    <i class="bi bi-check-circle me-2"></i>ลงทะเบียน
                                </button>
                                <button type="reset" class="btn btn-outline-secondary px-4">
                                    <i class="bi bi-arrow-clockwise me-2"></i>รีเซ็ต
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Body -->
    </div>
    <?=App::footer($index)?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>