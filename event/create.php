<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    $error = '';
    $success = '';
    
    // จัดการ GET request
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Validate ข้อมูล
        $name = isset($_GET['name']) ? trim($_GET['name']) : '';
        $start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
        $end_date = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';
        $participant_type = isset($_GET['participant_type']) ? trim($_GET['participant_type']) : 'ALL';
        $status = isset($_GET['status']) ? intval($_GET['status']) : 0;
        
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
            // ดึง user_id จาก session (ถ้ามีการล็อกอิน) หรือใช้ค่าดีฟอลต์
            $user_id = '';
            if (isset($_SESSION['login']) && isset($_SESSION['login']['user'])) {
                $user_id = isset($_SESSION['login']['user']['email']) ? $_SESSION['login']['user']['email'] : 
                            (isset($_SESSION['login']['user']['id']) ? $_SESSION['login']['user']['id'] : '');
            }
            
            // ถ้าไม่มี user_id ให้ใช้ค่าดีฟอลต์ (สำหรับระบบที่ยังไม่มีการล็อกอิน)
            if (empty($user_id)) {
                $user_id = 'system@localhost';
            }
            
            $events_id = 'EVT-' . date('YmdHis') . '-' . substr(md5(uniqid(rand(), true)), 0, 8);
            
            // ตรวจสอบว่า events_id ซ้ำหรือไม่ (ป้องกัน duplicate)
            $existing = Event::findByid($events_id);
            if ($existing) {
                // ถ้าซ้ำให้สร้างใหม่
                $events_id = 'EVT-' . date('YmdHis') . '-' . substr(md5(uniqid(rand(), true)), 0, 8);
            }
            
            // เตรียมข้อมูลสำหรับบันทึก
            $eventData = [
                'user_id' => $user_id,
                'events_id' => $events_id,
                'events_name' => Helper::stringSave($name),
                'start_date' => $start_date,
                'end_date' => $end_date,
                'participant_type' => $participant_type,
                'status' => $status
            ];
            
            // ใช้ Event model เพื่อบันทึกข้อมูล
            try {
                $eventId = Event::createEvent($eventData);
                
                if ($eventId) {
                    // บันทึกสำเร็จ
                    $_SESSION['event_create_success'] = 'สร้างกิจกรรมสำเร็จ';
                    header('Location: index.php');
                    exit();
                } else {
                    $error = 'ไม่สามารถสร้างกิจกรรมได้ กรุณาลองใหม่อีกครั้ง';
                }
            } catch (Exception $e) {
                $error = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
            }
        }
    }
    
    // แสดงข้อความ success จาก session (ถ้ามี)
    if (isset($_SESSION['event_create_success'])) {
        $success = $_SESSION['event_create_success'];
        unset($_SESSION['event_create_success']);
    }
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มกิจกรรม</title>
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
            <h1 class="display-6 mb-2">➕ เพิ่มกิจกรรมใหม่</h1>
            <p class="mb-0 opacity-75">กรอกข้อมูลกิจกรรมที่ต้องการสร้าง</p>
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
                
                <form method="GET" class="mt-3">
                    <div class="mb-3">
                        <label class="form-label">ชื่อกิจกรรม <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= isset($_GET['name']) ? htmlspecialchars($_GET['name']) : '' ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วันที่เริ่มต้น <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" value="<?= isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : '' ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วันที่สิ้นสุด <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" value="<?= isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : '' ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ประเภทผู้เข้าร่วม</label>
                        <select name="participant_type" class="form-select">
                            <option value="ALL" <?= (isset($_GET['participant_type']) && $_GET['participant_type'] === 'ALL') ? 'selected' : '' ?>>ทุกคน</option>
                            <option value="LIST" <?= (isset($_GET['participant_type']) && $_GET['participant_type'] === 'LIST') ? 'selected' : '' ?>>เฉพาะผู้ที่มีรายชื่อ</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">สถานะ</label>
                        <select name="status" class="form-select">
                            <option value="0" <?= (isset($_GET['status']) && $_GET['status'] == 0) ? 'selected' : '' ?>>ร่าง</option>
                            <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == 1) ? 'selected' : '' ?>>เปิดการเข้าร่วม</option>
                            <option value="2" <?= (isset($_GET['status']) && $_GET['status'] == 2) ? 'selected' : '' ?>>ปิดการเข้าร่วม</option>
                            <option value="3" <?= (isset($_GET['status']) && $_GET['status'] == 3) ? 'selected' : '' ?>>ยกเลิก</option>
                        </select>
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <a href="index.php"><button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-check-circle me-2"></i>บันทึก
                        </button></a>
                        <a href="index.php" class="btn btn-secondary px-4">
                            <i class="bi bi-arrow-left me-2"></i>กลับ
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>