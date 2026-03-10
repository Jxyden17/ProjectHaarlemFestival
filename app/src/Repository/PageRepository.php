<?php

namespace App\Repository;

use App\Models\Database;
use App\Models\Page\Page;
use App\Models\Page\Section;
use App\Models\Page\SectionItem;
use App\Repository\Interfaces\IPageRepository;
use PDO;

class PageRepository implements IPageRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getPageById(int $pageId): ?Page
    {
        if ($pageId <= 0) {
            return null;
        }

        $pageRow = $this->findPageRowById($pageId);
        if ($pageRow === null) {
            return null;
        }

        $page = new Page((string)($pageRow['page_name'] ?? ''), (string)($pageRow['slug'] ?? ''));
        $page->sections = $this->buildSectionsForPage($pageId);

        return $page;
    }

    public function getPageBySlug(string $slug, string $fallbackTitle = ''): Page
    {
        $page = new Page($fallbackTitle, $slug);
        $page->sections = [];

        $pageId = $this->findPageIdBySlug($slug);
        if ($pageId === null) {
            return $page;
        }

        $page->sections = $this->buildSectionsForPage($pageId);

        return $page;
    }

    public function ensurePageBySlug(int $eventId, string $slug, string $pageName): int
    {
        $pageId = $this->findPageIdBySlug($slug);
        if ($pageId !== null) {
            return $pageId;
        }

        $stmt = $this->db->prepare('INSERT INTO pages (event_id, slug, page_name) VALUES (:event_id, :slug, :page_name)');
        $stmt->execute([':event_id' => $eventId, ':slug' => $slug, ':page_name' => $pageName]);

        return (int)$this->db->lastInsertId();
    }

    public function saveOrUpdateSection(int $pageId, string $sectionType, ?string $title, ?string $subtitle, ?string $description, int $orderIndex): int
    {
        $stmt = $this->db->prepare('SELECT id FROM page_sections WHERE page_id = :page_id AND section_type = :section_type LIMIT 1');
        $stmt->execute([':page_id' => $pageId, ':section_type' => $sectionType]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new \RuntimeException('Missing section for update: ' . $sectionType);
        }

        $sectionId = (int)$row['id'];
        $update = $this->db->prepare('UPDATE page_sections SET title = :title, subtitle = :subtitle, description = :description, order_index = :order_index WHERE id = :id');
        $update->execute([':title' => $title, ':subtitle' => $subtitle, ':description' => $description, ':order_index' => $orderIndex, ':id' => $sectionId]);

        return $sectionId;
    }

    public function upsertSectionItems(int $sectionId, array $items): void
    {
        if (empty($items)) {
            return;
        }

        $updateStmt = $this->db->prepare(
            'UPDATE section_items
             SET title = :title,
                 content = :content,
                 image_path = :image_path,
                 link_url = :link_url,
                 order_index = :order_index,
                 item_category = :item_category,
                 duration = :duration,
                 icon_class = :icon_class,
                item_subtitle = :item_subtitle
             WHERE id = :id AND section_id = :section_id'
        );

        $existsStmt = $this->db->prepare(
            'SELECT 1 FROM section_items WHERE id = :id AND section_id = :section_id LIMIT 1'
        );

        foreach ($items as $item) {
            $params = [
                ':section_id' => $sectionId,
                ':title' => $item['title'] ?? null,
                ':content' => $item['content'] ?? null,
                ':image_path' => $item['image_path'] ?? null,
                ':link_url' => $item['link_url'] ?? null,
                ':order_index' => $item['order_index'] ?? null,
                ':item_category' => $item['item_category'] ?? null,
                ':duration' => $item['duration'] ?? null,
                ':icon_class' => $item['icon_class'] ?? $item['icon'] ?? null,
                ':item_subtitle' => $item['item_subtitle'] ?? $item['subTitle'] ?? null,
            ];

            $itemId = isset($item['id']) ? (int)$item['id'] : 0;
            if ($itemId <= 0) {
                throw new \RuntimeException('Missing section item id for update.');
            }

            $updateParams = $params;
            $updateParams[':id'] = $itemId;
            $updateStmt->execute($updateParams);

            if ($updateStmt->rowCount() > 0) {
                continue;
            }

            $existsStmt->execute([
                ':id' => $itemId,
                ':section_id' => $sectionId,
            ]);

            if ($existsStmt->fetchColumn() === false) {
                throw new \RuntimeException('Missing section item for update: ' . $itemId);
            }
        }
    }

    private function findPageIdBySlug(string $slug): ?int
    {
        $stmt = $this->db->prepare('SELECT id FROM pages WHERE slug = :slug LIMIT 1');
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return (int)$row['id'];
    }

    private function findPageRowById(int $pageId): ?array
    {
        $stmt = $this->db->prepare('SELECT id, page_name, slug FROM pages WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $pageId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    private function getPageSectionsByPageId(int $pageId): array
    {
        $stmt = $this->db->prepare('
            SELECT id, section_type, title, subtitle, description
            FROM page_sections
            WHERE page_id = :page_id
            ORDER BY order_index ASC
        ');
        $stmt->execute([':page_id' => $pageId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getItemsBySectionIds(array $sectionIds): array
    {
        if (empty($sectionIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($sectionIds), '?'));
        $stmt = $this->db->prepare('SELECT id, section_id, item_category, title, item_subtitle, content, image_path, link_url, duration, icon_class, order_index FROM section_items WHERE section_id IN (' . $placeholders . ') ORDER BY order_index ASC');
        $stmt->execute($sectionIds);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [];
        foreach ($rows as $row) {
            $sectionId = (int)$row['section_id'];
            if (!isset($grouped[$sectionId])) {
                $grouped[$sectionId] = [];
            }
            $grouped[$sectionId][] = $row;
        }

        return $grouped;
    }

    private function buildSectionsForPage(int $pageId): array
    {
        $sectionRows = $this->getPageSectionsByPageId($pageId);
        if (empty($sectionRows)) {
            return [];
        }

        $sectionIds = array_map(static fn(array $row): int => (int)$row['id'], $sectionRows);
        $itemsBySection = $this->getItemsBySectionIds($sectionIds);
        $sections = [];

        foreach ($sectionRows as $row) {
            $sectionId = (int)$row['id'];
            $section = $this->mapSectionRow($row);

            foreach (($itemsBySection[$sectionId] ?? []) as $itemRow) {
                $section->addItem($this->mapSectionItemRow($itemRow));
            }

            $sections[] = $section;
        }

        return $sections;
    }

    private function mapSectionRow(array $row): Section
    {
        return new Section(
            (int)($row['id'] ?? 0),
            (string)($row['section_type'] ?? ''),
            (string)($row['title'] ?? ''),
            (string)($row['subtitle'] ?? ''),
            (string)($row['description'] ?? '')
        );
    }

    private function mapSectionItemRow(array $row): SectionItem
    {
        return new SectionItem(
            (int)($row['id'] ?? 0),
            (string)($row['title'] ?? ''),
            isset($row['content']) ? (string)$row['content'] : null,
            isset($row['image_path']) ? (string)$row['image_path'] : null,
            isset($row['link_url']) ? (string)$row['link_url'] : null,
            (string)($row['item_category'] ?? ''),
            isset($row['duration']) ? (string)$row['duration'] : null,
            isset($row['icon_class']) ? (string)$row['icon_class'] : null,
            isset($row['item_subtitle']) ? (string)$row['item_subtitle'] : null,
            (int)($row['order_index'] ?? 0)
        );
    }
    
}