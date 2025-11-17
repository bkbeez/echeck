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
    
    $index = ['page' => 'dashboard'];
?>
<!DOCTYPE html>
<html lang="<?=App::lang()?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <title>Dashboard - <?=APP_CODE?></title>
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
        .chart-container {
            position: relative;
            height: 400px;
            margin: 2rem 0;
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
                    <h1 class="display-6 mb-2">📊 Dashboard รวมกิจกรรม</h1>
                    <p class="mb-0">กราฟแสดงจำนวนผู้เข้าร่วมแต่ละกิจกรรม</p>
                </div>
                <div class="text-lg-end d-flex gap-2 flex-wrap">
                    <a href="../event/" class="btn btn-nav shadow-sm">
                        <i class="bi bi-calendar-event me-2"></i>รายการกิจกรรม
                    </a>
                </div>
            </div>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="card content-card">
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="activitiesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // จะเรียก /dashboard/chart_data.php เพื่อดึง JSON ของจำนวนผู้เข้าร่วมแต่ละกิจกรรม
        fetch('chart_data.php')
            .then(r => {
                if (!r.ok) {
                    throw new Error('Network response was not ok');
                }
                return r.json();
            })
            .then(data => {
                const ctx = document.getElementById('activitiesChart');
                if (!ctx) return;
                
                if (data.error) {
                    console.error('Server error:', data.error);
                    const ctx2d = ctx.getContext('2d');
                    ctx2d.font = '16px Arial';
                    ctx2d.fillStyle = '#999';
                    ctx2d.textAlign = 'center';
                    ctx2d.fillText('เกิดข้อผิดพลาด: ' + data.error, ctx.width / 2, ctx.height / 2);
                    return;
                }
                
                if (data.labels && data.values && data.labels.length > 0) {
                    try {
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: data.labels,
                                datasets: [{
                                    label: 'จำนวนผู้เข้าร่วม',
                                    data: data.values,
                                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top'
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1
                                        }
                                    }
                                }
                            }
                        });
                    } catch (error) {
                        console.error('Chart creation error:', error);
                        const ctx2d = ctx.getContext('2d');
                        ctx2d.font = '16px Arial';
                        ctx2d.fillStyle = '#999';
                        ctx2d.textAlign = 'center';
                        ctx2d.fillText('ไม่สามารถสร้างกราฟได้', ctx.width / 2, ctx.height / 2);
                    }
                } else {
                    const ctx2d = ctx.getContext('2d');
                    ctx2d.font = '16px Arial';
                    ctx2d.fillStyle = '#999';
                    ctx2d.textAlign = 'center';
                    ctx2d.fillText('ไม่มีข้อมูลกิจกรรม', ctx.width / 2, ctx.height / 2);
                }
            })
            .catch(error => {
                console.error('Error loading chart data:', error);
                const ctx = document.getElementById('activitiesChart');
                if (ctx) {
                    const ctx2d = ctx.getContext('2d');
                    ctx2d.font = '16px Arial';
                    ctx2d.fillStyle = '#999';
                    ctx2d.textAlign = 'center';
                    ctx2d.fillText('เกิดข้อผิดพลาดในการโหลดข้อมูล', ctx.width / 2, ctx.height / 2);
                }
            });
    </script>
</body>
</html>
