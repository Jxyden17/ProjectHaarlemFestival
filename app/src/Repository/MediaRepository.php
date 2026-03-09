<?php

namespace App\Repository;

use App\Models\Database;
use App\Repository\Interfaces\IMediaRepository;
use PDO;

class MediaRepository implements IMediaRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function updateSectionItemImagePath(
        int $sectionItemId,
        string $imagePath,
        string $pageSlug,
        string $sectionType,
        string $itemCategory
    ): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE section_items si
             INNER JOIN page_sections ps ON ps.id = si.section_id
             INNER JOIN pages p ON p.id = ps.page_id
             SET si.image_path = :image_path
             WHERE si.id = :section_item_id
               AND si.item_category = :item_category
               AND ps.section_type = :section_type
               AND p.slug = :page_slug'
        );

        $stmt->execute([
            ':image_path' => $imagePath,
            ':section_item_id' => $sectionItemId,
            ':item_category' => $itemCategory,
            ':section_type' => $sectionType,
            ':page_slug' => $pageSlug,
        ]);

        if ($stmt->rowCount() > 0) {
            return true;
        }

        $existsStmt = $this->db->prepare(
            'SELECT si.id
             FROM section_items si
             INNER JOIN page_sections ps ON ps.id = si.section_id
             INNER JOIN pages p ON p.id = ps.page_id
             WHERE si.id = :section_item_id
               AND si.item_category = :item_category
               AND ps.section_type = :section_type
               AND p.slug = :page_slug
               AND si.image_path = :image_path
             LIMIT 1'
        );

        $existsStmt->execute([
            ':section_item_id' => $sectionItemId,
            ':item_category' => $itemCategory,
            ':section_type' => $sectionType,
            ':page_slug' => $pageSlug,
            ':image_path' => $imagePath,
        ]);

        return $existsStmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function findSectionItemIdByImagePath(
        string $imagePath,
        string $pageSlug,
        string $sectionType,
        string $itemCategory
    ): ?int
    {
        $stmt = $this->db->prepare(
            'SELECT si.id
             FROM section_items si
             INNER JOIN page_sections ps ON ps.id = si.section_id
             INNER JOIN pages p ON p.id = ps.page_id
             WHERE si.image_path = :image_path
               AND si.item_category = :item_category
               AND ps.section_type = :section_type
               AND p.slug = :page_slug
             LIMIT 1'
        );

        $stmt->execute([
            ':image_path' => $imagePath,
            ':item_category' => $itemCategory,
            ':section_type' => $sectionType,
            ':page_slug' => $pageSlug,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || !isset($row['id'])) {
            return null;
        }

        return (int)$row['id'];
    }
}
