<?php

namespace App\Repository;

use App\Models\Database;
use PDO;

class ScheduleRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findEventIdByName(string $name): ?int
    {
        $stmt = $this->db->prepare('SELECT id FROM events WHERE name = :name LIMIT 1');
        $stmt->execute([':name' => $name]);
        $id = $stmt->fetchColumn();

        return $id !== false ? (int)$id : null;
    }

    public function getSessionsByEventId(int $eventId): array
    {
        $sql = "
            SELECT
                s.id,
                s.date,
                s.start_time,
                s.language,
                s.price,
                s.available_spots,
                v.venue_name,
                v.address,
                GROUP_CONCAT(DISTINCT p.performer_name ORDER BY p.performer_name SEPARATOR ' B2B ') AS performer_lineup
            FROM sessions s
            INNER JOIN venues v ON v.id = s.venue_id
            LEFT JOIN session_performers sp ON sp.session_id = s.id
            LEFT JOIN performers p ON p.id = sp.performer_id
            WHERE s.event_id = :event_id
            GROUP BY
                s.id, s.date, s.start_time, s.language, s.price, s.available_spots, v.venue_name, v.address
            ORDER BY s.date ASC, s.start_time ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':event_id' => $eventId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
