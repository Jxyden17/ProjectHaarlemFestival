<?php

namespace App\Repository;

use App\Models\Database;
use App\Repository\Interfaces\IPageStorieRepository;
use PDO;

class PageStorieRepository implements IPageStorieRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM pages WHERE slug = :slug LIMIT 1");
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function getSections(int $pageId): array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM page_sections
            WHERE page_id = :page_id
            ORDER BY sort_order ASC, id ASC
        ");
        $stmt->execute([':page_id' => $pageId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSectionItems(int $sectionId): array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM section_items
            WHERE section_id = :section_id
            ORDER BY sort_order ASC, id ASC
        ");
        $stmt->execute([':section_id' => $sectionId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
