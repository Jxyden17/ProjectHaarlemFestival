<?php

namespace App\Repository;

use App\Mapper\DanceMapper;
use App\Models\Database;
use App\Models\Event\EventDetailPageModel;
use App\Repository\Interfaces\IDanceRepository;
use PDO;
use PDOException;

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
        $queries = [
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
             ORDER BY edp.display_order ASC, edp.id ASC',
            'SELECT edp.id,
                    edp.event_id,
                    edp.performer_id,
                    edp.page_id,
                    edp.public_slug AS detail_slug,
                    edp.entity_type,
                    edp.display_order,
                    p.slug AS page_slug,
                    p.page_name,
                    pf.performer_name
             FROM event_detail_pages edp
             INNER JOIN pages p ON p.id = edp.page_id
             LEFT JOIN performers pf ON pf.id = edp.performer_id
             WHERE edp.event_id = :event_id
             ORDER BY edp.display_order ASC, edp.id ASC',
            'SELECT edp.id,
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
             LEFT JOIN performers pf ON pf.id = edp.performer_id
             WHERE edp.event_id = :event_id
             ORDER BY edp.display_order ASC, edp.id ASC',
        ];

        $rows = $this->fetchRowsWithFallback($queries, [':event_id' => $eventId]);

        return array_map(fn(array $row): EventDetailPageModel => $this->danceMapper->mapDetailPageRow($row), $rows);
    }

    public function findDetailPageBySlug(string $detailSlug): ?EventDetailPageModel
    {
        if (trim($detailSlug) === '') {
            return null;
        }

        $queries = [
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
             LIMIT 1',
            'SELECT edp.id,
                    edp.event_id,
                    edp.performer_id,
                    edp.page_id,
                    edp.public_slug AS detail_slug,
                    edp.entity_type,
                    edp.display_order,
                    p.slug AS page_slug,
                    p.page_name,
                    pf.performer_name
             FROM event_detail_pages edp
             INNER JOIN pages p ON p.id = edp.page_id
             LEFT JOIN performers pf ON pf.id = edp.performer_id
             WHERE edp.public_slug = :detail_slug
                OR edp.cms_slug = :detail_slug
             LIMIT 1',
            'SELECT edp.id,
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
             LEFT JOIN performers pf ON pf.id = edp.performer_id
             WHERE p.slug = :detail_slug
             LIMIT 1',
        ];

        $row = $this->fetchFirstRowWithFallback($queries, [':detail_slug' => trim($detailSlug)]);

        return $row ? $this->danceMapper->mapDetailPageRow($row) : null;
    }

    private function fetchRowsWithFallback(array $queries, array $params): array
    {
        $lastException = null;

        foreach ($queries as $query) {
            try {
                $stmt = $this->db->prepare($query);
                $stmt->execute($params);

                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $exception) {
                $lastException = $exception;
            }
        }

        if ($lastException instanceof PDOException) {
            throw $lastException;
        }

        return [];
    }

    private function fetchFirstRowWithFallback(array $queries, array $params): ?array
    {
        $lastException = null;

        foreach ($queries as $query) {
            try {
                $stmt = $this->db->prepare($query);
                $stmt->execute($params);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row !== false) {
                    return $row;
                }
            } catch (PDOException $exception) {
                $lastException = $exception;
            }
        }

        if ($lastException instanceof PDOException) {
            throw $lastException;
        }

        return null;
    }
}