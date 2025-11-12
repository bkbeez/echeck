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

$statuses = [
    0 => 'ร่าง',
    1 => 'เปิดให้ลงทะเบียน',
    2 => 'ปิดรับลงทะเบียน',
    3 => 'ยกเลิก',
];

$participantOptions = [
    'ALL' => 'ทุกคน',
    'LIST' => 'เฉพาะรายชื่อ',
];

$input = [
    'events_name' => '',
    'start_date' => '',
    'end_date' => '',
    'participant_type' => 'ALL',
    'status' => 0,
];

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input['events_name'] = trim($_POST['events_name'] ?? '');
    $input['start_date'] = trim($_POST['start_date'] ?? '');
    $input['end_date'] = trim($_POST['end_date'] ?? '');
    $input['participant_type'] = strtoupper(trim($_POST['participant_type'] ?? 'ALL'));
    $input['status'] = is_numeric($_POST['status'] ?? null) ? (int) $_POST['status'] : $input['status'];

    if ($input['events_name'] === '') {
        $errors[] = 'กรุณากรอกชื่อกิจกรรม';
    }

    $startDate = null;
    $startDateForDb = null;
    if ($input['start_date'] === '') {
        $errors[] = 'กรุณาเลือกวันที่เริ่มต้น';
    } else {
        $startDate = DateTime::createFromFormat('d/m/Y', $input['start_date']);
        if (!$startDate || $startDate->format('d/m/Y') !== $input['start_date']) {
            $errors[] = 'รูปแบบวันที่เริ่มต้นไม่ถูกต้อง';
            $startDate = null;
        } else {
            $startDateForDb = $startDate->format('Y-m-d');
        }
    }

    $endDate = null;
    $endDateForDb = null;
    if ($input['end_date'] === '') {
        $errors[] = 'กรุณาเลือกวันที่สิ้นสุด';
    } else {
        $endDate = DateTime::createFromFormat('d/m/Y', $input['end_date']);
        if (!$endDate || $endDate->format('d/m/Y') !== $input['end_date']) {
            $errors[] = 'รูปแบบวันที่สิ้นสุดไม่ถูกต้อง';
            $endDate = null;
        } else {
            $endDateForDb = $endDate->format('Y-m-d');
        }
    }

    if ($startDate && $endDate && $startDate > $endDate) {
        $errors[] = 'วันที่เริ่มต้นต้องไม่น้อยกว่าวันที่สิ้นสุด';
    }

    if (!array_key_exists($input['participant_type'], $participantOptions)) {
        $errors[] = 'รูปแบบผู้เข้าร่วมกิจกรรมไม่ถูกต้อง';
    }

    if (!array_key_exists($input['status'], $statuses)) {
        $errors[] = 'สถานะกิจกรรมไม่ถูกต้อง';
    }

    if (empty($errors) && (!$startDateForDb || !$endDateForDb)) {
        $errors[] = 'รูปแบบวันที่ไม่ถูกต้อง';
    }

    if (empty($errors)) {
        $userId = $_SESSION['login']['user']['id'] ?? null;
        if (!$userId) {
            $errors[] = 'ไม่พบข้อมูลผู้ใช้งาน กรุณาเข้าสู่ระบบใหม่';
        } else {
            $isCreated = DB::create(
                sprintf(
                    "INSERT INTO events (user_id, events_name, %s, %s, participant_type, status) 
                        VALUES (:user_id, :events_name, :start_date, :end_date, :participant_type, :status)",
                    $startColumn,
                    $endColumn
                ),
                [
                    ':user_id' => $userId,
                    ':events_name' => $input['events_name'],
                    ':start_date' => $startDateForDb,
                    ':end_date' => $endDateForDb,
                    ':participant_type' => $input['participant_type'],
                    ':status' => $input['status'],
                ]
            );

            if ($isCreated) {
                $success = 'เพิ่มกิจกรรมเรียบร้อยแล้ว';
                $input['events_name'] = '';
                $input['start_date'] = '';
                $input['end_date'] = '';
                $input['participant_type'] = 'ALL';
                $input['status'] = 0;
            } else {
                $errors[] = 'ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่อีกครั้ง';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มกิจกรรมใหม่</title>
    <style>
        :root {
            color-scheme: light;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --success: #16a34a;
            --danger: #dc2626;
            --warning: #f97316;
        }
        *, *::before, *::after {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(130deg, #eef2ff 0%, #ffffff 70%);
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
            width: min(720px, 100%);
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 25px 45px -12px rgba(37, 99, 235, 0.22);
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(120deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #ffffff;
            padding: clamp(28px, 6vw, 40px);
        }
        .header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .card-header h1 {
            margin: 0;
            font-size: clamp(1.6rem, 3vw, 2.2rem);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .card-header p {
            margin-top: 8px;
            font-size: 0.95rem;
            opacity: 0.85;
        }
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 24px;
            border-radius: 999px;
            background: #ffffff;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 22px 40px -18px rgba(37, 99, 235, 0.55);
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }
        .back-button .icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 999px;
            background: linear-gradient(135deg, #7c3aed 0%, #2563eb 100%);
            color: #ffffff;
            font-size: 0.95rem;
        }
        .back-button .text {
            color: var(--primary-dark);
            letter-spacing: 0.02em;
        }
        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 28px 48px -20px rgba(37, 99, 235, 0.6);
            background: #ffffff;
        }
        .back-button:active {
            transform: translateY(0);
            box-shadow: 0 16px 32px -18px rgba(37, 99, 235, 0.5);
        }
        @media (max-width: 600px) {
            .back-button {
                width: 100%;
                justify-content: center;
            }
        }
        .card-body {
            padding: clamp(28px, 6vw, 44px);
        }
        .alert {
            border-radius: 16px;
            padding: 16px 20px;
            font-size: 0.95rem;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .alert-success {
            background: rgba(22, 163, 74, 0.12);
            color: var(--success);
            border: 1px solid rgba(22, 163, 74, 0.35);
        }
        .alert-error {
            background: rgba(220, 38, 38, 0.12);
            color: var(--danger);
            border: 1px solid rgba(220, 38, 38, 0.35);
        }
        .alert ul {
            margin: 8px 0 0 20px;
            padding: 0;
        }
        form {
            display: grid;
            gap: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        label {
            font-weight: 600;
            color: #1e293b;
        }
        input[type="text"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #cbd5f5;
            border-radius: 14px;
            font-size: 0.98rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            background: #f8f9ff;
        }
        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18);
            background: #ffffff;
        }
        .form-row {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }
        .form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 8px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 999px;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none;
        }
        .btn-primary {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 15px 30px -12px rgba(37, 99, 235, 0.6);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 20px 35px -14px rgba(37, 99, 235, 0.75);
        }
        .btn-secondary {
            background: rgba(100, 116, 139, 0.15);
            color: var(--secondary);
        }
        .btn-secondary:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 28px -14px rgba(100, 116, 139, 0.45);
        }
        @media (max-width: 600px) {
            .form-actions {
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
        <div class="card">
            <div class="card-header">
                <div class="header-top">
                    <h1>➕ เพิ่มกิจกรรมใหม่</h1>
                    <a href="manage_events.php" class="back-button">
                        <span class="text">← กลับไปหน้าจัดการกิจกรรม</span>
                    </a>
                </div>
                <p>บันทึกข้อมูลกิจกรรม กำหนดช่วงเวลา ผู้เข้าร่วม และสถานะได้จากที่นี่</p>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        ✅ <?php echo escape_event_html($success); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        ⚠️ กรุณาตรวจสอบข้อมูลอีกครั้ง
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo escape_event_html($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" novalidate>
                    <div class="form-group">
                        <label for="events_name">ชื่อกิจกรรม </label>
                        <input
                            type="text"
                            id="events_name"
                            name="events_name"
                            value="<?php echo escape_event_html($input['events_name']); ?>"
                            placeholder="ระบุชื่อกิจกรรม"
                            required
                        >
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_date">วันที่เริ่มต้น </label>
                            <input
                                type="text"
                                inputmode="numeric"
                                pattern="^\\d{1,2}/\\d{1,2}/\\d{4}$"
                                id="start_date"
                                name="start_date"
                                value="<?php echo escape_event_html($input['start_date']); ?>"
                                placeholder="dd/mm/yyyy"
                                data-date-format="dd/mm/yyyy"
                                required
                            >
                        </div>
                        <div class="form-group">
                            <label for="end_date">วันที่สิ้นสุด </label>
                            <input
                                type="text"
                                inputmode="numeric"
                                pattern="^\\d{1,2}/\\d{1,2}/\\d{4}$"
                                id="end_date"
                                name="end_date"
                                value="<?php echo escape_event_html($input['end_date']); ?>"
                                placeholder="dd/mm/yyyy"
                                data-date-format="dd/mm/yyyy"
                                required
                            >
                        </div>
            </div>
            
                    <div class="form-row">
                        <div class="form-group">
                            <label for="participant_type">ประเภทผู้เข้าร่วม </label>
                            <select id="participant_type" name="participant_type">
                                <?php foreach ($participantOptions as $value => $label): ?>
                                    <option value="<?php echo escape_event_html($value); ?>"
                                        <?php echo $value === $input['participant_type'] ? 'selected' : ''; ?>>
                                        <?php echo escape_event_html($label); ?>
                                    </option>
                                <?php endforeach; ?>
                </select>
            </div>
                        <div class="form-group">
                            <label for="status">สถานะกิจกรรม </label>
                            <select id="status" name="status">
                                <?php foreach ($statuses as $value => $label): ?>
                                    <option value="<?php echo escape_event_html($value); ?>"
                                        <?php echo (int) $value === (int) $input['status'] ? 'selected' : ''; ?>>
                                        <?php echo escape_event_html($label); ?>
                                    </option>
                                <?php endforeach; ?>
                </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a class="btn btn-secondary" href="manage_events.php">ยกเลิก</a>
                        <button class="btn btn-primary" type="submit">บันทึกกิจกรรม</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    (function() {
        const dateInputs = document.querySelectorAll('[data-date-format="dd/mm/yyyy"]');

        function formatValue(value) {
            const digits = value.replace(/\D/g, '').slice(0, 8);
            let result = '';
            if (digits.length > 0) {
                result += digits.slice(0, Math.min(2, digits.length));
            }
            if (digits.length >= 3) {
                result += '/' + digits.slice(2, Math.min(4, digits.length));
            }
            if (digits.length >= 5) {
                result += '/' + digits.slice(4, 8);
            }
            return result;
        }

        dateInputs.forEach((input) => {
            input.addEventListener('input', (event) => {
                const caretPosition = event.target.selectionStart;
                const previousLength = event.target.value.length;
                event.target.value = formatValue(event.target.value);
                const newLength = event.target.value.length;
                const diff = newLength - previousLength;
                event.target.setSelectionRange(caretPosition + (diff > 0 ? diff : 0), caretPosition + (diff > 0 ? diff : 0));
            });

            input.addEventListener('blur', () => {
                if (input.value === '') {
                    return;
                }
                const parts = input.value.split('/');
                if (parts.length !== 3) {
                    return;
                }
                const [day, month, year] = parts.map((part) => parseInt(part, 10));
                const date = new Date(year, month - 1, day);
                if (
                    !Number.isInteger(day) ||
                    !Number.isInteger(month) ||
                    !Number.isInteger(year) ||
                    date.getFullYear() !== year ||
                    date.getMonth() + 1 !== month ||
                    date.getDate() !== day
                ) {
                    return;
                }
                const twoDigit = (num) => num.toString().padStart(2, '0');
                input.value = `${twoDigit(day)}/${twoDigit(month)}/${year}`;
            });
        });
    })();
</script>
</body>
</html>