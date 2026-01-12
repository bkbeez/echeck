<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
$events_id = $_POST['events_id'] ?? null;

if($events_id) {
    $stats = DB::one("SELECT COUNT(*) as total FROM events_lists WHERE events_id = :id AND status = 1", ['id'=>$events_id]);
    $lists = DB::sql("SELECT student_id, firstname, lastname, DATE_FORMAT(date_checkin, '%H:%i:%s') as checkin_time 
                    FROM events_lists
                    WHERE events_id = :id AND status = 1
                    ORDER BY date_checkin DESC LIMIT ''", ['id'=>$events_id]);
    echo json_encode([
        'arrived_count' => number_format($stats['total']),
        'lists' => $lists
    ]);
}
?>
