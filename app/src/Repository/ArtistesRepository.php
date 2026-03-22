<?php

namespace App\Repository;

use App\Repository\Interfaces\IArtistesRepository;
use PDO;
use App\Models\Database;
use App\Models\Event\PerformerModel;

class ArtistesRepository implements IArtistesRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllArtistesForEvent(int $eventId): array
    {
        $stmt = $this->db->prepare("SELECT id, event_id, performer_name, performer_type, description, created_at FROM performers WHERE event_id = :event_id LIMIT 100");
        $stmt->execute([':event_id' => $eventId]);
        $artistes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
        {
             $artistes[] = $this->mapArtistes($row);
        }
        return $artistes;
    }

    public function getArtisteById(int $id): ?PerformerModel
    {
        $stmt = $this->db->prepare("SELECT id, event_id, performer_name, performer_type, description, created_at FROM performers WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) 
        {
            return $this->mapArtistes($row);
        }
        return null;
    }

    public function updateArtiste(PerformerModel $artiste): bool
    {
        $stmt = $this->db->prepare("UPDATE performers SET performer_name = :name, performer_type = :type, event_id = :event_id, description = :description WHERE id = :id");
        $params = $this->mapStmt($artiste);
        $params[':id'] = $artiste->id;
        return $stmt->execute($params);
    }

    public function deleteArtisteById(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM performers WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    public function addArtiste(PerformerModel $artiste): bool
    {
        $stmt = $this->db->prepare("INSERT INTO performers (performer_name, performer_type, event_id, description) VALUES (:name, :type, :event_id, :description)");
        $params = $this->mapStmt($artiste);
        return $stmt->execute($params);
    }

    private function mapArtistes(array $row): PerformerModel
    {
         return new PerformerModel(
            id: (int) $row['id'],
            eventId: (int) $row['event_id'],
            performerName: $row['performer_name'],
            performerType: $row['performer_type'],
            description: $row['description'],
            createdAt: $row['created_at'],
        );
    }

    private function mapStmt(PerformerModel $artiste): array
    {
        return [
            ':name' => $artiste->performerName,
            ':type' => $artiste->performerType,
            ':event_id' => $artiste->eventId,
            ':description' => $artiste->description,
        ];
    }
}