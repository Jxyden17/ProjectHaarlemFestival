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

    private const DETAIL_PAGES_SELECT = 'SELECT edp.id,
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

    public function findDetailPageBySlug(string $detailSlug): ?EventDetailPageModel
    {
        if (trim($detailSlug) === '') {
            return null;
        }

        $row = $this->fetchDetailPageRowBySlug(trim($detailSlug));

        return $row ? $this->danceMapper->mapDetailPageRow($row) : null;
    }

    private function fetchDetailPageRowsByEventId(int $eventId): array
    {
        try {
            $stmt = $this->db->prepare(
                self::DETAIL_PAGES_SELECT . '
             WHERE edp.event_id = :event_id
             ORDER BY edp.display_order ASC, edp.id ASC'
            );
            $stmt->execute([':event_id' => $eventId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            if (!$this->isMissingDetailSlugColumnError($e)) {
                throw $e;
            }

            $stmt = $this->db->prepare(
                self::DETAIL_PAGES_FALLBACK_SELECT . '
             WHERE edp.event_id = :event_id
             ORDER BY edp.display_order ASC, edp.id ASC'
            );
            $stmt->execute([':event_id' => $eventId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    private function fetchDetailPageRowBySlug(string $detailSlug): array|false
    {
        try {
            $stmt = $this->db->prepare(
                self::DETAIL_PAGES_SELECT . '
             WHERE edp.detail_slug = :detail_slug
             LIMIT 1'
            );
            $stmt->execute([':detail_slug' => $detailSlug]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            if (!$this->isMissingDetailSlugColumnError($e)) {
                throw $e;
            }

            $stmt = $this->db->prepare(
                self::DETAIL_PAGES_FALLBACK_SELECT . '
             WHERE p.slug = :detail_slug
             LIMIT 1'
            );
            $stmt->execute([':detail_slug' => $detailSlug]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

    private function isMissingDetailSlugColumnError(\PDOException $e): bool
    {
        return str_contains(strtolower($e->getMessage()), 'unknown column')
            && str_contains(strtolower($e->getMessage()), 'detail_slug');
    }
}
