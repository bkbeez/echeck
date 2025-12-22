<?php

class Checkin {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * เช็คอิน
     */
    public function checkin($data) {

        // ตรวจข้อมูลจำเป็น
        $required = ['event_id','student_id','prefix','firstname','lastname','email','organization'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return [
                    'status' => false,
                    'message' => "ข้อมูล $field ไม่ครบ"
                ];
            }
        }

        // ป้องกันเช็คอินซ้ำ
        $stmt = $this->db->prepare("
            SELECT id FROM checkins
            WHERE event_id=? AND student_id=?
        ");
        $stmt->bind_param("is",$data['event_id'],$data['student_id']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            return [
                'status' => false,
                'message' => 'เช็คอินไปแล้ว'
            ];
        }

        // บันทึกข้อมูล
        $stmt = $this->db->prepare("
            INSERT INTO checkin
            (event_id,prefix,firstname,lastname,organization,email,student_id,checkin_time)
            VALUES (?,?,?,?,?,?,?,NOW())
        ");
        $stmt->bind_param(
            "issssss",
            $data['event_id'],
            $data['prefix'],
            $data['firstname'],
            $data['lastname'],
            $data['organization'],
            $data['email'],
            $data['student_id']
        );
        $stmt->execute();

        return [
            'status' => true,
            'message' => 'เช็คอินสำเร็จ'
        ];
    }

    /**
     * ดึงรายชื่อเช็คอินตามกิจกรรม
     */
    public function getByEvent($event_id) {
        $stmt = $this->db->prepare("
            SELECT * FROM checkin
            WHERE event_id=?
            ORDER BY checkin_time DESC
        ");
        $stmt->bind_param("i",$event_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * นับจำนวนเช็คอินตามกิจกรรม
     */
    public function getCountByEvent($event_id) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS count FROM checkin
            WHERE event_id=?
        ");
        $stmt->bind_param("i",$event_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] ?? 0;
    }

    /**
     * ปิดการเชื่อมต่อฐานข้อมูลเมื่อทำงานเสร็จ
     */
    public function __destruct() {
        $this->db->close();
    }

    /**
     * ลบข้อมูลเช็คอินเก่าที่เกินจำนวนวันที่กำหนด
     */
    public function clearOldCheckins($days) {
        $stmt = $this->db->prepare("
            DELETE FROM checkin
            WHERE checkin_time < NOW() - INTERVAL ? DAY
        ");
        $stmt->bind_param("i",$days);
        $stmt->execute();
    }

    /**
     * ดึงข้อมูลเช็คอินตาม ID
     */
    public function getCheckin($id) {
        $stmt = $this->db->prepare("
            SELECT * FROM checkin
            WHERE id=?
        ");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * ลบข้อมูลเช็คอินตาม ID
     */

    public function deleteCheckin($id) {
        $stmt = $this->db->prepare("
            DELETE FROM checkin
            WHERE id=?
        ");
        $stmt->bind_param("i",$id);
        $stmt->execute();
    }

    /**
     * อัพเดตข้อมูลเช็คอินตาม ID
     */
    public function updateCheckin($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE checkin
            SET prefix=?, firstname=?, lastname=?, organization=?, email=?, student_id=?
            WHERE id=?
        ");
        $stmt->bind_param(
            "ssssssi",
            $data['prefix'],
            $data['firstname'],
            $data['lastname'],
            $data['organization'],
            $data['email'],
            $data['student_id'],
            $id
        );
        $stmt->execute();
    }

    /**
     * นับจำนวนเช็คอินทั้งหมด
     */
    public function getTotalCheckins() {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total FROM checkin
        ");
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    /**
     * ดึงรายชื่อเช็คอินทั้งหมด
     */
    public function getAllCheckins() {
        $stmt = $this->db->prepare("
            SELECT * FROM checkin
            ORDER BY checkin_time DESC
        ");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * ตรวจสอบว่าผู้เข้าร่วมกิจกรรมได้เช็คอินหรือไม่
     */
    public function isCheckedIn($event_id, $student_id) {
        $stmt = $this->db->prepare("
            SELECT id FROM checkin
            WHERE event_id=? AND student_id=?
        ");
        $stmt->bind_param("is",$event_id,$student_id);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * นับจำนวนเช็คอินตามช่วงเวลา
     */
    public function getCountByDateRange($start_date, $end_date) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS count FROM checkin
            WHERE checkin_time BETWEEN ? AND ?
        ");
        $stmt->bind_param("ss",$start_date,$end_date);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] ?? 0;
    }

    /**
     * ลบข้อมูลเช็คอินทั้งหมด
     */
    public function clearAllCheckins() {
        $stmt = $this->db->prepare("
            DELETE FROM checkin
        ");
        $stmt->execute();
    }

    /**
     * ดึงรายชื่อเช็คอินตามองค์กร
     */

    public function getByOrganization($organization) {
        $stmt = $this->db->prepare("
            SELECT * FROM checkin
            WHERE organization=?
            ORDER BY checkin_time DESC
        ");
        $stmt->bind_param("s",$organization);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * ดึงรายชื่อเช็คอินตามอีเมล
     */
    public function getByEmail($email) {
        $stmt = $this->db->prepare("
            SELECT * FROM checkin
            WHERE email=?
            ORDER BY checkin_time DESC
        ");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * ดึงรายชื่อเช็คอินตามชื่อ
     */
    public function getByName($name) {
        $stmt = $this->db->prepare("
            SELECT * FROM checkin
            WHERE firstname LIKE ? OR lastname LIKE ?
            ORDER BY checkin_time DESC
        ");
        $like_name = "%$name%";
        $stmt->bind_param("ss",$like_name,$like_name);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * ดึงรายชื่อเช็คอินตามวันที่เช็คอิน
     */
    public function getByCheckinDate($date) {
        $stmt = $this->db->prepare("
            SELECT * FROM checkin
            WHERE DATE(checkin_time)=?
            ORDER BY checkin_time DESC
        ");
        $stmt->bind_param("s",$date);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * ดึงรายชื่อเช็คอินตามรหัสนักศึกษา
     */
    public function getByStudentId($student_id) {
        $stmt = $this->db->prepare("
            SELECT * FROM checkin
            WHERE student_id=?
            ORDER BY checkin_time DESC
        ");
        $stmt->bind_param("s",$student_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * อัพเดตเวลาการเช็คอิน
     */
    public function updateCheckinTime($id, $new_time) {
        $stmt = $this->db->prepare("
            UPDATE checkin
            SET checkin_time=?
            WHERE id=?
        ");
        $stmt->bind_param("si",$new_time,$id);
        $stmt->execute();
    }

    /**
     * นับจำนวนเช็คอินตาม prefix
     */
    public function getCountByPrefix($prefix) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS count FROM checkin
            WHERE prefix=?
        ");
        $stmt->bind_param("s",$prefix);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] ?? 0;
    }

    /**
     * นับจำนวนเช็คอินตาม organization
     */
    public function getCountByOrganization($organization) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS count FROM checkin
            WHERE organization=?
        ");
        $stmt->bind_param("s",$organization);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] ?? 0;
    }

    /**
     * นับจำนวนเช็คอินตาม email
     */
    public function getCountByEmail($email) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS count FROM checkin
            WHERE email=?
        ");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] ?? 0;
    }

    /**
     * นับจำนวนเช็คอินตามชื่อ
     */
    public function getCountByName($name) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS count FROM checkin
            WHERE firstname LIKE ? OR lastname LIKE ?
        ");
        $like_name = "%$name%";
        $stmt->bind_param("ss",$like_name,$like_name);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] ?? 0;
    }

    /**
     * นับจำนวนเช็คอินตามวันที่เช็คอิน
     */
    public function getCountByCheckinDate($date) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS count FROM checkin
            WHERE DATE(checkin_time)=?
        ");
        $stmt->bind_param("s",$date);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] ?? 0;
    }

    /**
     * นับจำนวนเช็คอินตามรหัสนักศึกษา
     */
    public function getCountByStudentId($student_id) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS count FROM checkin
            WHERE student_id=?
        ");
        $stmt->bind_param("s",$student_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] ?? 0;
    }

    /**
     * ดึงรายชื่อเช็คอินตาม prefix
     */
    public function getByPrefix($prefix) {
        $stmt = $this->db->prepare("
            SELECT * FROM checkin
            WHERE prefix=?
            ORDER BY checkin_time DESC
        ");
        $stmt->bind_param("s",$prefix);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * ดึงรายชื่อเช็คอินตามช่วงเวลาเช็คอิน
     */
    public function getByCheckinTimeRange($start_time, $end_time) {
        $stmt = $this->db->prepare("
            SELECT * FROM checkin
            WHERE checkin_time BETWEEN ? AND ?
            ORDER BY checkin_time DESC
        ");
        $stmt->bind_param("ss",$start_time,$end_time);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
