<?php 
    include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php');

    // รับ event_id จาก GET parameter
    $event_id = $_GET['id'] ?? '';
    if($event_id === '') {
        header('Location: event_summary_report.php?error=' . urlencode('กรุณาระบุรหัสกิจกรรม'));
        exit();
    }

    // ดึงข้อมูลกิจกรรม
    try {
        $event = Event::findByid($event_id);
        if (!$event) {
            header('Location: ../event/?error=' . urlencode('ไม่พบข้อมูลกิจกรรม'));
            exit();
        }
    } catch (Exception $e) {
        header('Location: ../event/?error=' . urlencode('เกิดข้อผิดพลาด: ' . $e->getMessage()));
        exit();
    }

    // รับ filter parameters
    $from = $_GET['from'] ?? '';
    $to = $_GET['to'] ?? '';
    $status = $_GET['status'] ?? '';
    $search = $_GET['q'] ?? '';

    // Build query with filters
    $sql = "SELECT * FROM `participants` WHERE `events_id` = :id";
    $params = ['id' => $event_id];

    if ($status !== '') {
        $sql .= " AND `status` = :status";
        $params['status'] = $status;
    }
    if ($from !== '') {
        $sql .= " AND `create_at` >= :from";
        $params['from'] = $from . ' 00:00:00';
    }
    if ($to !== '') {
        $sql .= " AND `create_at` <= :to";
        $params['to'] = $to . ' 23:59:59';
    }
    if ($search !== '') {
        $sql .= " AND (`firstname` LIKE :q OR `lastname` LIKE :q OR `email` LIKE :q)";
        $params['q'] = '%' . $search . '%';
    }
    $sql .= " ORDER BY `create_at` DESC";

    try {
        $participants = DB::query($sql, $params);
        if (!is_array($participants)) {
            $participants = [];
        }
    } catch (Exception $e) {
        $participants = [];
    }

    // สร้าง CSV format (สามารถเปิดด้วย Excel ได้)
    $filename = 'รายงานกิจกรรม_' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $event['events_name'] ?? 'event') . '_' . date('Y-m-d_His') . '.csv';

    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');

    // เปิด output stream
    $output = fopen('php://output', 'w');

    // เพิ่ม BOM สำหรับ UTF-8 เพื่อให้ Excel อ่านภาษาไทยได้ถูกต้อง
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // เขียนหัวตาราง
    $headers = [
        'ลำดับ',
        'คำนำหน้า',
        'ชื่อ',
        'นามสกุล',
        'อีเมล',
        'สถานะ',
        'วันที่ลงทะเบียน',
        'วันที่เช็คอิน'
    ];
    fputcsv($output, $headers);

    // เขียนข้อมูล
    $index = 1;
    foreach($participants as $p) {
        $participantStatus = isset($p['status']) ? $p['status'] : 'รอเข้าร่วม';
        $statusText = ($participantStatus == 'เข้าร่วมแล้ว') ? 'เช็คอินแล้ว' : 
                    (($participantStatus == 'ยกเลิก') ? 'ยกเลิก' : 'ยังไม่เช็คอิน');
        
        $joinedDate = isset($p['create_at']) && $p['create_at'] && $p['create_at'] !== '0000-00-00 00:00:00' 
            ? Helper::datetimeDisplay($p['create_at'], 'th') 
            : '-';
        
        $checkinDate = isset($p['checkin_time']) && $p['checkin_time'] && $p['checkin_time'] !== '0000-00-00 00:00:00'
            ? Helper::datetimeDisplay($p['checkin_time'], 'th')
            : '-';
        
        $row = [
            $index,
            $p['prefix'] ?? '',
            $p['firstname'] ?? '',
            $p['lastname'] ?? '',
            $p['email'] ?? '',
            $statusText,
            $joinedDate,
            $checkinDate
        ];
        
        fputcsv($output, $row);
        $index++;
    }

    // เพิ่มข้อมูลสรุป
    fputcsv($output, []); // บรรทัดว่าง
    fputcsv($output, ['สรุปข้อมูล']);
    fputcsv($output, ['ชื่อกิจกรรม', $event['events_name'] ?? '']);
    fputcsv($output, ['วันที่เริ่ม', isset($event['start_date']) && $event['start_date'] && $event['start_date'] !== '0000-00-00' 
        ? Helper::dateDisplay($event['start_date'], 'th') : '-']);
    fputcsv($output, ['วันที่สิ้นสุด', isset($event['end_date']) && $event['end_date'] && $event['end_date'] !== '0000-00-00' 
        ? Helper::dateDisplay($event['end_date'], 'th') : '-']);
    fputcsv($output, ['ผู้เข้าร่วมทั้งหมด', count($participants)]);

    $checked = 0;
    $notChecked = 0;
    foreach($participants as $p) {
        if (isset($p['status']) && $p['status'] == 'เข้าร่วมแล้ว') {
            $checked++;
        } else {
            $notChecked++;
        }
    }
    fputcsv($output, ['เช็คอินแล้ว', $checked]);
    fputcsv($output, ['ยังไม่เช็คอิน', $notChecked]);
    fputcsv($output, ['วันที่ส่งออก', Helper::datetimeDisplay(date('Y-m-d H:i:s'), 'th')]);

    fclose($output);
    exit();
?>

