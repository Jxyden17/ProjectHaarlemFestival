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

    public function findPageRowsById(int $pageId): array
    {
        if ($pageId <= 0) {
            return [];
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

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findPageRowsBySlug(string $slug): array
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

        $existsStmt = $this->db->prepare(
            'SELECT 1 FROM section_items WHERE id = :id AND section_id = :section_id LIMIT 1'
        );

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

        return (int) $row['id'];
    }

    public function getAllPages(): array
    {
        $stmt = $this->db->query('SELECT id,event_id, page_name, slug FROM pages ORDER BY page_name ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
