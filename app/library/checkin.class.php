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
}
