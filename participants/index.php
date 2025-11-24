<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php

    $user_id = '';
    if (isset($_SESSION['login']) && isset($_SESSION['login']['user'])) {
        $user_id = isset($_SESSION['login']['user']['email']) ? $_SESSION['login']['user']['email'] : 
    (isset($_SESSION['login']['user']['id']) ? $_SESSION['login']['user']['id'] : '');
    }

    $success = '';
    $error = '';
    
    if (isset($_SESSION['participant_create_success'])) {
        $success = $_SESSION['participant_create_success'];
        unset($_SESSION['participant_create_success']);
    }
    if (isset($_SESSION['participant_update_success'])) {
        $success = $_SESSION['participant_update_success'];
        unset($_SESSION['participant_update_success']);
    }
    if (isset($_SESSION['participant_delete_success'])) {
        $success = $_SESSION['participant_delete_success'];
        unset($_SESSION['participant_delete_success']);
    }
    if (isset($_SESSION['participant_delete_error'])) {
        $error = $_SESSION['participant_delete_error'];
        unset($_SESSION['participant_delete_error']);
    }
    

    if (isset($_GET['delete'])) {
        header('Location: delete.php?delete=' . intval($_GET['delete']));
        exit();
    }

    if (!function_exists('formatParticipantDate')) {
        function formatParticipantDate(?string $date): string
        {
            if (empty($date) || $date === '0000-00-00 00:00:00') {
                return '-';
            }
            return Helper::datetimeDisplay($date, 'th');
        }
    }

    if (!function_exists('statusBadgeClass')) {
        function statusBadgeClass(string $status): array
        {
            return match ($status) {
                'เข้าร่วมแล้ว' => ['badge-joined', 'เข้าร่วมแล้ว'],
                'รอเข้าร่วม' => ['badge-waiting', 'รอเข้าร่วม'],
                'ยกเลิก' => ['badge-cancelled', 'ยกเลิก'],
                default => ['badge-waiting', 'รอเข้าร่วม'],
            };
        }
    }


    try {
        $participants = Participant::listAll();
        
        if (!is_array($participants)) {
            $participants = [];
        }
    } catch (Exception $e) {
        $error = 'เกิดข้อผิดพลาดในการดึงข้อมูลผู้เข้าร่วม: ' . $e->getMessage();
        $participants = [];
    }
?>
<!DOCTYPE html>
<html lang="<?=App::lang()?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <title>หน้าจัดรายชื่อผู้เข้าร่วมกิจกรรม</title>
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
        .badge-waiting {
            background: rgba(100, 116, 139, 0.15);
            color: #475569;
        }
        .badge-joined {
            background: rgba(16, 185, 129, 0.15);
            color: #059669;
        }
        .badge-cancelled {
            background: rgba(239, 68, 68, 0.15);
            color: #dc2626;
        }
        .table tbody td {
            vertical-align: middle;
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
                    <h1 class="display-6 mb-2">รายชื่อผู้เข้าร่วมกิจกรรมทั้งหมด</h1>
                </div>
                <div class="text-lg-end">
                    <a href="create.php" class="btn btn-create shadow-sm">
                        <i class="bi bi-plus-circle-fill me-2"></i>เพิ่มรายชื่อใหม่
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
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
                                <th scope="col" class="text-start ps-4">รหัสรายการ</th>
                                <th scope="col" class="text-start">ชื่อ-นามสกุล</th>
                                <th scope="col" class="text-start">กิจกรรม</th>
                                <th scope="col">ประเภท</th>
                                <th scope="col">อีเมล</th>
                                <th scope="col">สังกัด</th>
                                <th scope="col">วันที่เข้าร่วม</th>
                                <th scope="col">สถานะ</th>
                                <th scope="col" class="text-center pe-4">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (count($participants) > 0): ?>
                        <?php foreach ($participants as $row): ?>
                            <?php
                                $joinedAt = $row['joined_at'] ?? null;
                                [$statusClass, $statusLabel] = statusBadgeClass($row['status'] ?? 'รอเข้าร่วม');
                                $fullName = trim(($row['prefix'] ?? '') . ' ' . ($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? ''));
                            ?>
                            <tr class="text-center">
                                <td class="text-start ps-4 fw-semibold text-primary-emphasis">#<?= $row['id'] ?></td>
                                <td class="text-start">
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($fullName) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($row['participant_id'] ?? '-') ?></small>
                                </td>
                                <td class="text-start">
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($row['events_name'] ?? '-') ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($row['events_id'] ?? '-') ?></small>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-info-subtle text-info-emphasis px-3 py-2">
                                        <?= htmlspecialchars($row['type'] ?? '-') ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($row['email'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['organization'] ?? '-') ?></td>
                                <td><?= formatParticipantDate($joinedAt) ?></td>
                                <td>
                                    <span class="badge-status <?= $statusClass ?>"><?= $statusLabel ?></span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center flex-wrap gap-2 action-buttons">
                                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-pencil-fill"></i>แก้ไข
                                        </a>
                                        <a href="delete.php?delete=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('ยืนยันการลบรายชื่อนี้?')">
                                            <i class="bi bi-trash-fill"></i>ลบ
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <i class="bi bi-people fs-1 d-block mb-3"></i>
                                        <h5 class="fw-semibold mb-1">ยังไม่มีรายชื่อผู้เข้าร่วม</h5>
                                        <p class="mb-0">เริ่มต้นเพิ่มรายชื่อผู้เข้าร่วมกิจกรรมแรกของคุณ</p>
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
    <!-- Body -->
    </div>
        <?=App::footer($index)?>
</body>
</html>
