<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/app/autoload.php';

if (!function_exists('escape_event_html')) {
    function escape_event_html($value)
    {
        return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

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

$event = DB::one(
    "SELECT events_id, events_name
        FROM events
        WHERE events_id = :id",
        ['id' => $eventId]
);

if (!$event) {
    header('Location: manage_events.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirmed = isset($_POST['confirm_delete']);

    if ($confirmed) {
        DB::delete(
            "DELETE FROM events WHERE events_id = :id",
            ['id' => $eventId]
        );
    }

    header('Location: manage_events.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ลบกิจกรรม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-danger shadow-sm">
                    <div class="card-header bg-danger text-white text-center">
                        <h1 class="h5 mb-0">⚠️ ยืนยันการลบกิจกรรม</h1>
                    </div>
                    <div class="card-body text-center">
                        <p class="mb-3">คุณต้องการลบกิจกรรมนี้ใช่หรือไม่?</p>
                        <p class="fw-bold text-danger">"<?= escape_event_html($event['events_name']); ?>"</p>
                        <p class="text-muted">การลบนี้จะไม่สามารถย้อนกลับได้</p>

                        <form method="post" class="d-flex flex-column flex-sm-row gap-2 justify-content-center mt-4">
                            <button type="submit" name="confirm_delete" class="btn btn-danger">
                                ✅ ยืนยันการลบ
                            </button>
                            <a href="manage_events.php" class="btn btn-outline-secondary">
                                ❌ ยกเลิก
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

