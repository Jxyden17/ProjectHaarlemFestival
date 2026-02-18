<?php

namespace App\Repository;

use App\Models\Database;
use App\Models\EventModel;
use App\Models\VenueModel;
use PDO;

class DanceRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findDanceEvent(): ?EventModel
    {
        $stmt = $this->db->prepare('SELECT id, name, description FROM events WHERE name = :name LIMIT 1');
        $stmt->execute([':name' => 'Dance']);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new EventModel(
            (int)$row['id'],
            (string)$row['name'],
            isset($row['description']) ? (string)$row['description'] : null
        );
    }

    public function countSessionsByEventId(int $eventId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) AS total FROM sessions WHERE event_id = :event_id');
        $stmt->execute([':event_id' => $eventId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return isset($row['total']) ? (int)$row['total'] : 0;
    }

    public function countDistinctVenuesByEventId(int $eventId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(DISTINCT venue_id) AS total FROM sessions WHERE event_id = :event_id');
        $stmt->execute([':event_id' => $eventId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return isset($row['total']) ? (int)$row['total'] : 0;
    }

    public function getVenuesByEventId(int $eventId): array
    {
        $stmt = $this->db->prepare('
            SELECT id, event_id, venue_name, address, venue_type, created_at
            FROM venues
            WHERE event_id = :event_id
            ORDER BY venue_name ASC
        ');
        $stmt->execute([':event_id' => $eventId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            static fn(array $row): VenueModel => new VenueModel(
                (int)$row['id'],
                (int)$row['event_id'],
                (string)$row['venue_name'],
                isset($row['address']) ? (string)$row['address'] : null,
                isset($row['venue_type']) ? (string)$row['venue_type'] : null,
                isset($row['created_at']) ? (string)$row['created_at'] : null
            ),
            $rows
        );
    }
}
