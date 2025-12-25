<?php include($_SERVER["DOCUMENT_ROOT"].'/app/autoload.php'); ?>
<?php
    $form = ( (isset($_POST['form_as'])&&$_POST['form_as']) ? $_POST['form_as'] : null );
    if( isset($_POST['events_id']) && $_POST['events_id'] ){
        $data = DB::one("SELECT events.*
                        ,events_list.email AS user_role
                        FROM events
                        LEFT JOIN events_lists ON events.email=events_lists.email
                        WHERE events.events_id=:events_id
                        LIMIT 1;"
                        , array('events_id'=>$_POST['events_id'])
        );
        if(isset($data['events_id'])&&$data['events_id']){
            
        }
    }
?>
<!DOCTYPE html>
<html lang="<?=App::lang()?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มกิจกรรม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: radial-gradient(circle at 10% 20%, #e0f2ff 0%, #f3f4ff 40%, #ffffff 100%);
            min-height: 100vh;
            font-family: "Prompt", "Segoe UI", sans-serif;
        }
        .page-header {
            border-radius: 1.5rem;
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.95), rgba(111, 66, 193, 0.92));
            color: #fff;
            padding: 2.5rem;
            box-shadow: 0 20px 45px rgba(13, 110, 253, 0.2);
        }
        .content-card {
            margin-top: -4rem;
            border-radius: 1.5rem;
            border: none;
            box-shadow: 0 16px 45px rgba(15, 23, 42, 0.1);
        }
        .content-card .card-body {
            padding-top: 2.5rem;
            
        }
        .form-label {
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.5rem;
        }
        .form-control, .form-select {
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        .alert {
            border-radius: 0.5rem;
            border: none;
        }
    </style>
</head>
<body>
    <div class="page-loader"></div>
        <div class="content-wrapper on-font-primary">
        <!-- Body -->
            <?=App::menus($index)?>

    <div class="container py-5">
        <div class="page-header mb-5">
            <h1 class="display-6 mb-2">➕ เพิ่มกิจกรรมใหม่</h1>
            <p class="mb-0 opacity-75">กรอกข้อมูลกิจกรรมที่ต้องการสร้าง</p>
        </div>
        
        <div class="card content-card mt-3">
            <div class="card-body p-4 pt-5">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <form method="GET" class="mt-4">
                    <div class="mb-3">
                        <label class="form-label">ชื่อกิจกรรม <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= isset($_GET['name']) ? htmlspecialchars($_GET['name']) : '' ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วันที่เริ่มต้น <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="text" name="start_date" id="start_date" class="form-control" placeholder="dd/mm/yyyy" value="<?= isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : '' ?>" required autocomplete="off">
                                <i class="bi bi-calendar position-absolute" style="right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #6c757d;"></i>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วันที่สิ้นสุด <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="text" name="end_date" id="end_date" class="form-control" placeholder="dd/mm/yyyy" value="<?= isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : '' ?>" required autocomplete="off">
                                <i class="bi bi-calendar position-absolute" style="right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #6c757d;"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ประเภทผู้เข้าร่วม</label>
                        <select name="participant_type" class="form-select">
                            <option value="ALL" <?= (isset($_GET['participant_type']) && $_GET['participant_type'] === 'ALL') ? 'selected' : '' ?>>ทุกคน</option>
                            <option value="LIST" <?= (isset($_GET['participant_type']) && $_GET['participant_type'] === 'LIST') ? 'selected' : '' ?>>เฉพาะผู้ที่มีรายชื่อ</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">สถานะ</label>
                        <select name="status" class="form-select">
                            <option value="0" <?= (isset($_GET['status']) && $_GET['status'] == 0) ? 'selected' : '' ?>>ร่าง</option>
                            <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == 1) ? 'selected' : '' ?>>เปิดการเข้าร่วม</option>
                            <option value="2" <?= (isset($_GET['status']) && $_GET['status'] == 2) ? 'selected' : '' ?>>ปิดการเข้าร่วม</option>
                            <option value="3" <?= (isset($_GET['status']) && $_GET['status'] == 3) ? 'selected' : '' ?>>ยกเลิก</option>
                        </select>
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <a href="index.php"><button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-check-circle me-2"></i>บันทึก
                        </button></a>
                        <a href="index.php" class="btn btn-secondary px-4">
                            <i class="bi bi-arrow-left me-2"></i>กลับ
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script>
        // Helper function to format date for display
        function formatDateForDisplay(dateStr) {
            if (!dateStr) return '';
            // If already in Y-m-d format, convert to d/m/Y
            if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) {
                const parts = dateStr.split('-');
                return parts[2] + '/' + parts[1] + '/' + parts[0];
            }
            return dateStr;
        }

        // Initialize date pickers
        let startDatePicker, endDatePicker;
        
        document.addEventListener('DOMContentLoaded', function() {
            const startInput = document.getElementById('start_date');
            const endInput = document.getElementById('end_date');
            
            // Format initial values for display
            if (startInput.value) {
                startInput.value = formatDateForDisplay(startInput.value);
            }
            if (endInput.value) {
                endInput.value = formatDateForDisplay(endInput.value);
            }
            
            startDatePicker = flatpickr("#start_date", {
                dateFormat: "d/m/Y",
                altInput: false,
                placeholder: "dd/mm/yyyy",
                allowInput: true,
                parseDate: function(datestr, format) {
                    // Try to parse d/m/Y format (day/month/year)
                    const parts = datestr.split('/');
                    if (parts.length === 3) {
                        const day = parseInt(parts[0], 10);
                        const month = parseInt(parts[1], 10);
                        const year = parseInt(parts[2], 10);
                        if (month >= 1 && month <= 12 && day >= 1 && day <= 31 && year >= 1900) {
                            return new Date(year, month - 1, day);
                        }
                    }
                    return null;
                },
                onChange: function(selectedDates, dateStr, instance) {
                    // Update hidden value to Y-m-d format
                    if (selectedDates.length > 0) {
                        const formatted = selectedDates[0].toISOString().split('T')[0];
                        instance.input.setAttribute('data-date-value', formatted);
                        if (endDatePicker) {
                            endDatePicker.set('minDate', selectedDates[0]);
                        }
                    }
                }
            });

            endDatePicker = flatpickr("#end_date", {
                dateFormat: "d/m/Y",
                altInput: false,
                placeholder: "dd/mm/yyyy",
                allowInput: true,
                parseDate: function(datestr, format) {
                    // Try to parse d/m/Y format (day/month/year)
                    const parts = datestr.split('/');
                    if (parts.length === 3) {
                        const day = parseInt(parts[0], 10);
                        const month = parseInt(parts[1], 10);
                        const year = parseInt(parts[2], 10);
                        if (month >= 1 && month <= 12 && day >= 1 && day <= 31 && year >= 1900) {
                            return new Date(year, month - 1, day);
                        }
                    }
                    return null;
                },
                onChange: function(selectedDates, dateStr, instance) {
                    // Update hidden value to Y-m-d format
                    if (selectedDates.length > 0) {
                        const formatted = selectedDates[0].toISOString().split('T')[0];
                        instance.input.setAttribute('data-date-value', formatted);
                    }
                }
            });
            
            if (startDatePicker.selectedDates.length > 0) {
                endDatePicker.set('minDate', startDatePicker.selectedDates[0]);
            }
        });

        // Form validation and conversion
        document.querySelector('form').addEventListener('submit', function(e) {
            const startInput = document.getElementById('start_date');
            const endInput = document.getElementById('end_date');
            
            // Convert display format to Y-m-d for submission
            if (startInput.value) {
                const startValue = startInput.getAttribute('data-date-value');
                if (startValue) {
                    startInput.value = startValue;
                } else if (startDatePicker && startDatePicker.selectedDates.length > 0) {
                    startInput.value = startDatePicker.selectedDates[0].toISOString().split('T')[0];
                } else {
                    // Try to parse d/m/Y format (day/month/year)
                    const parts = startInput.value.split('/');
                    if (parts.length === 3) {
                        startInput.value = parts[2] + '-' + parts[1].padStart(2, '0') + '-' + parts[0].padStart(2, '0');
                    }
                }
            }
            
            if (endInput.value) {
                const endValue = endInput.getAttribute('data-date-value');
                if (endValue) {
                    endInput.value = endValue;
                } else if (endDatePicker && endDatePicker.selectedDates.length > 0) {
                    endInput.value = endDatePicker.selectedDates[0].toISOString().split('T')[0];
                } else {
                    // Try to parse d/m/Y format (day/month/year)
                    const parts = endInput.value.split('/');
                    if (parts.length === 3) {
                        endInput.value = parts[2] + '-' + parts[1].padStart(2, '0') + '-' + parts[0].padStart(2, '0');
                    }
                }
            }
            
            // Validate dates
            if (startInput.value && endInput.value) {
                const start = new Date(startInput.value);
                const end = new Date(endInput.value);
                
                if (isNaN(start.getTime()) || isNaN(end.getTime())) {
                    e.preventDefault();
                    alert('รูปแบบวันที่ไม่ถูกต้อง กรุณาใช้รูปแบบ dd/mm/yyyy');
                    return false;
                }
                
                if (start > end) {
                    e.preventDefault();
                    alert('วันที่เริ่มต้นต้องไม่เกินวันที่สิ้นสุด');
                    return false;
                }
            }
        });
    </script>
</body>
</html>

<?=App::footer($index)?>