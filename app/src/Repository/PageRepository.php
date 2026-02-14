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

    public function getPageDataById($pageId)
    {
      $sql = "SELECT 
                    p.page_name AS page_title, 
                    p.id,
                    ps.id AS section_id, 
                    ps.section_type, 
                    ps.title AS section_title,
                    ps.subtitle,
                    ps.description,
                    si.id AS item_id, 
                    si.title AS item_title,
                    si.item_subtitle,
                    si.content, 
                    si.image_path, 
                    si.link_url,
                    si.item_category,
                    si.duration,
                    si.icon_class,
                    si.order_index
                FROM pages p
                JOIN page_sections ps ON p.id = ps.page_id
                LEFT JOIN section_items si ON ps.id = si.section_id
                WHERE p.id = :pageId
                ORDER BY ps.order_index ASC, si.order_index ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['pageId' => $pageId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}