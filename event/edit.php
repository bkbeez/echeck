<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    // ตรวจสอบการล็อกอิน (optional - ถ้าต้องการให้มีการล็อกอินก่อนแก้ไขกิจกรรม)
    // Auth::check('/login');
    
    $error = '';
    $success = '';
    $event = null;
    
    // ดึง event ID จาก query parameter
    $eventId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($eventId <= 0) {
        $error = 'ไม่พบข้อมูลกิจกรรม';
    } else {
        $user_id = '';
        if (isset($_SESSION['login']) && isset($_SESSION['login']['user'])) {
            $user_id = isset($_SESSION['login']['user']['email']) ? $_SESSION['login']['user']['email'] : 
                        (isset($_SESSION['login']['user']['id']) ? $_SESSION['login']['user']['id'] : '');
        }
        
        // ดึงข้อมูลกิจกรรมด้วย Event model
        $event = Event::getOwnedEvent($eventId, $user_id);
        
        if (!$event) {
            $error = 'ไม่พบกิจกรรมหรือคุณไม่มีสิทธิ์แก้ไขกิจกรรมนี้';
        }
    }
    
    // จัดการ POST request สำหรับอัพเดทข้อมูล
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $event) {
        // Validate ข้อมูล
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $start_date = isset($_POST['start_date']) ? trim($_POST['start_date']) : '';
        $end_date = isset($_POST['end_date']) ? trim($_POST['end_date']) : '';
        $participant_type = isset($_POST['participant_type']) ? trim($_POST['participant_type']) : 'ALL';
        $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
        
        // ตรวจสอบข้อมูลที่จำเป็น
        if (empty($name)) {
            $error = 'กรุณากรอกชื่อกิจกรรม';
        } elseif (empty($start_date)) {
            $error = 'กรุณาเลือกวันที่เริ่มต้น';
        } elseif (empty($end_date)) {
            $error = 'กรุณาเลือกวันที่สิ้นสุด';
        } elseif ($start_date > $end_date) {
            $error = 'วันที่เริ่มต้นต้องไม่เกินวันที่สิ้นสุด';
        } elseif (!in_array($participant_type, ['ALL', 'LIST'])) {
            $error = 'ประเภทผู้เข้าร่วมไม่ถูกต้อง';
        } elseif (!in_array($status, [0, 1, 2, 3])) {
            $error = 'สถานะไม่ถูกต้อง';
        } else {
            // เตรียมข้อมูลสำหรับอัพเดท
            $eventData = [
                'events_id' => $event['events_id'], // ใช้ events_id เดิม
                'events_name' => Helper::stringSave($name),
                'start_date' => $start_date,
                'end_date' => $end_date,
                'participant_type' => $participant_type,
                'status' => $status
            ];
            
            // ใช้ Event model เพื่ออัพเดทข้อมูล
            try {
                $result = Event::updateEvent($eventId, $user_id, $eventData);
                
                if ($result) {
                    $_SESSION['event_update_success'] = 'อัพเดทกิจกรรมสำเร็จ';
                    header('Location: index.php');
                    exit();
                } else {
                    $error = 'ไม่สามารถอัพเดทกิจกรรมได้ กรุณาลองใหม่อีกครั้ง';
                }
            } catch (Exception $e) {
                $error = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
            }
        }
        

        if ($error) {
            $event['events_name'] = $name;
            $event['start_date'] = $start_date;
            $event['end_date'] = $end_date;
            $event['participant_type'] = $participant_type;
            $event['status'] = $status;
        }
    }
    
    // แสดงข้อความ success จาก session (ถ้ามี)
    if (isset($_SESSION['event_update_success'])) {
        $success = $_SESSION['event_update_success'];
        unset($_SESSION['event_update_success']);
    }
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขกิจกรรม</title>
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
        }
        .content-card {
            margin-top: -4rem;
            border-radius: 1.5rem;
            border: none;
            box-shadow: 0 16px 45px rgba(15, 23, 42, 0.1);
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
        }
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        .alert {
            border-radius: 0.5rem;
            border: none;
        }
    </style>
</head>
<body>

    <div class="container py-5">
        <div class="page-header mb-5">
            <h1 class="display-6 mb-2">✏️ แก้ไขกิจกรรม</h1>
            <p class="mb-0 opacity-75">แก้ไขข้อมูลกิจกรรม</p>
        </div>
        
        <div class="card content-card">
            <div class="card-body p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($event): ?>
                <form method="POST" class="mt-3">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($event['id']) ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">รหัสกิจกรรม</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($event['events_id']) ?>" disabled>
                        <small class="text-muted">รหัสกิจกรรมไม่สามารถแก้ไขได้</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ชื่อกิจกรรม <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($event['events_name']) ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วันที่เริ่มต้น <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($event['start_date']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วันที่สิ้นสุด <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($event['end_date']) ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ประเภทผู้เข้าร่วม</label>
                        <select name="participant_type" class="form-select">
                            <option value="ALL" <?= ($event['participant_type'] === 'ALL') ? 'selected' : '' ?>>ทุกคน</option>
                            <option value="LIST" <?= ($event['participant_type'] === 'LIST') ? 'selected' : '' ?>>เฉพาะผู้ที่มีรายชื่อ</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">สถานะ</label>
                        <select name="status" class="form-select">
                            <option value="0" <?= ($event['status'] == 0) ? 'selected' : '' ?>>ร่าง</option>
                            <option value="1" <?= ($event['status'] == 1) ? 'selected' : '' ?>>เปิดการเข้าร่วม</option>
                            <option value="2" <?= ($event['status'] == 2) ? 'selected' : '' ?>>ปิดการเข้าร่วม</option>
                            <option value="3" <?= ($event['status'] == 3) ? 'selected' : '' ?>>ยกเลิก</option>
                        </select>
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-check-circle me-2"></i>บันทึกการแก้ไข
                        </button>
                        <a href="index.php" class="btn btn-secondary px-4">
                            <i class="bi bi-arrow-left me-2"></i>กลับ
                        </a>
                    </div>
                </form>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>ไม่พบข้อมูลกิจกรรม
                    </div>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>กลับไปยังรายการกิจกรรม
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
