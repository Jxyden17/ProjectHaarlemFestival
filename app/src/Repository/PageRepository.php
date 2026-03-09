<?php

namespace App\Repository;

use App\Models\Database;
use App\Repository\Interfaces\IPageRepository;
use PDO;

class PageRepository implements IPageRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findPageRowById(int $pageId): ?array
    {
        if ($pageId <= 0) {
            return null;
        }

        $stmt = $this->db->prepare('SELECT id, page_name, slug FROM pages WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $pageId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findPageRowBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT id, page_name, slug FROM pages WHERE slug = :slug LIMIT 1');
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
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

        $existsStmt = $this->db->prepare(
            'SELECT 1 FROM section_items WHERE id = :id AND section_id = :section_id LIMIT 1'
        );

        foreach ($items as $item) {
            $params = [
                ':section_id' => $sectionId,
                ':title' => $item['title'],
                ':item_subtitle' => $item['item_subtitle'] ?? null,
                ':content' => $item['content'],
                ':image_path' => $item['image_path'],
                ':link_url' => $item['link_url'],
                ':duration' => $item['duration'] ?? null,
                ':icon_class' => $item['icon_class'] ?? null,
                ':order_index' => $item['order_index'],
                ':item_category' => $item['item_category'],
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

    public function findPageIdBySlug(string $slug): ?int
    {
        $stmt = $this->db->prepare('SELECT id FROM pages WHERE slug = :slug LIMIT 1');
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return (int)$row['id'];
    }

    public function getPageSectionsByPageId(int $pageId): array
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

    public function getItemsBySectionIds(array $sectionIds): array
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
}
