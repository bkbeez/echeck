<?php
/**
 * Event Model
 */
class Event extends DB
{
    /**
     * Create new event
     * @param array $data
     * @return bool|int
     */
    public static function createEvent(array $data)
    {
        // เนื่องจาก database schema ไม่มี user_id column จึงไม่ใส่ user_id ใน INSERT
        // เติมค่า default ให้ครบทุก placeholder เพื่อหลีกเลี่ยง PDO error HY093
        $defaults = [
            'participant_type' => 'ALL',
            'status' => '0=ร่าง',
        ];
        $requiredKeys = ['events_id', 'events_name', 'start_date', 'end_date', 'participant_type', 'status'];
        $params = array_merge($defaults, $data);
        // เก็บเฉพาะ key ที่จำเป็นตาม placeholder
        $params = array_intersect_key($params, array_flip($requiredKeys));

        // map สถานะที่เป็นตัวเลข -> ค่า enum ใน schema
        $statusMap = [
            '0' => '0=ร่าง',
            '1' => '1=เปิดการเข้าร่วม',
            '2' => '2=ปิดการเข้าร่วม',
            '3' => '3=ยกเลิก',
        ];
        if (isset($params['status'])) {
            $params['status'] = $statusMap[(string)$params['status']] ?? '0=ร่าง';
        }

        $sql = "INSERT INTO `events`
                (`events_id`, `events_name`, `start_date`, `end_date`, `participant_type`, `status`)
                VALUES (:events_id, :events_name, :start_date, :end_date, :participant_type, :status);";
        return DB::create($sql, $params);
    }

    /**
     * Update event information for user
     * @param int $eventId
     * @param string $userid
     * @param array $data
     * @return bool
     */
    public static function updateEvent(string $eventId, string $userid, array $data)
    {
        // เนื่องจาก database schema ไม่มี user_id และ id column จึงใช้ events_id แทน
        // เตรียม parameters โดยใช้ events_id จาก parameter และ merge กับ data
        // map สถานะ (รับได้ทั้งตัวเลขและค่า enum ที่มีอยู่แล้ว)
        $statusMap = [
            '0' => '0=ร่าง',
            '1' => '1=เปิดการเข้าร่วม',
            '2' => '2=ปิดการเข้าร่วม',
            '3' => '3=ยกเลิก',
        ];
        $rawStatus = $data['status'] ?? null;
        if (isset($statusMap[(string)$rawStatus])) {
            $normalizedStatus = $statusMap[(string)$rawStatus];
        } elseif (in_array($rawStatus, $statusMap, true)) {
            // กรณีส่งมาเป็นข้อความ enum อยู่แล้ว
            $normalizedStatus = $rawStatus;
        } else {
            // ถ้าไม่ส่งมา หรือไม่ตรง map ให้คงค่าสถานะเดิม
            $existing = self::findByid($eventId);
            $normalizedStatus = $existing['status'] ?? '0=ร่าง';
        }

        $params = [
            'events_id' => $eventId,
            'events_name' => $data['events_name'] ?? '',
            'start_date' => $data['start_date'] ?? '',
            'end_date' => $data['end_date'] ?? '',
            'participant_type' => $data['participant_type'] ?? 'ALL',
            'status' => $normalizedStatus,
        ];

        $sql = "UPDATE `events`
                SET
                    `events_name` = :events_name,
                    `start_date` = :start_date,
                    `end_date` = :end_date,
                    `participant_type` = :participant_type,
                    `status` = :status
                WHERE `events_id` = :events_id
                LIMIT 1;";
        return DB::update($sql, $params);
    }

    /**
     * Delete event (user only)
     * @param string $eventId
     * @param string $userid
     * @return bool
     */
    public static function deleteEvent(string $eventId, string $userid)
    {
        // เนื่องจาก database schema ไม่มี user_id และ id column จึงใช้ events_id แทน
        $sql = "DELETE FROM `events` WHERE `events_id` = :events_id LIMIT 1;";
        return DB::delete($sql, [
            'events_id' => $eventId,
        ]);
    }

    /**
     * Fetch event for user
     * @param string $eventId
     * @param string $userid
     * @return array|null
     */
    public static function getOwnedEvent(string $eventId, string $userid)
    {
        // เนื่องจาก database schema ไม่มี user_id column จึงดึง event ตาม events_id เท่านั้น
        $sql = "SELECT *
                FROM `events`
                WHERE `events_id` = :events_id
                LIMIT 1;";
        return DB::one($sql, [
            'events_id' => $eventId,
        ], ['ignore_error' => true]);
    }

