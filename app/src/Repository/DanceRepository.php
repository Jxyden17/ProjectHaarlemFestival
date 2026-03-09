<?php

namespace App\Repository;

use App\Models\Database;
use App\Models\Event\EventDetailPageModel;
use App\Models\Event\EventModel;
use App\Models\Event\PerformerModel;
use App\Models\Event\VenueModel;
use App\Repository\Interfaces\IDanceRepository;
use PDO;

class DanceRepository implements IDanceRepository
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

        return new EventModel((int)$row['id'], (string)$row['name'], isset($row['description']) ? (string)$row['description'] : null);
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

    public function getVenuesByEventId(int $eventId): array {
        $stmt = $this->db->prepare('
            SELECT id, event_id, venue_name, address, venue_type, created_at
            FROM venues
            WHERE event_id = :event_id
            ORDER BY venue_name ASC
        ');
        $stmt->execute([':event_id' => $eventId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(static fn(array $row): VenueModel => new VenueModel((int)$row['id'], (int)$row['event_id'], (string)$row['venue_name'], isset($row['address']) ? (string)$row['address'] : null, isset($row['venue_type']) ? (string)$row['venue_type'] : null, isset($row['created_at']) ? (string)$row['created_at'] : null), $rows);
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

    public function findDetailPageByPublicSlug(string $publicSlug): ?EventDetailPageModel
    {
        if (trim($publicSlug) === '') {
            return null;
        }

        $stmt = $this->db->prepare(
            'SELECT edp.id,
                    edp.event_id,
                    edp.performer_id,
                    edp.page_id,
                    edp.public_slug,
                    edp.cms_slug,
                    edp.entity_type,
                    edp.is_published,
                    edp.display_order,
                    p.slug AS page_slug,
                    p.page_name,
                    pf.performer_name
             FROM event_detail_pages edp
             INNER JOIN pages p ON p.id = edp.page_id
             LEFT JOIN performers pf ON pf.id = edp.performer_id
             WHERE edp.public_slug = :public_slug
             LIMIT 1'
        );
        $stmt->execute([':public_slug' => trim($publicSlug)]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapDetailPageRow($row) : null;
    }

    public function findDetailPageByCmsSlug(string $cmsSlug): ?EventDetailPageModel
    {
        if (trim($cmsSlug) === '') {
            return null;
        }

        $stmt = $this->db->prepare(
            'SELECT edp.id,
                    edp.event_id,
                    edp.performer_id,
                    edp.page_id,
                    edp.public_slug,
                    edp.cms_slug,
                    edp.entity_type,
                    edp.is_published,
                    edp.display_order,
                    p.slug AS page_slug,
                    p.page_name,
                    pf.performer_name
             FROM event_detail_pages edp
             INNER JOIN pages p ON p.id = edp.page_id
             LEFT JOIN performers pf ON pf.id = edp.performer_id
             WHERE edp.cms_slug = :cms_slug
             LIMIT 1'
        );
        $stmt->execute([':cms_slug' => trim($cmsSlug)]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapDetailPageRow($row) : null;
    }

    public function getPublishedDetailPagesByEventId(int $eventId): array
    {
        $stmt = $this->db->prepare(
            'SELECT edp.id,
                    edp.event_id,
                    edp.performer_id,
                    edp.page_id,
                    edp.public_slug,
                    edp.cms_slug,
                    edp.entity_type,
                    edp.is_published,
                    edp.display_order,
                    p.slug AS page_slug,
                    p.page_name,
                    pf.performer_name
             FROM event_detail_pages edp
             INNER JOIN pages p ON p.id = edp.page_id
             LEFT JOIN performers pf ON pf.id = edp.performer_id
             WHERE edp.event_id = :event_id
               AND edp.is_published = 1
             ORDER BY edp.display_order ASC, edp.id ASC'
        );
        $stmt->execute([':event_id' => $eventId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $row): EventDetailPageModel => $this->mapDetailPageRow($row), $rows);
    }

    private function mapDetailPageRow(array $row): EventDetailPageModel
    {
        return new EventDetailPageModel(
            (int)$row['id'],
            (int)$row['event_id'],
            isset($row['performer_id']) ? (int)$row['performer_id'] : null,
            (int)$row['page_id'],
            (string)($row['page_slug'] ?? ''),
            (string)($row['public_slug'] ?? ''),
            (string)($row['cms_slug'] ?? ''),
            (string)($row['entity_type'] ?? 'performer'),
            (bool)($row['is_published'] ?? false),
            (int)($row['display_order'] ?? 0),
            isset($row['performer_name']) ? (string)$row['performer_name'] : null
        );
    }

}
