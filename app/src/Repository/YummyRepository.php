<?php

namespace App\Repository;

use App\Models\Database;
use App\Models\EventModel;
use App\Models\VenueModel;
use PDO;

class YummyRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findYummyEvent(): ?EventModel
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, description 
             FROM events 
             WHERE name = :name 
             LIMIT 1'
        );

        $stmt->execute([':name' => 'Yummy']);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new EventModel(
            (int)$row['id'],
            (string)$row['name'],
            $row['description'] ?? null
        );
    }

    public function getVenuesByEventId(int $eventId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, event_id, venue_name, address, venue_type, created_at
             FROM venues
             WHERE event_id = :event_id
             ORDER BY venue_name ASC'
        );

        $stmt->execute([':event_id' => $eventId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            static fn(array $row): VenueModel => new VenueModel(
                (int)$row['id'],
                (int)$row['event_id'],
                (string)$row['venue_name'],
                $row['address'] ?? null,
                $row['venue_type'] ?? null,
                $row['created_at'] ?? null
            ),
            $rows
        );
    }
}