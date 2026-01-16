<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php');?>
<?php Auth::check(APP_PATH.'/registration'); ?>
<?php 
$events_id = isset($_GET['events_id']) ? $_GET['events_id'] : null;
if (!$events_id) {
    die("Access Denied: Invalid Request");
}
$user_email = User::get('email');
$check_owner = DB::one("SELECT events_id FROM events
                        WHERE events_id = :id
                        AND (user_create = :user OR events_id IN (SELECT events_id FROM events_shared WHERE email = :user))", 
                        ['id' => $events_id, 'user' => $user_email]);
if (!$check_owner) {
    die("Permission Denied: คุณไม่มีสิทธิ์เข้าถึงข้อมูลส่วนนี้");
}
$event = DB::one("SELECT events_name FROM events WHERE events_id = :id", ['id' => $events_id]);
$lists = DB::sql("SELECT student_id, prefix, firstname, lastname, organization, email, date_checkin
                    FROM events_lists
                    WHERE events_id = :id AND status = 1
                    ORDER BY date_checkin ASC", ['id' => $events_id]);
$safe_event_name = $event ? preg_replace('/[^a-zA-Z0-9]/', '_', $event['events_name']) : 'Event';
$filename = "Report_" . $safe_event_name . "_" . date('Ymd_Hi') . ".xls";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
echo '<head><meta http-equiv="Content-type" content="text/html;charset=utf-8" /></head><body>';
echo '<h3>รายชื่อผู้เข้าร่วม: ' . htmlspecialchars($event['events_name'] ?? '') . '</h3>';
echo '<table border="1">
        <tr>
            <th style="background-color: #DFF0D8;">ลำดับ</th>
            <th style="background-color: #DFF0D8;">รหัสนักศึกษา</th>
            <th style="background-color: #DFF0D8;">คำนำหน้า</th>
            <th style="background-color: #DFF0D8;">ชื่อ</th>
            <th style="background-color: #DFF0D8;">นามสกุล</th>
            <th style="background-color: #DFF0D8;">หน่วยงาน/คณะ</th>
            <th style="background-color: #DFF0D8;">อีเมล</th>
            <th style="background-color: #DFF0D8;">เวลาเช็คอิน</th>
        </tr>';

foreach ($lists as $index => $row) {
    echo '<tr>
            <td align="center">' . ($index + 1) . '</td>
            <td style="vnd.ms-excel.numberformat:@">' . htmlspecialchars($row['student_id'] ?? '') . '</td>
            <td>' . htmlspecialchars($row['prefix'] ?? '') . '</td>
            <td>' . htmlspecialchars($row['firstname'] ?? '') . '</td>
            <td>' . htmlspecialchars($row['lastname'] ?? '') . '</td>
            <td>' . htmlspecialchars($row['organization'] ?? '') . '</td>
            <td>' . htmlspecialchars($row['email'] ?? '') . '</td>
            <td align="center">' . ($row['date_checkin'] ? date('d/m/Y H:i', strtotime($row['date_checkin'])) : '-') . '</td>
        </tr>';
}
echo '</table></body></html>';
exit;
?>