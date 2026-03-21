<?php

namespace App\Repository;

use App\Models\Database;
use App\Models\Event\VenueModel;
use App\Repository\Interfaces\IVenueRepository;
use PDO;

class VenueRepository implements IVenueRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getByEventId(int $eventId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, event_id, venue_name, address, venue_type, created_at
             FROM venues
             WHERE event_id = :event_id
             Limit 100'
        );
        $stmt->execute([':event_id' => $eventId]);
        return array_map(fn(array $row) => $this->mapRow($row), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getById(int $id): ?VenueModel
    {
        $stmt = $this->db->prepare(
            'SELECT id, event_id, venue_name, address, venue_type, created_at
             FROM venues WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->mapRow($row) : null;
    }

    public function create(VenueModel $venueModel): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO venues (event_id, venue_name, address, venue_type)
             VALUES (:event_id, :venue_name, :address, :venue_type)'
        );
        return  $stmt->execute($this->mapStmt($venueModel));
    }

    public function update(VenueModel $venueModel): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE venues SET venue_name = :venue_name, address = :address, venue_type = :venue_type
             WHERE id = :id'
        );
        return $stmt->execute($this->mapStmt($venueModel));
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM venues WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    private function mapRow(array $row): VenueModel
    {
        return new VenueModel(
            $row['id'],
            $row['event_id'],
            $row['venue_name'],
            $row['address'],
            $row['venue_type'],
            $row['created_at'],
        );
    }

    private function mapStmt(VenueModel $venue): array
    {
        $data = [
            ':id' => $venue->id,
            ':event_id' => $venue->eventId,
            ':venue_name' => $venue->venueName,
            ':address' => $venue->address,
            ':venue_type' => $venue->venueType,
        ];
        return $data;
    }
}
