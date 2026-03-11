<?php

namespace App\Repository;

use App\Mapper\DanceMapper;
use App\Models\Database;
use App\Models\Event\EventDetailPageModel;
use App\Models\Event\EventModel;
use App\Repository\Interfaces\IDanceRepository;
use PDO;

class DanceRepository implements IDanceRepository
{
    private PDO $db;
    private DanceMapper $danceMapper;

    public function __construct(?DanceMapper $danceMapper = null)
    {
        $this->db = Database::getInstance();
        $this->danceMapper = $danceMapper ?? new DanceMapper();
    }

    public function findEventByName(string $eventName): ?EventModel
    {
        $stmt = $this->db->prepare('SELECT id, name, description FROM events WHERE name = :name LIMIT 1');
        $stmt->execute([':name' => trim($eventName)]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $this->danceMapper->mapEventRow($row);
    }

    public function getVenuesByEventId(int $eventId): array {
        $stmt = $this->db->prepare('
            SELECT id, event_id, venue_name, address, venue_type, created_at
            FROM venues
            WHERE event_id = :event_id
            ORDER BY venue_name ASC
        ');
        $stmt->execute([':event_id' => $eventId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $row) => $this->danceMapper->mapVenueRow($row), $rows);
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

        return array_map(fn(array $row) => $this->danceMapper->mapPerformerRow($row), $rows);
    }

    public function getDetailPagesByEventId(int $eventId): array
    {
        $stmt = $this->db->prepare(
            'SELECT edp.id,
                    edp.event_id,
                    edp.performer_id,
                    edp.page_id,
                    edp.detail_slug,
                    edp.entity_type,
                    edp.display_order,
                    p.slug AS page_slug,
                    p.page_name,
                    pf.performer_name
             FROM event_detail_pages edp
             INNER JOIN pages p ON p.id = edp.page_id
             LEFT JOIN performers pf ON pf.id = edp.performer_id
             WHERE edp.event_id = :event_id
             ORDER BY edp.display_order ASC, edp.id ASC'
        );
        $stmt->execute([':event_id' => $eventId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $row): EventDetailPageModel => $this->danceMapper->mapDetailPageRow($row), $rows);
    }

    public function findDetailPageBySlug(string $detailSlug): ?EventDetailPageModel
    {
        if (trim($detailSlug) === '') {
            return null;
        }

        $stmt = $this->db->prepare(
            'SELECT edp.id,
                    edp.event_id,
                    edp.performer_id,
                    edp.page_id,
                    edp.detail_slug,
                    edp.entity_type,
                    edp.display_order,
                    p.slug AS page_slug,
                    p.page_name,
                    pf.performer_name
             FROM event_detail_pages edp
             INNER JOIN pages p ON p.id = edp.page_id
             LEFT JOIN performers pf ON pf.id = edp.performer_id
             WHERE edp.detail_slug = :detail_slug
             LIMIT 1'
        );
        $stmt->execute([':detail_slug' => trim($detailSlug)]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->danceMapper->mapDetailPageRow($row) : null;
    }

}
