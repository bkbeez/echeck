<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    $error = '';
    $success = '';
    
    $user_id = '';
    if (isset($_SESSION['login']) && isset($_SESSION['login']['user'])) {
        $user_id = isset($_SESSION['login']['user']['email']) ? $_SESSION['login']['user']['email'] : 
                    (isset($_SESSION['login']['user']['id']) ? $_SESSION['login']['user']['id'] : '');
    }
    
    // ดึงรายการกิจกรรมทั้งหมด
    try {
        if (isset($_SESSION['login']) && isset($_SESSION['login']['user'])) {
            $events = Event::listForUser($user_id);
        } else {
            $events = DB::query(
                "SELECT * FROM `events` ORDER BY `start_date` DESC, `id` DESC"
            );
        }
        if (!is_array($events)) {
            $events = [];
        }
    } catch (Exception $e) {
        $error = 'เกิดข้อผิดพลาดในการดึงข้อมูลกิจกรรม: ' . $e->getMessage();
        $events = [];
    }
    
    // จัดการ POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate ข้อมูล
        $events_id = isset($_POST['events_id']) ? trim($_POST['events_id']) : '';
        $type = isset($_POST['type']) ? trim($_POST['type']) : '';
        $prefix = isset($_POST['prefix']) ? trim($_POST['prefix']) : '';
        $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
        $lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $organization = isset($_POST['organization']) ? trim($_POST['organization']) : '';
        $status = isset($_POST['status']) ? trim($_POST['status']) : 'รอเข้าร่วม';
        $note = isset($_POST['note']) ? trim($_POST['note']) : '';
        
        // ตรวจสอบข้อมูลที่จำเป็น
        if (empty($events_id)) {
            $error = 'กรุณาเลือกกิจกรรม';
        } elseif (empty($firstname)) {
            $error = 'กรุณากรอกชื่อ';
        } elseif (empty($lastname)) {
            $error = 'กรุณากรอกนามสกุล';
        } elseif (empty($email)) {
            $error = 'กรุณากรอกอีเมล';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'รูปแบบอีเมลไม่ถูกต้อง';
        } elseif (!in_array($status, ['รอเข้าร่วม', 'เข้าร่วมแล้ว', 'ยกเลิก'])) {
            $error = 'สถานะไม่ถูกต้อง';
        } else {
            // สร้าง participant_id
            $participant_id = 'PART-' . date('YmdHis') . '-' . substr(md5(uniqid(rand(), true)), 0, 8);
            
            // ตรวจสอบว่า participant_id ซ้ำหรือไม่
            $existing = Participant::findByParticipantId($participant_id);
            if ($existing) {
                $participant_id = 'PART-' . date('YmdHis') . '-' . substr(md5(uniqid(rand(), true)), 0, 8);
            }
            
            // เตรียมข้อมูลสำหรับบันทึก
            $participantData = [
                'participant_id' => $participant_id,
                'events_id' => Helper::stringSave($events_id),
                'type' => Helper::stringSave($type),
                'prefix' => Helper::stringSave($prefix),
                'firstname' => Helper::stringSave($firstname),
                'lastname' => Helper::stringSave($lastname),
                'email' => Helper::stringSave($email),
                'organization' => Helper::stringSave($organization),
                'status' => $status,
                'note' => Helper::stringSave($note)
            ];
            
            // ใช้ Participant model เพื่อบันทึกข้อมูล
            try {
                $participantId = Participant::createParticipant($participantData);
                
                if ($participantId) {
                    $_SESSION['participant_create_success'] = 'เพิ่มรายชื่อผู้เข้าร่วมสำเร็จ';
                    header('Location: index.php');
                    exit();
                } else {
                    $error = 'ไม่สามารถเพิ่มรายชื่อได้ กรุณาลองใหม่อีกครั้ง';
                }
            } catch (Exception $e) {
                $error = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
            }
        }
    }
    
    // แสดงข้อความ success จาก session (ถ้ามี)
    if (isset($_SESSION['participant_create_success'])) {
        $success = $_SESSION['participant_create_success'];
        unset($_SESSION['participant_create_success']);
    }
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มรายชื่อผู้เข้าร่วมกิจกรรม</title>
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
            <h1 class="display-6 mb-2">➕ เพิ่มรายชื่อผู้เข้าร่วมกิจกรรมใหม่</h1>
            <p class="mb-0 opacity-75">กรอกข้อมูลผู้เข้าร่วมกิจกรรมที่ต้องการเพิ่ม</p>
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
                
                <form method="POST" class="mt-3">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">เลือกกิจกรรม <span class="text-danger">*</span></label>
                            <select name="events_id" class="form-select" required>
                                <option value="">-- กรุณาเลือกกิจกรรม --</option>
                                <?php foreach ($events as $event): ?>
                                    <option value="<?= htmlspecialchars($event['events_id']) ?>" <?= (isset($_POST['events_id']) && $_POST['events_id'] === $event['events_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($event['events_name']) ?> (<?= htmlspecialchars($event['events_id']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ประเภทผู้เข้าร่วม</label>
                            <input type="text" name="type" class="form-control" placeholder="เช่น บุคลากร, นักศึกษา, บุคคลภายนอก" value="<?= isset($_POST['type']) ? htmlspecialchars($_POST['type']) : '' ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <label class="form-label">คำนำหน้า</label>
                            <select name="prefix" class="form-select">
                                <option value="นาย" <?= (isset($_POST['prefix']) && $_POST['prefix'] === 'นาย') ? 'selected' : '' ?>>นาย</option>
                                <option value="นางสาว" <?= (isset($_POST['prefix']) && $_POST['prefix'] === 'นางสาว') ? 'selected' : '' ?>>นางสาว</option>
                                <option value="นาง" <?= (isset($_POST['prefix']) && $_POST['prefix'] === 'นาง') ? 'selected' : '' ?>>นาง</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ชื่อ <span class="text-danger">*</span></label>
                            <input type="text" name="firstname" class="form-control" value="<?= isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : '' ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">นามสกุล <span class="text-danger">*</span></label>
                            <input type="text" name="lastname" class="form-control" value="<?= isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : '' ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">อีเมล <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">สังกัด</label>
                        <input type="text" name="organization" class="form-control" value="<?= isset($_POST['organization']) ? htmlspecialchars($_POST['organization']) : '' ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">สถานะ</label>
                        <select name="status" class="form-select">
                            <option value="รอเข้าร่วม" <?= (isset($_POST['status']) && $_POST['status'] === 'รอเข้าร่วม') ? 'selected' : '' ?>>รอเข้าร่วม</option>
                            <option value="เข้าร่วมแล้ว" <?= (isset($_POST['status']) && $_POST['status'] === 'เข้าร่วมแล้ว') ? 'selected' : '' ?>>เข้าร่วมแล้ว</option>
                            <option value="ยกเลิก" <?= (isset($_POST['status']) && $_POST['status'] === 'ยกเลิก') ? 'selected' : '' ?>>ยกเลิก</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">หมายเหตุ</label>
                        <textarea name="note" class="form-control" rows="3"><?= isset($_POST['note']) ? htmlspecialchars($_POST['note']) : '' ?></textarea>
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-check-circle me-2"></i>บันทึก
                        </button>
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