    /**
     * Fetch event accessible by user (user or shared)
     * @param int $eventId
     * @param string $id
     * @return array|null
     */
    public static function getAccessibleEvent(int $eventId, string $id)
    {
        // เนื่องจาก database schema ไม่มี user_id และ events_share table
        // จึงดึง event ตาม events_id เท่านั้น
        $sql = "SELECT e.*
                FROM `events` e
                WHERE e.`events_id` = :events_id
                LIMIT 1;";
        return DB::one($sql, [
            'events_id' => $eventId,
        ], ['ignore_error' => true]);
    }

    /**
     * List events for user (user + shared)
     * @param string $id
     * @return array
     */
    public static function listForUser(string $id)
    {
        // เนื่องจาก database schema ไม่มี user_id column ใน events table และไม่มี events_share table
        // จึงดึง events ทั้งหมดและกำหนด is_user = 1 สำหรับทุก events
        // ถ้าต้องการใช้ sharing feature ต้องเพิ่ม table และ column ที่จำเป็นใน database
        $sql = "SELECT e.*, 1 AS is_user
                FROM `events` e
                ORDER BY e.`start_date` DESC, e.`events_id` DESC;";
        
        $results = DB::query($sql, [], ['ignore_error' => true]);
        
        if (!is_array($results)) {
            return [];
        }

        // แปลง array เป็น associative array โดยใช้ events_id เป็น key
        $finalResults = [];
        foreach ($results as $row) {
            $eventId = $row['events_id'] ?? $row['id'] ?? null;
            if ($eventId !== null) {
                $finalResults[$eventId] = $row;
            }
        }

        // เรียงลำดับอีกครั้งเพื่อให้แน่ใจว่าถูกต้อง
        usort($finalResults, static function ($left, $right) {
            $leftDate = $left['start_date'] ?? '';
            $rightDate = $right['start_date'] ?? '';
            if ($leftDate === $rightDate) {
                $leftId = $left['events_id'] ?? $left['id'] ?? '';
                $rightId = $right['events_id'] ?? $right['id'] ?? '';
                return $rightId <=> $leftId;
            }
            return strcmp($rightDate, $leftDate);
        });

        return $finalResults;
    }

    /**
     * List shares for event user
     * @param int $eventId
     * @param string $userid
     * @return array
     */
    public static function listShares(int $eventId, string $userid)
    {
        // เนื่องจาก database schema ไม่มี events_share table จึง return empty array
        return [];
    }

    /**
     * Add share (user only)
     * @param int $eventId
     * @param string $userid
     * @param string $sharedid
     * @return bool
     */
    public static function addShare(int $eventId, string $userid, string $sharedid)
    {
        // เนื่องจาก database schema ไม่มี events_share table จึง return false
        return false;
    }

    /**
     * Remove share (user only)
     * @param int $shareId
     * @param int $eventId
     * @param string $userid
     * @return bool
     */
    public static function removeShare(int $shareId, int $eventId, string $userid)
    {
        // เนื่องจาก database schema ไม่มี events_share table จึง return false
        return false;
    }

    /**
     * Check if share already exists
     * @param int $eventId
     * @param string $sharedid
     * @return bool
     */
    public static function shareExists(int $eventId, string $sharedid)
    {
        // เนื่องจาก database schema ไม่มี events_share table จึง return false
        return false;
    }

    /**
     * Find event by events id
     * @param string $eventsid
     * @return array|null
     */
    public static function findByid(string $eventsid)
    {
        $sql = "SELECT *
                FROM `events`
                WHERE `events_id` = :events_id
                LIMIT 1;";

        return DB::one($sql, [
            'events_id' => $eventsid,
        ]);
    }

    /**
     * List events that are open for participation
     * @return array
     */
    public static function listOpenEvents()
    {
        $sql = "SELECT * FROM `events` 
                WHERE `status` = :status 
                ORDER BY `start_date` DESC, `events_id` DESC";
        
        $results = DB::query($sql, [
            'status' => '1=เปิดการเข้าร่วม',
        ], ['ignore_error' => true]);
        
        if (!is_array($results)) {
            return [];
        }
        
        return $results;
    }
}


