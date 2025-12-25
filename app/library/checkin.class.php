<?php

class Checkin {

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
        $existing = DB::one("
            SELECT id FROM checkin
            WHERE event_id=:event_id AND student_id=:student_id
        ", [
            ':event_id' => $data['event_id'],
            ':student_id' => $data['student_id']
        ]);
        if ($existing) {
            return [
                'status' => false,
                'message' => 'เช็คอินไปแล้ว'
            ];
        }

        // บันทึกข้อมูล
        $result = DB::create("
            INSERT INTO checkin
            (event_id,prefix,firstname,lastname,organization,email,student_id,checkin_time)
            VALUES (:event_id,:prefix,:firstname,:lastname,:organization,:email,:student_id,NOW())
        ", [
            ':event_id' => $data['event_id'],
            ':prefix' => $data['prefix'],
            ':firstname' => $data['firstname'],
            ':lastname' => $data['lastname'],
            ':organization' => $data['organization'],
            ':email' => $data['email'],
            ':student_id' => $data['student_id']
        ]);

        if ($result) {
            return [
                'status' => true,
                'message' => 'เช็คอินสำเร็จ'
            ];
        } else {
            return [
                'status' => false,
                'message' => 'เกิดข้อผิดพลาดในการเช็คอิน'
            ];
        }
    }

    /**
     * ดึงรายชื่อเช็คอินตามกิจกรรม
     */
    public function getByEvent($event_id) {
        return DB::query("
            SELECT * FROM checkin
            WHERE event_id=:event_id
            ORDER BY checkin_time DESC
        ", [':event_id' => $event_id]);
    }

    /**
     * นับจำนวนเช็คอินตามกิจกรรม
     */
    public function getCountByEvent($event_id) {
        $result = DB::one("
            SELECT COUNT(*) AS count FROM checkin
            WHERE event_id=:event_id
        ", [':event_id' => $event_id]);
        return $result['count'] ?? 0;
    }

    /**
     * ลบข้อมูลเช็คอินเก่าที่เกินจำนวนวันที่กำหนด
     */
    public function clearOldCheckins($days) {
        DB::update("
            DELETE FROM checkin
            WHERE checkin_time < NOW() - INTERVAL :days DAY
        ", [':days' => $days]);
    }

    /**
     * ดึงข้อมูลเช็คอินตาม ID
     */
    public function getCheckin($id) {
        return DB::one("
            SELECT * FROM checkin
            WHERE id=:id
        ", [':id' => $id]);
    }

    /**
     * ลบข้อมูลเช็คอินตาม ID
     */

    public function deleteCheckin($id) {
        DB::update("
            DELETE FROM checkin
            WHERE id=:id
        ", [':id' => $id]);
    }

    /**
     * อัพเดตข้อมูลเช็คอินตาม ID
     */
    public function updateCheckin($id, $data) {
        DB::update("
            UPDATE checkin
            SET prefix=:prefix, firstname=:firstname, lastname=:lastname, organization=:organization, email=:email, student_id=:student_id
            WHERE id=:id
        ", [
            ':prefix' => $data['prefix'],
            ':firstname' => $data['firstname'],
            ':lastname' => $data['lastname'],
            ':organization' => $data['organization'],
            ':email' => $data['email'],
            ':student_id' => $data['student_id'],
            ':id' => $id
        ]);
    }

    /**
     * นับจำนวนเช็คอินทั้งหมด
     */
    public function getTotalCheckins() {
        $result = DB::one("
            SELECT COUNT(*) AS total FROM checkin
        ");
        return $result['total'] ?? 0;
    }

    /**
     * ดึงรายชื่อเช็คอินทั้งหมด
     */
    public function getAllCheckins() {
        return DB::query("
            SELECT * FROM checkin
            ORDER BY checkin_time DESC
        ");
    }

    /**
     * ตรวจสอบว่าผู้เข้าร่วมกิจกรรมได้เช็คอินหรือไม่
     */
    public function isCheckedIn($event_id, $student_id) {
        $result = DB::one("
            SELECT id FROM checkin
            WHERE event_id=:event_id AND student_id=:student_id
        ", [':event_id' => $event_id, ':student_id' => $student_id]);
        return $result ? true : false;
    }

    /**
     * นับจำนวนเช็คอินตามช่วงเวลา
     */
    public function getCountByDateRange($start_date, $end_date) {
        $result = DB::one("
            SELECT COUNT(*) AS count FROM checkin
            WHERE checkin_time BETWEEN :start_date AND :end_date
        ", [':start_date' => $start_date, ':end_date' => $end_date]);
        return $result['count'] ?? 0;
    }

    /**
     * ลบข้อมูลเช็คอินทั้งหมด
     */
    public function clearAllCheckins() {
        DB::update("
            DELETE FROM checkin
        ");
    }

    /**
     * ดึงรายชื่อเช็คอินตามองค์กร
     */

    public function getByOrganization($organization) {
        return DB::query("
            SELECT * FROM checkin
            WHERE organization=:organization
            ORDER BY checkin_time DESC
        ", [':organization' => $organization]);
    }

    /**
     * ดึงรายชื่อเช็คอินตามอีเมล
     */
    public function getByEmail($email) {
        return DB::query("
            SELECT * FROM checkin
            WHERE email=:email
            ORDER BY checkin_time DESC
        ", [':email' => $email]);
    }

    /**
     * ดึงรายชื่อเช็คอินตามชื่อ
     */
    public function getByName($name) {
        $like_name = "%$name%";
        return DB::query("
            SELECT * FROM checkin
            WHERE firstname LIKE :name OR lastname LIKE :name
            ORDER BY checkin_time DESC
        ", [':name' => $like_name]);
    }

    /**
     * ดึงรายชื่อเช็คอินตามวันที่เช็คอิน
     */
    public function getByCheckinDate($date) {
        return DB::query("
            SELECT * FROM checkin
            WHERE DATE(checkin_time)=:date
            ORDER BY checkin_time DESC
        ", [':date' => $date]);
    }

    /**
     * ดึงรายชื่อเช็คอินตามรหัสนักศึกษา
     */
    public function getByStudentId($student_id) {
        return DB::query("
            SELECT * FROM checkin
            WHERE student_id=:student_id
            ORDER BY checkin_time DESC
        ", [':student_id' => $student_id]);
    }

    /**
     * อัพเดตเวลาการเช็คอิน
     */
    public function updateCheckinTime($id, $new_time) {
        DB::update("
            UPDATE checkin
            SET checkin_time=:new_time
            WHERE id=:id
        ", [':new_time' => $new_time, ':id' => $id]);
    }

    /**
     * นับจำนวนเช็คอินตาม prefix
     */
    public function getCountByPrefix($prefix) {
        $result = DB::one("
            SELECT COUNT(*) AS count FROM checkin
            WHERE prefix=:prefix
        ", [':prefix' => $prefix]);
        return $result['count'] ?? 0;
    }

    /**
     * นับจำนวนเช็คอินตาม organization
     */
    public function getCountByOrganization($organization) {
        $result = DB::one("
            SELECT COUNT(*) AS count FROM checkin
            WHERE organization=:organization
        ", [':organization' => $organization]);
        return $result['count'] ?? 0;
    }

    /**
     * นับจำนวนเช็คอินตาม email
     */
    public function getCountByEmail($email) {
        $result = DB::one("
            SELECT COUNT(*) AS count FROM checkin
            WHERE email=:email
        ", [':email' => $email]);
        return $result['count'] ?? 0;
    }

    /**
     * นับจำนวนเช็คอินตามชื่อ
     */
    public function getCountByName($name) {
        $like_name = "%$name%";
        $result = DB::one("
            SELECT COUNT(*) AS count FROM checkin
            WHERE firstname LIKE :name OR lastname LIKE :name
        ", [':name' => $like_name]);
        return $result['count'] ?? 0;
    }

    /**
     * นับจำนวนเช็คอินตามวันที่เช็คอิน
     */
    public function getCountByCheckinDate($date) {
        $result = DB::one("
            SELECT COUNT(*) AS count FROM checkin
            WHERE DATE(checkin_time)=:date
        ", [':date' => $date]);
        return $result['count'] ?? 0;
    }
}
