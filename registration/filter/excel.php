<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php');?>
<?php Auth::ajax(APP_PATH.'/registration'); ?>
<?php
$events_id = isset($_GET['events_id']) ? $_GET['events_id'] : (isset($_POST['events_id']) ? $_POST['events_id'] : null);

if (!$events_id) {
    die("ไม่พบรหัสกิจกรรม (Invalid Events ID)");
}

$filename = "Export_Participants_" . date('Ymd_Hi') . ".xls";
$event = DB::one("SELECT events_name FROM events WHERE events_id = :id", ['id'=>$events_id]);
$lists = DB::sql("SELECT student_id, prefix, firstname, lastname, organization, date_checkin
                    FROM events_lists
                    WHERE events_id = :id AND status = 1
                    ORDER BY date_checkin ASC", ['id'=>$events_id]);
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");
echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
