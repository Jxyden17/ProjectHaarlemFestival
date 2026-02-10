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

    public function getPageDataByTitle($slug)
    {
      $sql = "SELECT 
                p.page_name AS page_title, 
                ps.id AS section_id, 
                ps.section_type, 
                ps.title AS section_title,
                si.id AS item_id, 
                si.title AS item_title, 
                si.content, 
                si.image_path, 
                si.link_url
            FROM pages p
            JOIN page_sections ps ON p.id = ps.page_id
            LEFT JOIN section_items si ON ps.id = si.section_id
            WHERE p.slug = :slug
            ORDER BY ps.order_index ASC, si.order_index ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug' => $slug]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}