<?php 
include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php');
Auth::check(APP_PATH.'/registration');

$events_id = $_GET['events_id'] ?? null;
if (!$events_id) {
    die("<div style='color:red; padding:20px;'>Error: ไม่พบรหัสกิจกรรม (events_id is missing)</div>");
}
$event = DB::one("SELECT events_name FROM events WHERE events_id = :id", ['id'=>$events_id]);
$sql = "SELECT student_id, prefix, firstname, lastname, organization, email, date_checkin 
        FROM events_lists 
        WHERE events_id = :id 
        ORDER BY date_checkin ASC";
$lists = DB::sql($sql, ['id'=>$events_id]);
?>
<!DOCTYPE html>
<html lang="<?=App::lang()?>">
<head>
    <meta charset="UTF-8">
    <title>รายงานผู้เข้าร่วมกิจกรรม</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #5d5fef;
            --secondary-color: #f4f7fe;
            --text-dark: #2d3436;
            --text-light: #636e72;
            --border-color: #dfe6e9;
        }
        body { 
            font-family: 'Sarabun', sans-serif; 
            font-size: 13px; 
            color: var(--text-dark); 
            background-color: #fff;
            margin: 0;
            padding: 0;
        }
        .report-header {
            background-color: var(--primary-color);
            color: white;
            padding: 40px 30px;
            text-align: center;
            border-bottom-right-radius: 50px;
            margin-bottom: 30px;
        }
        .report-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 24px;
        }
        .summary-bar {
            display: flex;
            justify-content: space-between;
            margin: 0 30px 20px 30px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--secondary-color);
            color: var(--text-light);
            font-weight: 600;
        }
        .table-container {
            padding: 0 30px 50px 30px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        th { 
            background-color: var(--secondary-color);
            color: var(--primary-color);
            font-weight: 700;
            text-align: left;
            padding: 12px 10px;
            border-bottom: 2px solid var(--primary-color);
            font-size: 11px;
            text-transform: uppercase;
        }
        td { 
            padding: 12px 10px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-dark);
            vertical-align: middle;
        }
        tr:nth-child(even) { background-color: #fafbfc; }
        .text-center { text-align: center; }
        
        .no-print { 
            position: fixed; top: 20px; right: 20px; z-index: 999;
        }
        button { 
            padding: 10px 25px; cursor: pointer; background: var(--primary-color); 
            color: #fff; border: none; border-radius: 8px; font-weight: 600;
            box-shadow: 0 4px 15px rgba(93, 95, 239, 0.3); transition: 0.3s;
        }
        button:hover { transform: translateY(-2px); }

        @media print {
            .no-print { display: none; }
            .report-header { border-bottom-right-radius: 0; padding: 30px; }
            @page { size: A4; margin: 1cm; }
            .table-container { padding: 0; }
            .summary-bar { margin: 0 0 20px 0; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print();">พิมพ์รายงาน / Save as PDF</button>
    </div>

    <div class="report-header">
        <h2>รายชื่อผู้เข้าร่วมกิจกรรม</h2>
        <p><?=htmlspecialchars($event['events_name'] ?? 'รหัสกิจกรรม: '.$events_id)?></p>
    </div>

    <div class="summary-bar">
        <span>สรุปจำนวนผู้เช็คอิน: <?=count($lists)?> คน</span>
        <span>วันที่ออกรายงาน: <?=date('d/m/Y H:i')?></span>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">ลำดับ</th>
                    <th style="width: 100px;">รหัสนักศึกษา</th>
                    <th style="width: 60px;">คำนำหน้า</th>
                    <th>ชื่อ - นามสกุล</th>
                    <th>หน่วยงาน/คณะ</th>
                    <th>อีเมล</th>
                    <th class="text-center" style="width: 120px;">เวลาเช็คอิน</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($lists)): ?>
                    <?php foreach ($lists as $index => $row): ?>
                        <tr>
                            <td class="text-center"><?=($index + 1)?></td>
                            <td class="text-center"><?=htmlspecialchars($row['student_id'] ?? '-')?></td>
                            <td class="text-center"><?=htmlspecialchars($row['prefix'] ?? '')?></td>
                            <td><?=htmlspecialchars(($row['firstname'] ?? '').' '.($row['lastname'] ?? ''))?></td>
                            <td><?=htmlspecialchars($row['organization'] ?? '-')?></td>
                            <td><?=htmlspecialchars($row['email'] ?? '-')?></td>
                            <td class="text-center">
                                <span class="time-tag">
                                    <?= $row['date_checkin'] ? date('d/m/Y H:i', strtotime($row['date_checkin'])) : '-' ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 50px; color: var(--text-light);">
                            <i class="uil uil-info-circle"></i> ยังไม่มีข้อมูลการเช็คอินในกิจกรรมนี้
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>