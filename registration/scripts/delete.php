<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php Auth::check(APP_PATH.'/registration'); ?>
<?php 
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
$events_id = $_POST['events_id'] ?? null;
if (!$id || !$events_id) {
    echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}
if (DB::query("DELETE FROM `events_lists` WHERE `id` = :id AND `events_id` = :ev_id", ['id' => $id, 'ev_id' => $events_id])) {
    DB::query("UPDATE `events` 
               SET `participants` = (SELECT COUNT(id) FROM events_lists WHERE events_id = :ev_id) 
               WHERE events_id = :ev_id_where", 
               ['ev_id' => $events_id, 'ev_id_where' => $events_id]);
    echo json_encode(['status' => 'success', 'message' => 'ลบรายชื่อเรียบร้อยแล้ว']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถลบข้อมูลได้']);
}
?>