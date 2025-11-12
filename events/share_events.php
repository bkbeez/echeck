<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/app/autoload.php';

if (!Auth::check()) {
    $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'] ?? APP_HOME;
    header('Location: ' . APP_HOME . '/login/index.php');
    exit;
}

$eventId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($eventId <= 0) {
    header('Location: manage_events.php');
    exit;
}

if (!function_exists('escape_event_html')) {
    function escape_event_html($value)
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('events_column_name')) {
    function events_column_name($preferred, $fallback)
    {
        static $columns = null;
        if ($columns === null) {
            $columns = [];
            $result = DB::query("SHOW COLUMNS FROM events");
            if (is_array($result)) {
                foreach ($result as $row) {
                    if (isset($row['Field'])) {
                        $columns[$row['Field']] = true;
                    }
                }
            }
        }

        if (isset($columns[$preferred])) {
            return $preferred;
        }
        if (isset($columns[$fallback])) {
            return $fallback;
        }
        return $preferred;
    }
}

$startColumn = events_column_name('start_date', 'start_time');
$endColumn = events_column_name('end_date', 'end_time');

$event = DB::one(
    sprintf(
        "SELECT events_id, events_name, %s AS start_date, %s AS end_date, participant_type 
        FROM events 
        WHERE events_id = :id",
        $startColumn,
        $endColumn
    ),
    ['id' => $eventId]
);

if (!$event) {
    header('Location: manage_events.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $errors[] = 'กรุณากรอกอีเมลผู้รับแชร์';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'รูปแบบอีเมลไม่ถูกต้อง';
    } else {
        $exists = DB::one(
            "SELECT share_id FROM events_shares WHERE events_id = :id AND shared_email = :email",
            ['id' => $eventId, 'email' => $email]
        );

        if ($exists) {
            $errors[] = 'อีเมลนี้ถูกแชร์กิจกรรมอยู่แล้ว';
        } else {
            $created = DB::create(
                "INSERT INTO events_shares (events_id, shared_email) VALUES (:id, :email)",
                ['id' => $eventId, 'email' => $email]
            );

            if ($created) {
                header('Location: share_events.php?id=' . $eventId);
                exit;
            } else {
                $errors[] = 'ไม่สามารถเพิ่มอีเมลผู้รับแชร์ได้';
            }
        }
    }
}

if (isset($_GET['delete_share'])) {
    $shareId = (int) $_GET['delete_share'];
    if ($shareId > 0) {
        DB::delete(
            "DELETE FROM events_shares WHERE share_id = :share_id AND events_id = :events_id",
            ['share_id' => $shareId, 'events_id' => $eventId]
        );
    }
    header('Location: share_events.php?id=' . $eventId);
    exit;
}

$shares = DB::query(
    "SELECT share_id, shared_email, shared_at 
        FROM events_shares 
        WHERE events_id = :id 
        ORDER BY shared_at DESC",
    ['id' => $eventId]
);

function formatEventDate($value)
{
    if (!$value) {
        return '-';
    }
    try {
        return (new DateTime($value))->format('d/m/Y');
    } catch (Exception $exception) {
        return escape_event_html($value);
    }
}

function formatShareDateTime($value)
{
    if (!$value) {
        return '-';
    }
    try {
        $date = new DateTime($value);
        return $date->format('d/m/Y H:i');
    } catch (Exception $exception) {
        return escape_event_html($value);
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แชร์กิจกรรม</title>
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #f3f4f6;
            color: #1f2937;
        }
        .page-wrapper {
            max-width: 960px;
            margin: 48px auto;
            padding: 0 16px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 16px;
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }
        .card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px -20px rgba(15, 23, 42, 0.25);
            margin-bottom: 24px;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(120deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            padding: 24px clamp(24px, 5vw, 40px);
        }
        .card-header h1 {
            margin: 0;
            font-size: clamp(1.5rem, 3vw, 2.1rem);
            font-weight: 600;
        }
        .card-header p {
            margin: 8px 0 0;
            opacity: 0.9;
        }
        .card-body {
            padding: clamp(24px, 5vw, 40px);
        }
        .alert {
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 16px;
            font-size: 0.95rem;
        }
        .alert-error {
            background: rgba(220, 38, 38, 0.12);
            color: #b91c1c;
            border: 1px solid rgba(220, 38, 38, 0.35);
        }
        form {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
        }
        label {
            font-weight: 600;
            width: 100%;
        }
        input[type="email"] {
            flex: 1 1 280px;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid #cbd5f5;
            font-size: 1rem;
        }
        input[type="email"]:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 20px;
            border-radius: 12px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-primary {
            background: #2563eb;
            color: #ffffff;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 15px 30px -15px rgba(37, 99, 235, 0.6);
        }
        .btn-danger {
            background: rgba(220, 38, 38, 0.1);
            color: #b91c1c;
        }
        .btn-danger:hover {
            background: rgba(220, 38, 38, 0.18);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 14px 18px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }
        th {
            background: #f8fafc;
            font-weight: 600;
            color: #334155;
        }
        tbody tr:hover {
            background: rgba(37, 99, 235, 0.05);
        }
        .table-empty {
            text-align: center;
            padding: 32px;
            color: #64748b;
        }
        @media (max-width: 640px) {
            th, td {
                padding: 12px;
            }
            form {
                flex-direction: column;
                align-items: stretch;
            }
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <a href="manage_events.php" class="back-link">← กลับไปหน้ากิจกรรมทั้งหมด</a>

        <div class="card">
            <div class="card-header">
                <h1>แชร์กิจกรรมให้ผู้ใช้อื่น</h1>
                <p><?= escape_event_html($event['events_name']); ?></p>
                <p>
                    วันที่ <?= escape_event_html(formatEventDate($event['start_date'] ?? null)); ?>
                    - <?= escape_event_html(formatEventDate($event['end_date'] ?? null)); ?> ·
                    ประเภทผู้เข้าร่วม: <?= escape_event_html($event['participant_type']); ?>
                </p>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <ul style="margin: 0 0 0 18px; padding: 0;">
                            <?php foreach ($errors as $message): ?>
                                <li><?= escape_event_html($message); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <label for="email">อีเมลผู้รับแชร์</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="เช่น example@email.com"
                        value="<?= escape_event_html($_POST['email'] ?? ''); ?>"
                        required
                    >
                    <button type="submit" class="btn btn-primary">เพิ่มผู้รับแชร์</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body" style="padding-top: 0;">
                <h2 style="margin: 24px 0 12px; font-size: 1.2rem;">รายชื่อผู้รับแชร์</h2>
                <?php if (!empty($shares)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 60px;">ลำดับ</th>
                                <th>อีเมล</th>
                                <th style="width: 160px;">วันที่แชร์</th>
                                <th style="width: 120px;">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($shares as $index => $share): ?>
                                <tr>
                                    <td><?= $index + 1; ?></td>
                                    <td><?= escape_event_html($share['shared_email']); ?></td>
                                    <td><?= escape_event_html(formatShareDateTime($share['shared_at'] ?? null)); ?></td>
                                    <td>
                                        <a
                                            href="share_events.php?id=<?= $eventId; ?>&delete_share=<?= (int) $share['share_id']; ?>"
                                            class="btn btn-danger"
                                            onclick="return confirm('ต้องการยกเลิกการแชร์นี้หรือไม่?');"
                                        >
                                            ลบ
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="table-empty">ยังไม่มีผู้รับแชร์สำหรับกิจกรรมนี้</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>