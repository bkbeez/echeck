<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    
    $error = '';
    $success = '';
    $participant = null;
    
    $participantId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($participantId <= 0) {
        $error = 'ไม่พบข้อมูลผู้เข้าร่วม';
    } else {
        $participant = Participant::getParticipant($participantId);
        
        if (!$participant) {
            $error = 'ไม่พบข้อมูลผู้เข้าร่วม';
        }
    }
    
    $user_id = '';
    if (isset($_SESSION['login']) && isset($_SESSION['login']['user'])) {
        $user_id = isset($_SESSION['login']['user']['email']) ? $_SESSION['login']['user']['email'] : 
                    (isset($_SESSION['login']['user']['id']) ? $_SESSION['login']['user']['id'] : '');
    }
    
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
        if (empty($error)) {
            $error = 'เกิดข้อผิดพลาดในการดึงข้อมูลกิจกรรม: ' . $e->getMessage();
        }
        $events = [];
    }
    
    // จัดการ POST request สำหรับอัพเดทข้อมูล
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $participant) {
        // Validate ข้อมูล
        $participant_id = isset($_POST['participant_id']) ? trim($_POST['participant_id']) : '';
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
            // เตรียมข้อมูลสำหรับอัพเดท
            $participantData = [
                'participant_id' => Helper::stringSave($participant_id),
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
            
            // ใช้ Participant model เพื่ออัพเดทข้อมูล
            try {
                $result = Participant::updateParticipant($participantId, $participantData);
                
                if ($result) {
                    $_SESSION['participant_update_success'] = 'อัพเดทข้อมูลผู้เข้าร่วมสำเร็จ';
                    header('Location: index.php');
                    exit();
                } else {
                    $error = 'ไม่สามารถอัพเดทข้อมูลได้ อาจจะไม่มีข้อมูลนี้อยู่แล้ว หรือเกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง';
                }
            } catch (Exception $e) {
                $error = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
            }
        }
        

        if ($error) {
            $participant['events_id'] = $events_id;
            $participant['type'] = $type;
            $participant['prefix'] = $prefix;
            $participant['firstname'] = $firstname;
            $participant['lastname'] = $lastname;
            $participant['email'] = $email;
            $participant['organization'] = $organization;
            $participant['status'] = $status;
            $participant['note'] = $note;
        }
    }
    
    // แสดงข้อความ success จาก session (ถ้ามี)
    if (isset($_SESSION['participant_update_success'])) {
        $success = $_SESSION['participant_update_success'];
        unset($_SESSION['participant_update_success']);
    }
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลผู้เข้าร่วมกิจกรรม</title>
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
            <h1 class="display-6 mb-2">✏️ แก้ไขข้อมูลผู้เข้าร่วมกิจกรรม</h1>
            <p class="mb-0 opacity-75">แก้ไขข้อมูลผู้เข้าร่วมกิจกรรม</p>
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
                
                <?php if ($participant): ?>
                <form method="POST" class="mt-3">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($participant['id']) ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">รหัสรายการ (ID)</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($participant['id'] ?? '-') ?>" disabled>
                        <small class="text-muted">ID ไม่สามารถแก้ไขได้</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">รหัสผู้เข้าร่วม (Participant ID)</label>
                        <input type="text" name="participant_id" class="form-control" value="<?= htmlspecialchars($participant['participant_id'] ?? '') ?>" placeholder="เช่น PART-20240101120000-abc12345">
                        <small class="text-muted">สามารถแก้ไขได้</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">เลือกกิจกรรม <span class="text-danger">*</span></label>
                        <select name="events_id" class="form-select" required>
                            <option value="">-- กรุณาเลือกกิจกรรม --</option>
                            <?php foreach ($events as $event): ?>
                                <option value="<?= htmlspecialchars($event['events_id']) ?>" <?= ($participant['events_id'] === $event['events_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($event['events_name']) ?> (<?= htmlspecialchars($event['events_id']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ประเภทผู้เข้าร่วม</label>
                        <input type="text" name="type" class="form-control" placeholder="เช่น บุคลากร, นักศึกษา, บุคคลภายนอก" value="<?= htmlspecialchars($participant['type'] ?? '') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">คำนำหน้า</label>
                        <select name="prefix" class="form-select">
                            <option value="นาย" <?= ($participant['prefix'] === 'นาย') ? 'selected' : '' ?>>นาย</option>
                            <option value="นางสาว" <?= ($participant['prefix'] === 'นางสาว') ? 'selected' : '' ?>>นางสาว</option>
                            <option value="นาง" <?= ($participant['prefix'] === 'นาง') ? 'selected' : '' ?>>นาง</option>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ชื่อ <span class="text-danger">*</span></label>
                            <input type="text" name="firstname" class="form-control" value="<?= htmlspecialchars($participant['firstname'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">นามสกุล <span class="text-danger">*</span></label>
                            <input type="text" name="lastname" class="form-control" value="<?= htmlspecialchars($participant['lastname'] ?? '') ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">อีเมล <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($participant['email'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">สังกัด</label>
                        <input type="text" name="organization" class="form-control" value="<?= htmlspecialchars($participant['organization'] ?? '') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">สถานะ</label>
                        <select name="status" class="form-select">
                            <option value="รอเข้าร่วม" <?= ($participant['status'] === 'รอเข้าร่วม') ? 'selected' : '' ?>>รอเข้าร่วม</option>
                            <option value="เข้าร่วมแล้ว" <?= ($participant['status'] === 'เข้าร่วมแล้ว') ? 'selected' : '' ?>>เข้าร่วมแล้ว</option>
                            <option value="ยกเลิก" <?= ($participant['status'] === 'ยกเลิก') ? 'selected' : '' ?>>ยกเลิก</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">หมายเหตุ</label>
                        <textarea name="note" class="form-control" rows="3"><?= htmlspecialchars($participant['note'] ?? '') ?></textarea>
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
                        <i class="bi bi-exclamation-triangle me-2"></i>ไม่พบข้อมูลผู้เข้าร่วม
                    </div>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>กลับไปยังรายการผู้เข้าร่วม
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
