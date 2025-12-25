<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    $index['page'] = 'event';
    $index['view'] = 'staff_register';
    if( !Auth::check() ){
        $_SESSION['login_redirect'] = APP_HOME.'/'.$index['page']; 
        header('Location: '.APP_HOME.'/login');
        exit;
    }
    $error = '';
    $success = '';
    
    // ตรวจสอบสิทธิ์ว่าเป็น staff หรือ admin
    $isStaff = false;
    $isAdmin = false;
    
    if (isset($_SESSION['login'])) {
        if (isset($_SESSION['login']['admin']) && $_SESSION['login']['admin']) {
            $isAdmin = true;
            $isStaff = true;
        } elseif (isset($_SESSION['login']['staff']) && $_SESSION['login']['staff']) {
            $isStaff = true;
        }
    }
    
    // ถ้าไม่ใช่ staff หรือ admin ให้ redirect
    if (!$isStaff) {
        header('Location: /login/');
        exit();
    }
    
    $user_id = '';
    if (isset($_SESSION['login']) && isset($_SESSION['login']['user'])) {
        $user_id = isset($_SESSION['login']['user']['email']) ? $_SESSION['login']['user']['email'] : 
                (isset($_SESSION['login']['user']['id']) ? $_SESSION['login']['user']['id'] : '');
    }
    
    // ดึงรายการกิจกรรมทั้งหมด (Staff สามารถดูได้ทั้งหมด)
    try {
        if (isset($_SESSION['login']) && isset($_SESSION['login']['user'])) {
            $events = Event::listForUser($user_id);
        } else {
            $events = Event::listOpenEvents();
        }
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
        $status = isset($_GET['status']) ? trim($_GET['status']) : 'รอเข้าร่วม';
        $note = isset($_GET['note']) ? trim($_GET['note']) : '';
        
        // ตรวจสอบข้อมูลที่จำเป็น
        if (empty($events_id)) {
            $error = 'กรุณาเลือกกิจกรรม';
        } else {
            // ตรวจสอบว่า events_id มีอยู่ใน events table หรือไม่
            try {
                $event = Event::findByid($events_id);
                if (!$event) {
                    $error = 'ไม่พบกิจกรรมที่เลือก กรุณาเลือกกิจกรรมใหม่';
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
            } elseif (!in_array($status, ['รอเข้าร่วม', 'เข้าร่วมแล้ว', 'ยกเลิก'])) {
                $error = 'สถานะไม่ถูกต้อง';
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
            // type = "Staff" สำหรับเจ้าหน้าที่
            $participantData = [
                'participant_id' => '', // จะถูกสร้างอัตโนมัติใน createParticipant
                'events_id' => Helper::stringSave($events_id),
                'type' => 'Staff',
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
                    $success = 'ลงทะเบียนเข้าร่วมกิจกรรมสำเร็จ!';
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
<?php include(APP_HEADER);?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<style type="text/css">
    body {
        min-height: 100vh;
        font-family: "Prompt", "Segoe UI", sans-serif;
        background:url('<?=THEME_IMG?>/map.png') top center;
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
    .staff-badge {
        display: inline-block;
        background: rgba(255, 193, 7, 0.2);
        color: #856404;
        padding: 0.25rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 0.5rem;
    }
</style>
<div class="container py-5 position-relative">
    <div class="page-header mb-5">
        <h1 class="display-6 mb-2 text-white">👔 ลงทะเบียนเข้าร่วมกิจกรรม (Staff)</h1>
        <p class="mb-0 opacity-75">กรอกข้อมูลเพื่อลงทะเบียนเข้าร่วมกิจกรรมสำหรับเจ้าหน้าที่</p>
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
                    <i class="bi bi-info-circle-fill me-2"></i>ขณะนี้ยังไม่มีกิจกรรม
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
                        <input type="text" name="organization" class="form-control" value="<?= isset($_GET['organization']) ? htmlspecialchars($_GET['organization']) : '' ?>" placeholder="เช่น คณะศึกษาศาสตร์ มหาวิทยาลัยเชียงใหม่">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">สถานะ</label>
                        <select name="status" class="form-select">
                            <option value="รอเข้าร่วม" <?= (!isset($_GET['status']) || (isset($_GET['status']) && $_GET['status'] === 'รอเข้าร่วม')) ? 'selected' : '' ?>>รอเข้าร่วม</option>
                            <option value="เข้าร่วมแล้ว" <?= (isset($_GET['status']) && $_GET['status'] === 'เข้าร่วมแล้ว') ? 'selected' : '' ?>>เข้าร่วมแล้ว</option>
                            <option value="ยกเลิก" <?= (isset($_GET['status']) && $_GET['status'] === 'ยกเลิก') ? 'selected' : '' ?>>ยกเลิก</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">หมายเหตุ</label>
                        <textarea name="note" class="form-control" rows="3" placeholder="หมายเหตุเพิ่มเติม (ถ้ามี)"><?= isset($_GET['note']) ? htmlspecialchars($_GET['note']) : '' ?></textarea>
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" name="submit" class="btn btn-submit">
                            <i class="bi bi-check-circle me-2"></i>ลงทะเบียน
                        </button>
                        <button type="reset" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-arrow-clockwise me-2"></i>รีเซ็ต
                        </button>
                        <a href="index.php" class="btn btn-outline-primary px-4">
                            <i class="bi bi-list-ul me-2"></i>ดูรายชื่อทั้งหมด
                        </a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include(APP_FOOTER);?>