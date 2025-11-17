<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    //หน้ารายงานกิจกรรม
    $event_id = $_GET['id'] ?? '';
    if($event_id === '') {
        header('Location: ../event/?error=' . urlencode('กรุณาระบุรหัสกิจกรรม'));
        exit();
    }
    
    $from = $_GET['from'] ?? '';
    $to = $_GET['to'] ?? '';
    $status = $_GET['status'] ?? '';
    $search = $_GET['q'] ?? '';
    
    // ดึงข้อมูลกิจกรรม
    try {
        $event = Event::findByid($event_id);
        if (!$event) {
            header('Location: ../event/?error=' . urlencode('ไม่พบข้อมูลกิจกรรม'));
            exit();
        }
    } catch (Exception $e) {
        header('Location: ../event/?error=' . urlencode('เกิดข้อผิดพลาด: ' . $e->getMessage()));
        exit();
    }
    
    // Build query with filters (prepared)
    $sql = "SELECT * FROM participants WHERE events_id = :id";
    $params = [':id' => $event_id];
    
    if ($status !== '') {
        $sql .= " AND status = :status";
        $params[':status'] = $status;
    }
    if ($from !== '') {
        $sql .= " AND joined_at >= :from";
        $params[':from'] = $from . ' 00:00:00';
    }
    if ($to !== '') {
        $sql .= " AND joined_at <= :to";
        $params[':to'] = $to . ' 23:59:59';
    }
    if ($search !== '') {
        $sql .= " AND (firstname LIKE :q OR lastname LIKE :q OR email LIKE :q)";
        $params[':q'] = '%' . $search . '%';
    }
    $sql .= " ORDER BY joined_at DESC";
    
    try {
        $participants = DB::query($sql, $params);
        if (!is_array($participants)) {
            $participants = [];
        }
    } catch (Exception $e) {
        $participants = [];
        $error = 'เกิดข้อผิดพลาดในการดึงข้อมูลผู้เข้าร่วม: ' . $e->getMessage();
    }
    
    // Summary counts
    $total = count($participants);
    $checked = 0;
    $notChecked = 0;
    
    foreach ($participants as $p) {
        if (isset($p['status']) && $p['status'] == 'เข้าร่วมแล้ว') {
            $checked++;
        } else {
            $notChecked++;
        }
    }
    
    // Initialize error variable if not set
    if (!isset($error)) {
        $error = '';
    }
    
    $index = ['page' => 'report'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <title>รายงานสรุปผลกิจกรรม - <?=APP_CODE?></title>
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
        .btn-export {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.3);
            font-weight: 600;
            border-radius: 999px;
            padding: 0.75rem 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.3);
            color: #fff;
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
        .summary-card {
            border-radius: 1rem;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .summary-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
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
        .table tbody td {
            vertical-align: middle;
        }
        .chart-container {
            max-width: 400px;
            margin: 2rem auto;
            position: relative;
            height: 300px;
        }
        .event-info-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
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
    <div class="container py-5 position-relative">
        <div class="page-header mb-5">
            <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-4">
                <div>
                    <h1 class="display-6 mb-2">📊 รายงานสรุปผลกิจกรรม</h1>
                    <p class="mb-0"><?= htmlspecialchars($event['events_name'] ?? 'ไม่ระบุชื่อกิจกรรม') ?></p>
                </div>
                <div class="text-lg-end d-flex gap-2 flex-wrap">
                    <a href="../event/" class="btn btn-nav shadow-sm">
                        <i class="bi bi-arrow-left-circle me-2"></i>กลับไปรายการกิจกรรม
                    </a>
                    <?php if ($total > 0): ?>
                    <a href="export_excel.php?id=<?= urlencode($event_id) ?>" class="btn btn-export shadow-sm">
                        <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
                    </a>
                    <a href="export_pdf.php?id=<?= urlencode($event_id) ?>" class="btn btn-export shadow-sm">
                        <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if (isset($error) && $error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="event-info-card">
            <h4 class="mb-3"><?= htmlspecialchars($event['events_name'] ?? 'ไม่ระบุชื่อกิจกรรม') ?></h4>
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted mb-1"><i class="bi bi-calendar-event me-2"></i><strong>วันที่เริ่ม:</strong> <?= isset($event['start_date']) && $event['start_date'] ? date('d/m/Y', strtotime($event['start_date'])) : '-' ?></p>
                </div>
                <div class="col-md-6">
                    <p class="text-muted mb-0"><i class="bi bi-calendar-x me-2"></i><strong>วันที่สิ้นสุด:</strong> <?= isset($event['end_date']) && $event['end_date'] ? date('d/m/Y', strtotime($event['end_date'])) : '-' ?></p>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card summary-card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title mb-3"><i class="bi bi-people-fill me-2"></i>ผู้เข้าร่วมทั้งหมด</h5>
                        <h2 class="mb-0"><?= $total ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card summary-card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title mb-3"><i class="bi bi-check-circle-fill me-2"></i>เช็คอินแล้ว</h5>
                        <h2 class="mb-0"><?= $checked ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card summary-card text-white bg-danger">
                    <div class="card-body">
                        <h5 class="card-title mb-3"><i class="bi bi-x-circle-fill me-2"></i>ยังไม่เช็คอิน</h5>
                        <h2 class="mb-0"><?= $notChecked ?></h2>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if ($total > 0 && ($checked > 0 || $notChecked > 0)): ?>
        <div class="card content-card mb-4">
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="summaryChart"></canvas>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="card content-card mt-5">
            <div class="card-body">
                <h4 class="mb-4"><i class="bi bi-list-ul me-2"></i>📋 รายชื่อผู้เข้าร่วม</h4>
                <div class="table-responsive">
                    <table class="table align-middle mb-0"> 
                        <thead>
                            <tr class="text-uppercase small fw-semibold text-muted text-center">
                                <th scope="col" class="text-start ps-4">#</th>
                                <th scope="col" class="text-start">คำนำหน้า</th>
                                <th scope="col" class="text-start">ชื่อ</th>
                                <th scope="col" class="text-start">นามสกุล</th>
                                <th scope="col" class="text-start">อีเมล</th>
                                <th scope="col">สถานะ</th>
                                <th scope="col">วันที่ลงทะเบียน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($participants) > 0): ?>
                                <?php foreach($participants as $index => $p): ?>
                                <tr class="text-center">
                                    <td class="text-start ps-4 fw-semibold text-primary-emphasis"><?= $index + 1 ?></td>
                                    <td class="text-start"><?= htmlspecialchars($p['prefix'] ?? '') ?></td>
                                    <td class="text-start"><?= htmlspecialchars($p['firstname'] ?? '') ?></td>
                                    <td class="text-start"><?= htmlspecialchars($p['lastname'] ?? '') ?></td>
                                    <td class="text-start"><?= htmlspecialchars($p['email'] ?? '') ?></td>
                                    <td>
                                        <?php 
                                        $participantStatus = isset($p['status']) ? $p['status'] : 'รอเข้าร่วม';
                                        if($participantStatus == 'เข้าร่วมแล้ว'): ?>
                                            <span class="badge rounded-pill bg-success px-3 py-2">เช็คอินแล้ว</span>
                                        <?php elseif($participantStatus == 'ยกเลิก'): ?>
                                            <span class="badge rounded-pill bg-danger px-3 py-2">ยกเลิก</span>
                                        <?php else: ?>
                                            <span class="badge rounded-pill bg-secondary px-3 py-2">ยังไม่เช็คอิน</span>
                                        <?php endif ?>
                                    </td>
                                    <td><?= isset($p['joined_at']) && $p['joined_at'] ? date('d/m/Y H:i', strtotime($p['joined_at'])) : '-' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">
                                        <div class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                            <h5 class="fw-semibold mb-1">ไม่มีข้อมูลผู้เข้าร่วม</h5>
                                            <p class="mb-0">ยังไม่มีผู้เข้าร่วมในกิจกรรมนี้</p>
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
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php if ($total > 0 && ($checked > 0 || $notChecked > 0)): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('summaryChart');
            if(ctx){
                try {
                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ['เช็คอินแล้ว', 'ยังไม่เช็คอิน'],
                            datasets: [{
                                data: [<?= $checked ?>, <?= $notChecked ?>],
                                backgroundColor: [
                                    'rgba(40, 167, 69, 0.8)',
                                    'rgba(220, 53, 69, 0.8)'
                                ],
                                borderColor: [
                                    'rgba(40, 167, 69, 1)',
                                    'rgba(220, 53, 69, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 20,
                                        font: {
                                            size: 14
                                        }
                                    }
                                }
                            }
                        }
                    });
                } catch (error) {
                    console.error('Chart error:', error);
                }
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>


