<?php
/**
 * Participant Model
 */
class Participant extends DB
{
    /**
     * Create new participant
     * @param array $data
     * @return bool|int
     */
    public static function createParticipant(array $data)
    {
        // สร้าง participant_id อัตโนมัติถ้าไม่มีหรือเป็น null
        if (empty($data['participant_id'])) {
            $data['participant_id'] = 'PART-' . date('YmdHis') . '-' . substr(md5(uniqid(rand(), true)), 0, 8);
        }
        
        $sql = "INSERT INTO `participants`
                (`participant_id`, `events_id`, `type`, `prefix`, `firstname`, `lastname`, `email`, `organization`, `status`, `note`)
                VALUES (:participant_id, :events_id, :type, :prefix, :firstname, :lastname, :email, :organization, :status, :note);";
        
        try {
            $result = DB::createLastInsertId($sql, $data);
            if ($result && $result > 0) {
                return $result;
            }
            return false;
        } catch (Exception $e) {
            error_log('Participant::createParticipant error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update participant information
     * @param int $id - ID (primary key) ของ participant
     * @param array $data
     * @return bool
     */
    public static function updateParticipant(int $id, array $data)
    {
        // สร้าง participant_id อัตโนมัติถ้าไม่มีใน data
        if (empty($data['participant_id'])) {
            $data['participant_id'] = 'PART-' . date('YmdHis') . '-' . substr(md5(uniqid(rand(), true)), 0, 8);
        }
        
        $sql = "UPDATE `participants`
                SET
                    `participant_id` = :participant_id,
                    `events_id` = :events_id,
                    `type` = :type,
                    `prefix` = :prefix,
                    `firstname` = :firstname,
                    `lastname` = :lastname,
                    `email` = :email,
                    `organization` = :organization,
                    `status` = :status,
                    `note` = :note
                WHERE `id` = :id
                LIMIT 1;";
        $parameters = array_merge($data, [
            'id' => $id,
        ]);

        return DB::update($sql, $parameters);
    }

    /**
     * Delete participant
     * @param int $id - ID (primary key) ของ participant
     * @return bool
     */
    public static function deleteParticipant(int $id)
    {
        $sql = "DELETE FROM `participants` WHERE `id` = :id LIMIT 1;";
        try {
            $result = DB::delete($sql, [
                'id' => $id,
            ]);
            return $result;
        } catch (Exception $e) {
            error_log('Participant::deleteParticipant error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get participant by ID
     * @param int $id - ID (primary key) ของ participant
     * @return array|null
     */
    public static function getParticipant(int $id)
    {
        $sql = "SELECT p.*, e.events_name
                FROM `participants` p
                LEFT JOIN `events` e ON e.events_id = p.events_id
                WHERE p.`id` = :id
                LIMIT 1;";
        return DB::one($sql, [
            'id' => $id,
        ]);
    }

    /**
     * List all participants with event information
     * @return array
     */
    public static function listAll()
    {
        $sql = "SELECT p.*, e.events_name
                FROM `participants` p
                LEFT JOIN `events` e ON e.events_id = p.events_id
                ORDER BY p.`create_at` DESC, p.`id` DESC;";
        return DB::query($sql);
    }

    /**
     * List participants by event ID
     * @param string $eventsId
     * @return array
     */
    public static function listByEvent(string $eventsId)
    {
        $sql = "SELECT p.*, e.events_name
                FROM `participants` p
                LEFT JOIN `events` e ON e.events_id = p.events_id
                WHERE p.`events_id` = :events_id
                ORDER BY (p.`create_at` IS NULL), p.`create_at` DESC, p.`id` DESC;";
        return DB::query($sql, [
            'events_id' => $eventsId,
        ]);
    }

    /**
     * Find participant by participant_id (string)
     * @param string $participantId - participant_id (VARCHAR) เช่น 'PART-20240101120000-abc12345'
     * @return array|null
     */
    public static function findByParticipantId(string $participantId)
    {
        $sql = "SELECT p.*, e.events_name
                FROM `participants` p
                LEFT JOIN `events` e ON e.events_id = p.events_id
                WHERE p.`participant_id` = :participant_id
                LIMIT 1;";
        return DB::one($sql, [
            'participant_id' => $participantId,
        ]);
    }
}

