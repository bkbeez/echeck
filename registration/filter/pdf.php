<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php');?>
<?php Auth::ajax(APP_PATH.'/registration'); ?>
<?php
$events_id = $_GET['events_id'] ?? null;
if (!$events_id) die("ไม่พบรหัสกิจกรรม");

// ดึงชื่อกิจกรรมเหมือนใน excel.php
$event = DB::one("SELECT events_name FROM events WHERE events_id = :id", ['id'=>$events_id]);

// ดึงรายชื่อผู้เข้าร่วมเหมือนใน excel.php
$lists = DB::sql("SELECT student_id, prefix, firstname, lastname, organization, date_checkin
                    FROM events_lists
                    WHERE events_id = :id AND status = 1
                    ORDER BY date_checkin ASC", ['id'=>$events_id]);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PDF Report - <?=$events_id?></title>
    <style>
        body { font-family: 'Sarabun', sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 5px; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print();"> <div class="no-print" style="text-align: right; margin-bottom: 10px;">
        <button onclick="window.print();">พิมพ์รายงาน / Save as PDF</button>
    </div>
    <h3 style="text-align: center;">รายชื่อผู้เข้าร่วมกิจกรรม: <?=($event['events_name'] ?? $events_id)?></h3>
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">ลำดับ</th>
                <th style="width: 120px;">รหัสนักศึกษา</th>
                <th>ชื่อ-นามสกุล</th>
                <th>หน่วยงาน/คณะ</th>
                <th style="width: 150px;">เวลาเช็คอิน</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($lists) > 0): ?>
                <?php foreach ($lists as $index => $row): ?>
                    <tr>
                        <td class="text-center"><?=($index + 1)?></td>
                        <td class="text-center"><?=$row['student_id']?></td>
                        <td><?=$row['prefix'] . $row['firstname'] . ' ' . $row['lastname']?></td>
                        <td><?=$row['organization']?></td>
                        <td class="text-center"><?=date('d/m/Y H:i', strtotime($row['date_checkin']))?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">ยังไม่มีข้อมูลการเช็คอิน</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>