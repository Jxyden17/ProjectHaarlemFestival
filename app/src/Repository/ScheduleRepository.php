<?php

namespace App\Repository;

use App\Models\Database;
use App\Models\EventModel;
use App\Models\PerformerModel;
use App\Models\SessionModel;
use App\Models\SessionPerformerModel;
use App\Models\VenueModel;
use PDO;

class ScheduleRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findEventByName(string $name): ?EventModel
    {
        $stmt = $this->db->prepare('SELECT id, name, description FROM events WHERE name = :name LIMIT 1');
        $stmt->execute([':name' => $name]);
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

    public function getSessionsByEventId(int $eventId): array
    {
        $stmt = $this->db->prepare('
            SELECT id, event_id, venue_id, date, start_time, label, price, available_spots, amount_sold
            FROM sessions
            WHERE event_id = :event_id
            ORDER BY date ASC, start_time ASC
        ');
        $stmt->execute([':event_id' => $eventId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            static fn(array $row): SessionModel => new SessionModel(
                (int)$row['id'],
                isset($row['event_id']) ? (int)$row['event_id'] : null,
                (int)$row['venue_id'],
                (string)$row['date'],
                (string)$row['start_time'],
                isset($row['label']) ? (string)$row['label'] : null,
                (float)$row['price'],
                (int)$row['available_spots'],
                (int)$row['amount_sold']
            ),
            $rows
        );
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

    public function getPerformersByEventId(int $eventId): array
    {
        $stmt = $this->db->prepare('
            SELECT id, event_id, performer_name, performer_type, description, created_at
            FROM performers
            WHERE event_id = :event_id
            ORDER BY performer_name ASC
        ');
        $stmt->execute([':event_id' => $eventId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            static fn(array $row): PerformerModel => new PerformerModel(
                (int)$row['id'],
                (int)$row['event_id'],
                (string)$row['performer_name'],
                isset($row['performer_type']) ? (string)$row['performer_type'] : null,
                isset($row['description']) ? (string)$row['description'] : null,
                isset($row['created_at']) ? (string)$row['created_at'] : null
            ),
            $rows
        );
    }

    public function getSessionPerformersByEventId(int $eventId): array
    {
        $stmt = $this->db->prepare('
            SELECT sp.session_id, sp.performer_id
            FROM session_performers sp
            INNER JOIN sessions s ON s.id = sp.session_id
            WHERE s.event_id = :event_id
            ORDER BY sp.session_id ASC, sp.performer_id ASC
        ');
        $stmt->execute([':event_id' => $eventId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            static fn(array $row): SessionPerformerModel => new SessionPerformerModel(
                (int)$row['session_id'],
                (int)$row['performer_id']
            ),
            $rows
        );
    }
}
