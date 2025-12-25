<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    $error = '';
    $success = '';
    $event = null;
    $shares = [];
    
    $eventId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($eventId <= 0) {
        $error = 'ไม่พบข้อมูลกิจกรรม';
    } else {
        $user_id = '';
        if (isset($_SESSION['login']) && isset($_SESSION['login']['user'])) {
            $user_id = isset($_SESSION['login']['user']['email']) ? $_SESSION['login']['user']['email'] : 
                        (isset($_SESSION['login']['user']['id']) ? $_SESSION['login']['user']['id'] : '');
        }

        $event = Event::getOwnedEvent($eventId, $user_id);
        
        if (!$event) {
            $error = 'ไม่พบกิจกรรมหรือคุณไม่มีสิทธิ์แชร์กิจกรรมนี้';
        } else {
            $shares = Event::listShares($eventId, $user_id);
            if (!is_array($shares)) {
                $shares = [];
            }
        }
    }
    
    // จัดการ POST request สำหรับเพิ่มแชร์
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $event) {
        $action = isset($_POST['action']) ? trim($_POST['action']) : '';
        
        if ($action === 'add') {
            // เพิ่มแชร์
            $shared_id = isset($_POST['shared_id']) ? trim($_POST['shared_id']) : '';
            
            if (empty($shared_id)) {
                $error = 'กรุณากรอกอีเมลหรือ ID ของผู้ใช้ที่ต้องการแชร์';
            } else {
                // ใช้ Event model เพื่อเพิ่มแชร์
                try {
                    $result = Event::addShare($eventId, $user_id, $shared_id);
                    
                    if ($result) {
                        $success = 'เพิ่มการแชร์สำเร็จ';
                        // ดึงรายการแชร์ใหม่
                        $shares = Event::listShares($eventId, $user_id);
                        if (!is_array($shares)) {
                            $shares = [];
                        }
                    } else {
                        $error = 'ไม่สามารถเพิ่มการแชร์ได้ อาจเป็นเพราะแชร์กับผู้ใช้นี้แล้วหรือผู้ใช้เป็นเจ้าของกิจกรรม';
                    }
                } catch (Exception $e) {
                    $error = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
                }
            }
        } elseif ($action === 'remove') {
            // ลบแชร์
            $shareId = isset($_POST['share_id']) ? intval($_POST['share_id']) : 0;
            
            if ($shareId <= 0) {
                $error = 'ไม่พบข้อมูลการแชร์';
            } else {
                // ใช้ Event model เพื่อลบแชร์
                try {
                    $result = Event::removeShare($shareId, $eventId, $user_id);
                    
                    if ($result) {
                        $success = 'ลบการแชร์สำเร็จ';
                        // ดึงรายการแชร์ใหม่
                        $shares = Event::listShares($eventId, $user_id);
                        if (!is_array($shares)) {
                            $shares = [];
                        }
                    } else {
                        $error = 'ไม่สามารถลบการแชร์ได้ กรุณาลองใหม่อีกครั้ง';
                    }
                } catch (Exception $e) {
                    $error = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="<?=App::lang()?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แชร์กิจกรรม</title>
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
        .share-item {
            padding: 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            background: #f8fafc;
        }
    </style>
</head>
<body>
    <div class="page-loader"></div>
        <div class="content-wrapper on-font-primary">
        <!-- Body -->
            <?=App::menus($index)?>
    <div class="container py-5">
        <div class="page-header mb-5">
            <h1 class="display-6 mb-2">🔗 แชร์กิจกรรม</h1>
            <p class="mb-0 opacity-75">จัดการการแชร์กิจกรรมกับผู้ใช้อื่น</p>
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
                    <div class="mb-4">
                        <h5 class="mb-2">ข้อมูลกิจกรรม</h5>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-1"><strong>ชื่อกิจกรรม:</strong> <?= htmlspecialchars($event['events_name']) ?></p>
                                <p class="mb-1"><strong>รหัสกิจกรรม:</strong> <?= htmlspecialchars($event['events_id']) ?></p>
                                <p class="mb-0"><strong>วันที่เริ่มต้น:</strong> <?= !empty($event['start_date']) && $event['start_date'] !== '0000-00-00' ? Helper::dateDisplay($event['start_date'], 'th') : '-' ?> <strong>วันที่สิ้นสุด:</strong> <?= !empty($event['end_date']) && $event['end_date'] !== '0000-00-00' ? Helper::dateDisplay($event['end_date'], 'th') : '-' ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form เพิ่มแชร์ -->
                    <div class="mb-4">
                        <h5 class="mb-3">เพิ่มการแชร์</h5>
                        <form method="POST" class="row g-3">
                            <input type="hidden" name="action" value="add">
                            <div class="col-md-8">
                                <label class="form-label">อีเมลหรือ ID ของผู้ใช้ <span class="text-danger">*</span></label>
                                <input type="text" name="shared_id" class="form-control" placeholder="กรุณากรอกอีเมลหรือ ID" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-plus-circle me-2"></i>เพิ่มการแชร์
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- รายการแชร์ -->
                    <div class="mb-4">
                        <h5 class="mb-3">รายการแชร์ (<?= count($shares) ?>)</h5>
                        <?php if (count($shares) > 0): ?>
                            <?php foreach ($shares as $share): ?>
                                <div class="share-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-person-circle me-2"></i>
                                            <strong><?= htmlspecialchars($share['shared_id']) ?></strong>
                                            <?php if (isset($share['date_shared'])): ?>
                                                <small class="text-muted ms-2">(แชร์เมื่อ: <?= date('d/m/Y H:i', strtotime($share['date_shared'])) ?>)</small>
                                            <?php endif; ?>
                                        </div>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('ยืนยันการลบการแชร์นี้?')">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="share_id" value="<?= htmlspecialchars($share['id']) ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash me-1"></i>ลบ
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>ยังไม่มีผู้ใช้ที่ถูกแชร์กิจกรรมนี้
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <a href="index.php" class="btn btn-secondary px-4">
                            <i class="bi bi-arrow-left me-2"></i>กลับ
                        </a>
                    </div>
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
<?=App::footer($index)?>
