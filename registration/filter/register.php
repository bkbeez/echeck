<?php
    if(!isset($index['page'])||$index['page']!='registration'){ header("location:".((isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']==443)?'https://':'http://').$_SERVER["HTTP_HOST"]); exit(); } 
?>
<?php $index['page'] = 'registration'; ?>
<?php
$form = APP_PATH.'/registration';
$events_id = isset($_GET['events_id']) ? $_GET['events_id'] : (isset($_POST['events_id']) ? $_POST['events_id'] : '');
$type = isset($_GET['type']) ? $_GET['type'] : (isset($_POST['type']) ? $_POST['type'] : '');
?>
<?php
if($_SERVER['REQUEST_METHOD'] == 'GET' && $events_id && $type){
    include(APP_HEADER);
    echo '<section class="wrapper"><div class="container"><div class="row justify-content-center"><div class="col-md-8">';
    echo '<div id="registerModal" class="modal fade show" style="display:block;" data-bs-backdrop="static" data-bs-keyboard="false">';
}else{
    echo '<div class="modal-dialog">';
}
?>
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">ลงทะเบียนกิจกรรม</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
        <div class="modal-body">
            <form id="registerForm">
            <input type="hidden" name="events_id" value="<?=$events_id?>">
            <input type="hidden" name="type" value="<?=$type?>">
                <div class="row">
                    <?php if($type == 'student'){ ?>
                        <div class="col-md-12 mb-3">
                            <label>รหัสนักศึกษา</label>
                            <input type="text" name="student_id" class="form-control" required>
                        </div>
                    <?php } ?>
                        <div class="col-md-6 mb-3">
                            <label>คำนำหน้า</label>
                            <select name="prefix" class="form-control" required>
                                <option value="">เลือก</option>
                                <option value="นาย">นาย</option>
                                <option value="นาง">นาง</option>
                                <option value="นางสาว">นางสาว</option>
                                <option value="ดร.">ดร.</option>
                                <option value="ผศ.">ผศ.</option>
                                <option value="รศ.">รศ.</option>
                                <option value="ศ.">ศ.</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>ชื่อ</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>นามสกุล</label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                            <div class="col-md-6 mb-3">
                            <label>อีเมล</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <?php if($type != 'student'){ ?>
                        <div class="col-md-12 mb-3">
                            <label>องค์กร/หน่วยงาน</label>
                            <input type="text" name="organization" class="form-control">
                        </div>
                    <?php } ?>
                </div>
            </form>
        </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
        <button type="button" class="btn btn-primary" onclick="submit_register()">ลงทะเบียน</button>
    </div>
</div>
<?php
if($_SERVER['REQUEST_METHOD'] == 'GET' && $events_id && $type){
    echo '</div></div></div></div></section>';
    include(APP_FOOTER);
}else{
    echo '</div>';
}
?>
<script type="text/javascript">
function submit_register(){
    $.post("<?=$form?>/scripts/register.php", $("#registerForm").serialize(), function(data){
        try {
            var response = JSON.parse(data);
            if(response.status == 'success'){
                alert(response.title);
                window.location.href = "<?=$form?>";
            } else {
                alert(response.title);
            }
        } catch(e) {
            alert("เกิดข้อผิดพลาด");
        }
    });
}
</script>