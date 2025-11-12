<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/app/autoload.php';

if (!Auth::check()) {
    $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'] ?? APP_HOME;
    header('Location: ' . APP_HOME . '/login/index.php');
    exit;
}

$eventId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($eventId <= 0) {
    header('Location: edit_events.php');
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

function formatDateInput($value)
{
    if (!$value) {
        return '';
    }
    try {
        return (new DateTime($value))->format('Y-m-d');
    } catch (Exception $exception) {
        return '';
    }
}

$startColumn = events_column_name('start_date', 'start_time');
$endColumn = events_column_name('end_date', 'end_time');

$event = DB::one(
    sprintf(
        "SELECT events_id, events_name, %s AS start_date, %s AS end_date, participant_type, status
        FROM events
        WHERE events_id = :id",
        $startColumn,
        $endColumn
    ),
    ['id' => $eventId]
);

if (!$event) {
    header('Location: edit_events.php');
    exit;
}

if (isset($event['start_time']) && !isset($event['start_date'])) {
    $event['start_date'] = $event['start_time'];
}
if (isset($event['end_time']) && !isset($event['end_date'])) {
    $event['end_date'] = $event['end_time'];
}

$statuses = [
    0 => 'ร่าง',
    1 => 'เปิดการเข้าร่วม',
    2 => 'ปิดการเข้าร่วม',
    3 => 'ยกเลิก',
];

$participantOptions = [
    'ALL' => 'ทุกคน',
    'LIST' => 'เฉพาะรายชื่อ',
];

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['events_name'] ?? '');
    $startInput = trim($_POST['start_date'] ?? '');
    $endInput = trim($_POST['end_date'] ?? '');
    $participantType = strtoupper(trim($_POST['participant_type'] ?? 'ALL'));
    $status = (int) ($_POST['status'] ?? 0);

    if ($name === '') {
        $errors[] = 'กรุณากรอกชื่อกิจกรรม';
    }
//เวลา dd/mm/yyyy
    $startDate = null;
    if ($startInput === '') {
        $errors[] = 'กรุณาเลือกวันที่เริ่มต้น';
    } else {
        $startDate = DateTime::createFromFormat('Y-m-d', $startInput);
        if (!$startDate || $startDate->format('Y-m-d') !== $startInput) {
            $errors[] = 'รูปแบบวันที่เริ่มต้นไม่ถูกต้อง';
            $startDate = null;
        }
    }

    $endDate = null;
    if ($endInput === '') {
        $errors[] = 'กรุณาเลือกวันที่สิ้นสุด';
    } else {
        $endDate = DateTime::createFromFormat('Y-m-d', $endInput);
        if (!$endDate || $endDate->format('Y-m-d') !== $endInput) {
            $errors[] = 'รูปแบบวันที่สิ้นสุดไม่ถูกต้อง';
            $endDate = null;
        }
    }

    if ($startDate && $endDate && $startDate > $endDate) {
        $errors[] = 'วันที่เริ่มต้นต้องไม่น้อยกว่าวันที่สิ้นสุด';
    }

    if (!isset($participantOptions[$participantType])) {
        $errors[] = 'ประเภทผู้เข้าร่วมไม่ถูกต้อง';
    }

    if (!isset($statuses[$status])) {
        $errors[] = 'สถานะกิจกรรมไม่ถูกต้อง';
    }

    if (empty($errors)) {
        $updated = DB::update(
            sprintf(
                "UPDATE events
                    SET events_name = :name,
                        %s = :start_date,
                        %s = :end_date,
                        participant_type = :participant_type,
                        status = :status
                    WHERE events_id = :id",
                $startColumn,
                $endColumn
            ),
            [
                'name' => $name,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'participant_type' => $participantType,
                'status' => $status,
                'id' => $eventId,
            ]
        );

        if ($updated) {
            $success = 'บันทึกการแก้ไขเรียบร้อยแล้ว';
            $event = DB::one(
                sprintf(
                    "SELECT events_id, events_name, %s AS start_date, %s AS end_date, participant_type, status
                        FROM events
                        WHERE events_id = :id",
                    $startColumn,
                    $endColumn
                ),
                ['id' => $eventId]
            );
        } else {
            $errors[] = 'ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>แก้ไขกิจกรรม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="mb-4">
            <a href="manage_events.php" class="text-decoration-none fw-semibold">
                ← กลับไปหน้าจัดการกิจกรรม
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h1 class="h4 mb-0">📝 แก้ไขกิจกรรม</h1>
            </div>
            <div class="card-body">
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= escape_event_html($success); ?></div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $message): ?>
                                <li><?= escape_event_html($message); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" novalidate>
                    <div class="mb-3">
                        <label for="events_name" class="form-label">ชื่อกิจกรรม</label>
                        <input
                            type="text"
                            class="form-control"
                            id="events_name"
                            name="events_name"
                            required
                            value="<?= escape_event_html($_POST['events_name'] ?? $event['events_name'] ?? ''); ?>"
                            placeholder="ระบุชื่อกิจกรรม"
                        >
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">วันที่เริ่มต้น</label>
                            <input
                                type="date"
                                class="form-control"
                                id="start_date"
                                name="start_date"
                                required
                                value="<?= escape_event_html($_POST['start_date'] ?? formatDateInput($event['start_date'] ?? null)); ?>"
                            >
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">วันที่สิ้นสุด</label>
                            <input
                                type="date"
                                class="form-control"
                                id="end_date"
                                name="end_date"
                                required
                                value="<?= escape_event_html($_POST['end_date'] ?? formatDateInput($event['end_date'] ?? null)); ?>"
                            >
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="participant_type" class="form-label">ประเภทผู้เข้าร่วม</label>
                            <select class="form-select" id="participant_type" name="participant_type">
                                <?php foreach ($participantOptions as $value => $label): ?>
                                    <option value="<?= escape_event_html($value); ?>"
                                        <?= (($_POST['participant_type'] ?? $event['participant_type']) === $value) ? 'selected' : ''; ?>>
                                        <?= escape_event_html($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">สถานะกิจกรรม</label>
                            <select class="form-select" id="status" name="status">
                                <?php foreach ($statuses as $value => $label): ?>
                                    <option value="<?= escape_event_html($value); ?>"
                                        <?= ((int) ($_POST['status'] ?? $event['status']) === (int) $value) ? 'selected' : ''; ?>>
                                        <?= escape_event_html($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            💾 บันทึกการแก้ไข
                        </button>
                        <a href="manage_events.php" class="btn btn-outline-secondary">
                            ยกเลิก
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

