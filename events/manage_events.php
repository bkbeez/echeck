<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/app/autoload.php';

if (!Auth::check()) {
    $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'] ?? APP_HOME;
    header('Location: ' . APP_HOME . '/login/index.php');
    exit;
}

if (!function_exists('escape_event_html')) {
    function escape_event_html($value)
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('format_event_date')) {
    function format_event_date($value)
    {
        if (empty($value)) {
            return '-';
        }

        try {
            $date = new DateTime($value);
            return $date->format('d/m/Y');
        } catch (Exception $exception) {
            return escape_event_html($value);
        }
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

if (!function_exists('event_status_badge_info')) {
    function event_status_badge_info($status)
    {
        $map = [
            0 => ['label' => 'ร่าง', 'class' => 'badge-warning'],
            1 => ['label' => 'เปิด', 'class' => 'badge-success'],
            2 => ['label' => 'ปิด', 'class' => 'badge-secondary'],
            3 => ['label' => 'ยกเลิก', 'class' => 'badge-danger'],
            'draft' => ['label' => 'ร่าง', 'class' => 'badge-warning'],
            'active' => ['label' => 'เปิด', 'class' => 'badge-success'],
            'inactive' => ['label' => 'ปิด', 'class' => 'badge-secondary'],
            'closed' => ['label' => 'ปิด', 'class' => 'badge-secondary'],
            'cancelled' => ['label' => 'ยกเลิก', 'class' => 'badge-danger'],
            'archived' => ['label' => 'เก็บถาวร', 'class' => 'badge-dark'],
        ];

        $key = is_numeric($status) ? (int) $status : strtolower(trim((string) $status));

        return $map[$key] ?? ['label' => escape_event_html($status ?: 'ไม่ระบุ'), 'class' => 'badge-primary'];
    }
}

$events = DB::query(sprintf(
    "SELECT events_id, events_name, %s AS start_date, %s AS end_date, participant_type, status
    FROM events
    ORDER BY %s DESC, events_id DESC",
    $startColumn,
    $endColumn,
    $startColumn
));

if (!is_array($events)) {
    $events = [];
    }
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการกิจกรรม</title>
    <style>
        :root {
            color-scheme: light;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --success: #16a34a;
            --danger: #dc2626;
            --warning: #f97316;
            --secondary: #64748b;
        }
        *, *::before, *::after {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f4f6fb 0%, #ffffff 100%);
            color: #1f2937;
        }
        .page-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 48px 16px;
        }
        .card {
            width: min(1080px, 100%);
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.18);
            overflow: hidden;
        }
        .card-header {
            padding: 32px clamp(24px, 6vw, 48px);
            background: linear-gradient(120deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #ffffff;
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: space-between;
            align-items: center;
        }
        .card-header h1 {
            margin: 0;
            font-size: clamp(1.5rem, 3vw, 2.25rem);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .card-header h1 span {
            font-size: 1.75rem;
        }
        .card-header p {
            margin: 4px 0 0;
            max-width: 520px;
            font-size: 0.95rem;
            opacity: 0.85;
        }
        .primary-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 999px;
            font-weight: 600;
            color: #1d4ed8;
            background: #ffffff;
            border: none;
            text-decoration: none;
                box-shadow: 0 10px 25px -10px rgba(255, 255, 255, 0.8);
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }
            .primary-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 15px 35px -12px rgba(255, 255, 255, 0.9);
            }
            .card-body {
                padding: clamp(24px, 5vw, 48px);
            }
            .table-wrapper {
                overflow-x: auto;
                border-radius: 18px;
                border: 1px solid #e2e8f0;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                min-width: 720px;
            }
            thead th {
                background: #f8fafc;
                text-align: left;
                font-weight: 600;
                padding: 16px 20px;
                color: #334155;
                font-size: 0.95rem;
                letter-spacing: 0.01em;
            }
            tbody td {
                padding: 18px 20px;
                border-top: 1px solid #e2e8f0;
                color: #0f172a;
                font-size: 0.95rem;
            }
            tbody tr:hover {
                background: rgba(37, 99, 235, 0.05);
                transition: background 0.2s ease;
            }
            .badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 14px;
                border-radius: 999px;
                font-size: 0.8rem;
                font-weight: 600;
                text-transform: capitalize;
            }
            .badge::before {
                content: "";
                width: 8px;
                height: 8px;
                border-radius: 50%;
            }
            .badge-success {
                background: rgba(22, 163, 74, 0.12);
                color: var(--success);
            }
            .badge-success::before {
                background: var(--success);
            }
            .badge-danger {
                background: rgba(220, 38, 38, 0.12);
                color: var(--danger);
            }
            .badge-danger::before {
                background: var(--danger);
            }
            .badge-warning {
                background: rgba(249, 115, 22, 0.12);
                color: var(--warning);
            }
            .badge-warning::before {
                background: var(--warning);
            }
            .badge-secondary {
                background: rgba(100, 116, 139, 0.14);
                color: var(--secondary);
            }
            .badge-secondary::before {
                background: var(--secondary);
            }
            .badge-dark {
                background: rgba(15, 23, 42, 0.16);
            color: #0f172a;
        }
        .badge-dark::before {
            background: #0f172a;
        }
        .badge-primary {
            background: rgba(37, 99, 235, 0.14);
            color: var(--primary);
        }
        .badge-primary::before {
            background: var(--primary);
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn:hover {
            transform: translateY(-1px);
        }
        .btn-edit {
            background: rgba(37, 99, 235, 0.14);
            color: var(--primary);
        }
        .btn-edit:hover {
            box-shadow: 0 10px 25px -12px rgba(37, 99, 235, 0.6);
        }
        .btn-delete {
            background: rgba(220, 38, 38, 0.15);
            color: var(--danger);
        }
        .btn-delete:hover {
            box-shadow: 0 10px 25px -12px rgba(220, 38, 38, 0.5);
        }
        .empty-state {
            padding: 48px;
            text-align: center;
            color: #475569;
        }
        .empty-state h2 {
            margin: 0 0 12px;
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
        }
        .empty-state p {
            margin: 0;
            font-size: 0.95rem;
            opacity: 0.8;
        }
        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .primary-button {
                width: 100%;
                justify-content: center;
            }
            tbody td {
                padding: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <div class="card">
            <div class="card-header">
                <div>
                    <h1><span>📅</span>จัดการรายการกิจกรรม</h1>
                    <p>ตรวจสอบสถานะ อัปเดตข้อมูล และควบคุมการจัดการกิจกรรมทั้งหมดได้จากหน้าเดียว</p>
                </div>
                <a href="create_events.php" class="primary-button">
                    <span>➕</span>
                    <span>เพิ่มกิจกรรมใหม่</span>
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($events)): ?>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>รหัสกิจกรรม</th>
                                    <th>ชื่อกิจกรรม</th>
                                    <th>วันที่เริ่มต้น</th>
                                    <th>วันที่สิ้นสุด</th>
                                    <th>ประเภทผู้เข้าร่วม</th>
                                    <th>สถานะ</th>
                                    <th style="width: 170px;">การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($events as $row): ?>
                                    <?php
                                    $startDate = format_event_date($row['start_date'] ?? null);
                                    $endDate = format_event_date($row['end_date'] ?? null);
                                    $statusInfo = event_status_badge_info($row['status'] ?? null);
                                    $participantType = strtoupper(trim((string) ($row['participant_type'] ?? 'ALL')));
                                    $participantLabel = $participantType === 'LIST' ? 'เฉพาะรายชื่อ' : 'ทุกคน';
                                    ?>
                                    <tr>
                                        <td><?php echo escape_event_html($row['events_id'] ?? '-'); ?></td>
                                        <td><?php echo escape_event_html($row['events_name'] ?? '-'); ?></td>
                                        <td><?php echo $startDate; ?></td>
                                        <td><?php echo $endDate; ?></td>
                                        <td>
                                            <span class="badge badge-primary">
                                                <?php echo escape_event_html($participantLabel); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo escape_event_html($statusInfo['class']); ?>">
                                                <?php echo escape_event_html($statusInfo['label']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="edit_events.php?id=<?php echo urlencode($row['events_id'] ?? ''); ?>"
                                                class="btn btn-edit">แก้ไข</a>
                                                <a href="delete.php?id=<?php echo urlencode($row['events_id'] ?? ''); ?>"
                                                    class="btn btn-delete"
                                                    onclick="return confirm('ยืนยันการลบกิจกรรมนี้?');">
                                                    ลบ
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <h2>ยังไม่มีกิจกรรม</h2>
                        <p>เริ่มเพิ่มกิจกรรมใหม่เพื่อจัดการและวางแผนได้ทันที</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>