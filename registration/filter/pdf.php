<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php');?>
<?php Auth::ajax(APP_PATH.'/registration'); ?>
<?php
$events_id = $_GET['events_id'] ?? null;
if (!$events_id) die("ไม่พบรหัสกิจกรรม");
$event = DB::one("SELECT events_name FROM events WHERE events_id = :id", ['id'=>$events_id]);
$lists = DB::sql("SELECT student_id, prefix, firstname, lastname, organization, email, date_checkin
                    FROM events_lists
                    WHERE events_id = :id AND status = 1
                    ORDER BY date_checkin ASC", ['id'=>$events_id]);
?>
<html lang="<?=App::lang()?>">
<style>
    :root {
        --primary-color: #5d5fef;
        --secondary-color: #f4f7fe;            --text-dark: #2d3436;
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
        padding: 30px;
        text-align: center;
        border-bottom-right-radius: 50px;
        margin-bottom: 30px;
    }
    .report-header h2 {
        margin: 0;
        font-weight: 700;
        font-size: 22px;
        letter-spacing: 0.5px;
    }
    .report-header p {
        margin: 5px 0 0 0;
        opacity: 0.8;
        font-weight: 300;
    }
    .summary-bar {
        display: flex;
        justify-content: space-between;
        margin: 0 30px 20px 30px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--secondary-color);
        color: var(--text-light);
    }
    .table-container {
        padding: 0 30px;
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
        text-transform: uppercase;
        font-size: 11px;
    }
    td { 
        padding: 10px;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-dark);
    }
    tr:nth-child(even) {
        background-color: #fafbfc;
    }
    .text-center {
        text-align: center; 
    }
    .fw-bold { 
        font-weight: 600; 
    }
    .no-print { 
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 999;
    }
    button { 
        padding: 10px 20px; 
        cursor: pointer; 
        background: var(--primary-color); 
        color: #fff; 
        border: none; 
        border-radius: 8px; 
        font-family: 'Sarabun', sans-serif;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(93, 95, 239, 0.3);
        transition: 0.3s;
    }
    button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(93, 95, 239, 0.4);
    }
    .time-tag {
        background: #e1f5fe;
        color: #0288d1;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }
    @media print {
        .no-print { display: none; }
        .report-header { 
            -webkit-print-color-adjust: exact; 
            border-bottom-right-radius: 0;
        }
    body { background-color: #fff; }
    @page { size: A4; margin: 0; }
        .table-container { padding: 0 1.5cm; }
        .summary-bar { margin: 0 1.5cm 20px 1.5cm; }
    }
</style>
<body onload="window.print();">
    <div class="no-print">
        <button onclick="window.print();">พิมพ์รายงาน / Save as PDF</button>
    </div>
    <h3 style="text-align: center; margin-bottom: 5px;">รายชื่อผู้เข้าร่วมกิจกรรม: <?=htmlspecialchars($event['events_name'] ?? $events_id)?></h3>
    
    <table>
        <thead>
            <tr>
                <th style="width: 35px;">ลำดับ</th>
                <th style="width: 90px;">รหัสนักศึกษา</th>
                <th style="width: 50px;">คำนำหน้า</th>
                <th>ชื่อ</th>
                <th>นามสกุล</th>
                <th>หน่วยงาน/คณะ</th>
                <th>อีเมล</th>
                <th style="width: 110px;">เวลาเช็คอิน</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($lists)): ?>
                <?php foreach ($lists as $index => $row): ?>
                    <tr>
                        <td class="text-center"><?=($index + 1)?></td>
                        <td class="text-center"><?=htmlspecialchars($row['student_id'] ?? '')?></td>
                        <td class="text-center"><?=htmlspecialchars($row['prefix'] ?? '')?></td>
                        <td><?=htmlspecialchars($row['firstname'] ?? '')?></td>
                        <td><?=htmlspecialchars($row['lastname'] ?? '')?></td>
                        <td><?=htmlspecialchars($row['organization'] ?? '')?></td>
                        <td><?=htmlspecialchars($row['email'] ?? '')?></td>
                        <td class="text-center">
                            <?= $row['date_checkin'] ? date('d/m/Y H:i', strtotime($row['date_checkin'])) : '-' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8" class="text-center" style="padding: 20px;">ยังไม่มีข้อมูลการเช็คอิน</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div style="margin-top: 15px; text-align: right; font-size: 11px;">
        สรุปจำนวนผู้เช็คอิน: <?=count($lists)?> คน | วันที่ออกรายงาน: <?=date('d/m/Y H:i')?>
    </div>
</body>