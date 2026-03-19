<?php

namespace App\Repository;

use App\Mapper\DanceMapper;
use App\Models\Database;
use App\Models\Event\EventDetailPageModel;
use App\Repository\Interfaces\IDanceRepository;
use PDO;

class DanceRepository implements IDanceRepository
{
    private PDO $db;
    private DanceMapper $danceMapper;

    public function __construct(DanceMapper $danceMapper)
    {
        $this->db = Database::getInstance();
        $this->danceMapper = $danceMapper;
    }

    public function getDetailPagesByEventId(int $eventId): array
    {
        $stmt = $this->db->prepare(
            'SELECT edp.id,
                    edp.event_id,
                    edp.performer_id,
                    edp.page_id,
                    edp.entity_type,
                    edp.display_order,
                    p.slug AS page_slug,
                    p.page_name,
                    pf.performer_name
             FROM event_detail_pages edp
             INNER JOIN pages p ON p.id = edp.page_id
             LEFT JOIN performers pf ON pf.id = edp.performer_id';

    private const DETAIL_PAGES_FALLBACK_SELECT = 'SELECT edp.id,
                    edp.event_id,
                    edp.performer_id,
                    edp.page_id,
                    p.slug AS detail_slug,
                    edp.entity_type,
                    edp.display_order,
                    p.slug AS page_slug,
                    p.page_name,
                    pf.performer_name
             FROM event_detail_pages edp
             INNER JOIN pages p ON p.id = edp.page_id
             LEFT JOIN performers pf ON pf.id = edp.performer_id';

    public function __construct(DanceMapper $danceMapper)
    {
        $this->db = Database::getInstance();
        $this->danceMapper = $danceMapper;
    }

    public function getDetailPagesByEventId(int $eventId): array
    {
        $rows = $this->fetchDetailPageRowsByEventId($eventId);

        return array_map(fn(array $row): EventDetailPageModel => $this->danceMapper->mapDetailPageRow($row), $rows);
    }

    public function findDetailPageByPageSlug(string $pageSlug): ?EventDetailPageModel
    {
        if (trim($pageSlug) === '') {
            return null;
        }

        $stmt = $this->db->prepare(
            'SELECT edp.id,
                    edp.event_id,
                    edp.performer_id,
                    edp.page_id,
                    edp.entity_type,
                    edp.display_order,
                    p.slug AS page_slug,
                    p.page_name,
                    pf.performer_name
             FROM event_detail_pages edp
             INNER JOIN pages p ON p.id = edp.page_id
             LEFT JOIN performers pf ON pf.id = edp.performer_id
             WHERE p.slug = :page_slug
             LIMIT 1'
        );
        $stmt->execute([':page_slug' => trim($pageSlug)]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->danceMapper->mapDetailPageRow($row) : null;
    }
}
