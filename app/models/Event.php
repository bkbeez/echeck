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
        $sql = "INSERT INTO `events`
                (`user_id`, `events_id`, `events_name`, `start_date`, `end_date`, `participant_type`, `status`)
                VALUES (:user_id, :events_id, :events_name, :start_date, :end_date, :participant_type, :status);";
        return DB::createLastInsertId($sql, $data);
    }

    /**
     * Update event information for user
     * @param int $eventId
     * @param string $userid
     * @param array $data
     * @return bool
     */
    public static function updateEvent(int $eventId, string $userid, array $data)
    {
        $sql = "UPDATE `events`
                SET `events_id` = :events_id,
                    `events_name` = :events_name,
                    `start_date` = :start_date,
                    `end_date` = :end_date,
                    `participant_type` = :participant_type,
                    `status` = :status
                WHERE `id` = :events_id AND `user_id` = :user_id
                LIMIT 1;";
        $parameters = array_merge($data, [
            'events_id'    => $eventId,
            'user_id' => $userid,
        ]);

        return DB::update($sql, $parameters);
    }

    /**
     * Delete event (user only)
     * @param int $eventId
     * @param string $userid
     * @return bool
     */
    public static function deleteEvent(int $eventId, string $userid)
    {
        $sql = "DELETE FROM `events` WHERE `id` = :events_id AND `user_id` = :user_id LIMIT 1;";
        return DB::delete($sql, [
            'events_id'    => $eventId,
            'user_id' => $userid,
        ]);
    }

    /**
     * Fetch event for user
     * @param int $eventId
     * @param string $userid
     * @return array|null
     */
    public static function getOwnedEvent(int $eventId, string $userid)
    {
        $sql = "SELECT *
                FROM `events`
                WHERE `id` = :events_id AND `user_id` = :user_id
                LIMIT 1;";
        return DB::one($sql, [
            'events_id'    => $eventId,
            'user_id' => $userid,
        ]);
    }

    /**
     * Fetch event accessible by user (user or shared)
     * @param int $eventId
     * @param string $id
     * @return array|null
     */
    public static function getAccessibleEvent(int $eventId, string $id)
    {
        $sql = "SELECT DISTINCT e.*
                FROM `events` e
                LEFT JOIN `events_share` es
                    ON es.`events_id` = e.`id`
                WHERE e.`id` = :events_id
                    AND (e.`user_id` = :id OR es.`shared_id` = :id)
                LIMIT 1;";
        return DB::one($sql, [
            'events_id' => $eventId,
            'id'    => $id,
        ]);
    }

    /**
     * List events for user (user + shared)
     * @param string $id
     * @return array
     */
    public static function listForUser(string $id)
    {
        $owned = DB::query(
            "SELECT e.*, 1 AS is_user
                FROM `events` e
                WHERE e.`user_id` = :id
                ORDER BY e.`start_date` DESC, e.`id` DESC;",
            ['id' => $id]
        );

        $shared = DB::query(
            "SELECT e.*, 0 AS is_user
                FROM `events` e
                INNER JOIN `events_share` es ON es.`events_id` = e.`id`
                WHERE es.`shared_id` = :id
                ORDER BY e.`start_date` DESC, e.`id` DESC;",
            ['id' => $id]
        );

        $results = [];
        if (is_array($owned)) {
            foreach ($owned as $row) {
                $results[$row['id']] = $row;
            }
        }
        if (is_array($shared)) {
            foreach ($shared as $row) {
                if (!isset($results[$row['id']])) {
                    $results[$row['id']] = $row;
                }
            }
        }

        // Ensure consistent ordering (by start_date desc, id desc)
        usort($results, static function ($left, $right) {
            if ($left['start_date'] === $right['start_date']) {
                return $right['id'] <=> $left['id'];
            }
            return strcmp($right['start_date'], $left['start_date']);
        });

        return $results;
    }

    /**
     * List shares for event user
     * @param int $eventId
     * @param string $userid
     * @return array
     */
    public static function listShares(int $eventId, string $userid)
    {
        $event = self::getOwnedEvent($eventId, $userid);
        if (!$event) {
            return [];
        }

        return DB::query(
            "SELECT es.*
                FROM `events_share` es
                WHERE es.`events_id` = :events_id
                ORDER BY es.`shared_id` ASC;",
            ['events_id' => $eventId]
        );
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
        if (!self::getOwnedEvent($eventId, $userid)) {
            return false;
        }
        if ($sharedid === $userid) {
            return false;
        }

        $sql = "INSERT IGNORE INTO `events_share` (`events_id`, `shared_id`)
                VALUES (:events_id, :shared_id);";

        return DB::create($sql, [
            'events_id'     => $eventId,
            'shared_id' => $sharedid,
        ]);
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
        if (!self::getOwnedEvent($eventId, $userid)) {
            return false;
        }

        $sql = "DELETE FROM `events_share`
                WHERE `id` = :share_id AND `events_id` = :events_id
                LIMIT 1;";

        return DB::delete($sql, [
            'share_id' => $shareId,
            'events_id' => $eventId,
        ]);
    }

    /**
     * Check if share already exists
     * @param int $eventId
     * @param string $sharedid
     * @return bool
     */
    public static function shareExists(int $eventId, string $sharedid)
    {
        $sql = "SELECT `id`
                FROM `events_share`
                WHERE `events_id` = :events_id AND `shared_id` = :shared_id
                LIMIT 1;";

        $share = DB::one($sql, [
            'events_id'     => $eventId,
            'shared_id' => $sharedid,
        ]);

        return $share !== null;
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
}


