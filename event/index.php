<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php


    $user_id = '';
    if (isset($_SESSION['login']) && isset($_SESSION['login']['user'])) {
        $user_id = isset($_SESSION['login']['user']['email']) ? $_SESSION['login']['user']['email'] : 
    (isset($_SESSION['login']['user']['id']) ? $_SESSION['login']['user']['id'] : '');
    }

    $success = '';
    $error = '';
    
    if (isset($_SESSION['event_create_success'])) {
        $success = $_SESSION['event_create_success'];
        unset($_SESSION['event_create_success']);
    }
    if (isset($_SESSION['event_update_success'])) {
        $success = $_SESSION['event_update_success'];
        unset($_SESSION['event_update_success']);
    }
    if (isset($_SESSION['event_delete_success'])) {
        $success = $_SESSION['event_delete_success'];
        unset($_SESSION['event_delete_success']);
    }
    if (isset($_SESSION['event_delete_error'])) {
        $error = $_SESSION['event_delete_error'];
        unset($_SESSION['event_delete_error']);
    }
    // รับ error จาก GET parameter (เช่น จาก report redirect)
    if (isset($_GET['error']) && !empty($_GET['error'])) {
        $error = urldecode($_GET['error']);
    }
    

    if (isset($_GET['delete'])) {
        header('Location: delete.php?delete=' . intval($_GET['delete']));
        exit();
    }

    if (!function_exists('formatEventDate')) {
        function formatEventDate(?string $date): string
        {
            if (empty($date) || $date === '0000-00-00') {
                return '-';
            }
            return date('d M Y', strtotime($date));
        }
    }

    if (!function_exists('statusBadgeClass')) {
        function statusBadgeClass(int $status): array
        {
            return match ($status) {
                1 => ['badge-open', 'เปิดเข้าร่วม'],
                2 => ['badge-closed', 'ปิดเข้าร่วม'],
                3 => ['badge-cancelled', 'ยกเลิก'],
                default => ['badge-draft', 'ร่าง'],
            };
        }
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
        $error = 'เกิดข้อผิดพลาดในการดึงข้อมูลกิจกรรม: ' . $e->getMessage();
        $events = [];
    }
?>
<!DOCTYPE html>
<html lang="<?=App::lang()?>">
<head>
    <meta charset="UTF-8">
    <meta name="keywords" content="<?=APP_CODE?>,EDU CMU">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <title>หน้าจัดรายการกิจกรรม</title>
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
        .btn-create {
            background: #fff;
            color: #0d6efd;
            border: none;
            font-weight: 600;
            border-radius: 999px;
            padding: 0.75rem 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(255, 255, 255, 0.3);
        }
        .content-card {
            margin-top: -4rem;
            border-radius: 1.5rem;
            border: none;
            box-shadow: 0 16px 45px rgba(15, 23, 42, 0.1);
            overflow: hidden;
        }
        .content-card .card-body {
            padding: 0;
        }
        .table thead th {
            border-bottom: none;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.75rem;
            color: #64748b;
            background-color: #f8fafc;
            padding-top: 1.2rem;
            padding-bottom: 1.2rem;
        }
        .table tbody tr {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(13, 110, 253, 0.08);
            background-color: #f8fbff;
        }
        .badge-status {
            font-size: 0.65rem;
            letter-spacing: 0.05em;
            padding: 0.35rem 0.7rem;
            border-radius: 999px;
            text-transform: uppercase;
            font-weight: 600;
        }
        .badge-draft {
            background: rgba(100, 116, 139, 0.15);
            color: #475569;
        }
        .badge-open {
            background: rgba(16, 185, 129, 0.15);
            color: #059669;
        }
        .badge-closed {
            background: rgba(245, 158, 11, 0.15);
            color: #d97706;
        }
        .badge-cancelled {
            background: rgba(239, 68, 68, 0.15);
            color: #dc2626;
        }
        .table tbody td {
            vertical-align: middle;
        }
        .table tbody td:nth-child(2) {
            min-width: 395px;
            max-width: 500px;
            word-wrap: break-word;
            white-space: normal;
        }
        .table thead th:nth-child(2) {
            min-width: 350px;
        }
        .action-buttons .btn {
            border-radius: 999px;
            font-weight: 600;
            padding: 0.4rem 0.9rem;
        }
        .action-buttons .btn i {
            margin-right: 0.35rem;
        }
        .empty-state {
            padding: 3rem 1rem;
            text-align: center;
            color: #94a3b8;
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
                    <h1 class="display-6 mb-2">หน้าจัดรายการกิจกรรมทั้งหมด</h1>
                </div>
                <div class="text-lg-end d-flex gap-2 flex-wrap">
                    <a href="../dashboard/" class="btn btn-create shadow-sm">
                        <i class="bi bi-graph-up-arrow me-2"></i>Dashboard
                    </a>
                    <a href="create.php" class="btn btn-create shadow-sm">
                        <i class="bi bi-plus-circle-fill me-2"></i>เพิ่มกิจกรรมใหม่
                    </a>
                </div>
            </div>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="card content-card mt-3">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr class="text-uppercase small fw-semibold text-muted text-center">
                                <th scope="col" class="text-start ps-4">รหัสกิจกรรม</th>
                                <th scope="col" class="text-start">ชื่อกิจกรรม</th>
                                <th scope="col">วันที่เริ่มต้น</th>
                                <th scope="col">วันที่สิ้นสุด</th>
                                <th scope="col">ประเภทผู้เข้าร่วม</th>
                                <th scope="col">สถานะ</th>
                                <th scope="col" class="text-center pe-4">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (count($events) > 0): ?>
                        <?php foreach ($events as $row): ?>
                            <?php
                                $updatedAt = $row['updated_at'] ?? ($row['created_at'] ?? null);
                                [$statusClass, $statusLabel] = statusBadgeClass((int)($row['status'] ?? 0));
                            ?>
                            <tr class="text-center">
                                <td class="text-start ps-4 fw-semibold text-primary-emphasis">#<?= $row['id'] ?></td>
                                <td class="text-start">
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($row['events_name'] ?? '-') ?></div>
                                </td>
                                <td><?= formatEventDate($row['start_date'] ?? null) ?></td>
                                <td><?= formatEventDate($row['end_date'] ?? null) ?></td>
                                <td>
                                    <span class="badge rounded-pill bg-info-subtle text-info-emphasis px-3 py-2">
                                        <?= (($row['participant_type'] ?? '') === 'ALL') ? 'ทุกคน' : 'เฉพาะรายชื่อ' ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-status <?= $statusClass ?>"><?= $statusLabel ?></span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center flex-wrap gap-2 action-buttons">
                                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-pencil-fill"></i>แก้ไข
                                        </a>
                                        <a href="share.php?id=<?= $row['id'] ?>" class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-share-fill"></i>แชร์
                                        </a>
                                        <a href="../report/event_summary_report.php?id=<?= urlencode($row['events_id']) ?>" class="btn btn-outline-info btn-sm">
                                            <i class="bi bi-file-earmark-text-fill"></i>รายงาน
                                        </a>
                                        <a href="delete.php?delete=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('ยืนยันการลบกิจกรรมนี้?')">
                                            <i class="bi bi-trash-fill"></i>ลบ
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
                                        <h5 class="fw-semibold mb-1">ยังไม่มีกิจกรรม</h5>
                                        <p class="mb-0">เริ่มต้นสร้างกิจกรรมแรกของคุณ เพื่อให้ผู้เข้าร่วมพร้อมรับข้อมูล</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>
<!-- Body -->
        </div>
        <?=App::footer($index)?>
