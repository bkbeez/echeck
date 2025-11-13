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
        $sql = "INSERT INTO `participants`
                (`participant_id`, `events_id`, `type`, `prefix`, `firstname`, `lastname`, `email`, `organization`, `status`, `note`)
                VALUES (:participant_id, :events_id, :type, :prefix, :firstname, :lastname, :email, :organization, :status, :note);";
        return DB::createLastInsertId($sql, $data);
    }

    /**
     * Update participant information
     * @param int $participantId
     * @param array $data
     * @return bool
     */
    public static function updateParticipant(int $participantId, array $data)
    {
        $sql = "UPDATE `participants`
                SET `events_id` = :events_id,
                    `type` = :type,
                    `prefix` = :prefix,
                    `firstname` = :firstname,
                    `lastname` = :lastname,
                    `email` = :email,
                    `organization` = :organization,
                    `status` = :status,
                    `note` = :note
                WHERE `id` = :participant_id
                LIMIT 1;";
        $parameters = array_merge($data, [
            'participant_id' => $participantId,
        ]);

        return DB::update($sql, $parameters);
    }

    /**
     * Delete participant
     * @param int $participantId
     * @return bool
     */
    public static function deleteParticipant(int $participantId)
    {
        $sql = "DELETE FROM `participants` WHERE `id` = :participant_id LIMIT 1;";
        return DB::delete($sql, [
            'participant_id' => $participantId,
        ]);
    }

    /**
     * Get participant by ID
     * @param int $participantId
     * @return array|null
     */
    public static function getParticipant(int $participantId)
    {
        $sql = "SELECT p.*, e.events_name
                FROM `participants` p
                LEFT JOIN `events` e ON e.events_id = p.events_id
                WHERE p.`id` = :participant_id
                LIMIT 1;";
        return DB::one($sql, [
            'participant_id' => $participantId,
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
                ORDER BY p.`joined_at` DESC, p.`id` DESC;";
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
                ORDER BY p.`joined_at` DESC, p.`id` DESC;";
        return DB::query($sql, [
            'events_id' => $eventsId,
        ]);
    }

    /**
     * Find participant by participant_id
     * @param string $participantId
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

