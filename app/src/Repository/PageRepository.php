<?php

namespace App\Repository;

use App\Mapper\PageMapper;
use App\Models\Database;
use App\Models\Page\Page;
use App\Repository\Interfaces\IPageRepository;
use PDO;

class PageRepository implements IPageRepository
{
    private PDO $db;
    private PageMapper $pageMapper;

    public function __construct(PageMapper $pageMapper)
    {
        $this->db = Database::getInstance();
        $this->pageMapper = $pageMapper;
    }

    public function findPageById(int $pageId): ?Page
    {
        if ($pageId <= 0) {
            return null;
        }

        $stmt = $this->db->prepare(
            'SELECT p.id AS page_id,
                    p.page_name,
                    p.slug,
                    ps.id AS section_id,
                    ps.section_type,
                    ps.title AS section_title,
                    ps.subtitle AS section_subtitle,
                    ps.description AS section_description,
                    ps.order_index AS section_order_index,
                    si.id AS item_id,
                    si.item_category,
                    si.title AS item_title,
                    si.item_subtitle,
                    si.content,
                    si.image_path,
                    si.link_url,
                    si.duration,
                    si.icon_class,
                    si.order_index AS item_order_index
             FROM pages p
             LEFT JOIN page_sections ps ON ps.page_id = p.id
             LEFT JOIN section_items si ON si.section_id = ps.id
             WHERE p.id = :id
             ORDER BY ps.order_index ASC, si.order_index ASC'
        );
        $stmt->execute([':id' => $pageId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($rows === []) {
            return null;
        }

        return $this->pageMapper->mapPageRows($rows);
    }

    public function findPageBySlug(string $slug): ?Page
    {
        $stmt = $this->db->prepare(
            'SELECT p.id AS page_id,
                    p.page_name,
                    p.slug,
                    ps.id AS section_id,
                    ps.section_type,
                    ps.title AS section_title,
                    ps.subtitle AS section_subtitle,
                    ps.description AS section_description,
                    ps.order_index AS section_order_index,
                    si.id AS item_id,
                    si.item_category,
                    si.title AS item_title,
                    si.item_subtitle,
                    si.content,
                    si.image_path,
                    si.link_url,
                    si.duration,
                    si.icon_class,
                    si.order_index AS item_order_index
             FROM pages p
             LEFT JOIN page_sections ps ON ps.page_id = p.id
             LEFT JOIN section_items si ON si.section_id = ps.id
             WHERE p.slug = :slug
             ORDER BY ps.order_index ASC, si.order_index ASC'
        );
        $stmt->execute([':slug' => $slug]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($rows === []) {
            return null;
        }

        return $this->pageMapper->mapPageRows($rows);
    }

    public function findPagesByEventId(int $eventId): array
    {
        if ($eventId <= 0) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT id, page_name, slug
             FROM pages
             WHERE event_id = :event_id
             ORDER BY id ASC'
        );
        $stmt->execute([':event_id' => $eventId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveOrUpdateSection(
        int $pageId,
        string $sectionType,
        ?string $title,
        ?string $subtitle,
        ?string $description,
        int $orderIndex
    ): int {
        $stmt = $this->db->prepare(
            'SELECT id FROM page_sections WHERE page_id = :page_id AND section_type = :section_type LIMIT 1'
        );
        $stmt->execute([':page_id' => $pageId, ':section_type' => $sectionType]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new \RuntimeException('Missing section for update: ' . $sectionType);
        }

        $sectionId = (int) $row['id'];
        $this->updateSectionById($sectionId, $title, $subtitle, $description, $orderIndex);

        return $sectionId;
    }

    public function saveOrUpdateSectionItems(int $sectionId, array $items): void
    {
        if (empty($items)) {
            return;
        }

        $updateStmt = $this->db->prepare(
            'UPDATE section_items
             SET title = :title,
                 item_subtitle = :item_subtitle,
                 content = :content,
                 image_path = :image_path,
                 link_url = :link_url,
                 duration = :duration,
                 icon_class = :icon_class,
                 order_index = :order_index,
                 item_category = :item_category
             WHERE id = :id AND section_id = :section_id'
        );

        $existingItemIds = $this->findSectionItemIds($sectionId);

        foreach ($items as $item) {
            $params = [
                ':section_id' => $sectionId,
                ':title' => $item['title'],
                ':item_subtitle' => $item['item_subtitle'] ?? null,
                ':content' => $item['content'],
                ':image_path' => $item['image_path'] ?? null,
                ':link_url' => $item['link_url'] ?? null,
                ':duration' => $item['duration'] ?? null,
                ':icon_class' => $item['icon_class'] ?? null,
                ':order_index' => $item['order_index'],
                ':item_category' => $item['item_category'],
            ];

            $itemId = isset($item['id']) ? (int) $item['id'] : 0;
            if ($itemId <= 0) {
                throw new \RuntimeException('Missing section item id for update.');
            }

            if (!isset($existingItemIds[$itemId])) {
                throw new \RuntimeException('Missing section item for update: ' . $itemId);
            }

            $updateParams = $params;
            $updateParams[':id'] = $itemId;
            $updateStmt->execute($updateParams);
        }
    }

    public function findPageIdBySlug(string $slug): ?int
    {
        $stmt = $this->db->prepare('SELECT id FROM pages WHERE slug = :slug LIMIT 1');
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return (int) $row['id'];
    }

    public function findSectionIdsByPageId(int $pageId): array
    {
        if ($pageId <= 0) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT id, section_type
             FROM page_sections
             WHERE page_id = :page_id'
        );
        $stmt->execute([':page_id' => $pageId]);

        $sectionIdsByType = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $sectionIdsByType[(string) $row['section_type']] = (int) $row['id'];
        }

        return $sectionIdsByType;
    }

    public function updatePageName(int $pageId, string $pageName): void
    {
        $stmt = $this->db->prepare(
            'UPDATE pages
             SET page_name = :page_name
             WHERE id = :id'
        );
        $stmt->execute([
            ':page_name' => $pageName,
            ':id' => $pageId,
        ]);
    }

    public function updateSectionById(
        int $sectionId,
        ?string $title,
        ?string $subtitle,
        ?string $description,
        int $orderIndex
    ): void
    {
        $update = $this->db->prepare(
            'UPDATE page_sections
             SET title = :title, subtitle = :subtitle, description = :description, order_index = :order_index
             WHERE id = :id'
        );
        $update->execute([
            ':title' => $title,
            ':subtitle' => $subtitle,
            ':description' => $description,
            ':order_index' => $orderIndex,
            ':id' => $sectionId,
        ]);
    }

    private function findSectionItemIds(int $sectionId): array
    {
        if ($sectionId <= 0) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT id
             FROM section_items
             WHERE section_id = :section_id'
        );
        $stmt->execute([':section_id' => $sectionId]);

        $itemIds = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $itemIds[(int) $row['id']] = true;
        }

        return $itemIds;
    }
}
