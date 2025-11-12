<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มกิจกรรม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="card card-body">
            <h3>➕ เพิ่มกิจกรรมใหม่</h3>
            <form method="post" class="bg-white p-4 rounded shadow-sm mt-3">
                <div class="mb-3">
                    <label class="form-label">ชื่อกิจกรรม</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label">วันที่เริ่มต้น</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                <div class="col mb-3">
                    <label class="form-label">วันที่สิ้นสุด</label>
                    <input type="date" name="end_date" class="form-control" required>
                </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">ประเภทผู้เข้าร่วม</label>
                    <select name="participant_type" class="form-select">
                        <option value="ALL">ทุกคน</option>
                        <option value="LIST">เฉพาะผู้ที่มีรายชื่อ</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">สถานะ</label>
                    <select name="status" class="form-select">
                        <option value="0">ร่าง</option>
                        <option value="1">เปิดการเข้าร่วม</option>
                        <option value="2">ปิดการเข้าร่วม</option>
                        <option value="3">ยกเลิก</option>
                    </select>
                </div>
                    <button type="submit" class="btn btn-success">บันทึก</button>
                    <a href="index.php" class="btn btn-secondary">กลับ</a>
            </form>
        </div>
    </div>

</body>
</html>