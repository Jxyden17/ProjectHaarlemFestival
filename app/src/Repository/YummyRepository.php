<?php

namespace App\Repository;

use App\Repository\Interfaces\IYummyRepository;
use App\Models\Database;
use App\Models\Event\EventModel;
use App\Models\Event\VenueModel;
use App\Models\Page\Page;
use App\Models\Page\Section;
use App\Models\Page\SectionItem;
use PDO;

class YummyRepository implements IYummyRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findYummyEvent(): ?EventModel
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, description 
             FROM events 
             WHERE name = :name 
             LIMIT 1'
        );

        $stmt->execute([':name' => 'Yummy']);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new EventModel(
            (int)$row['id'],
            (string)$row['name'],
            $row['description'] ?? null
        );
    }

    public function getVenuesByEventId(int $eventId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, event_id, venue_name, address, venue_type, created_at
             FROM venues
             WHERE event_id = :event_id
             ORDER BY venue_name ASC'
        );

        $stmt->execute([':event_id' => $eventId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            static fn(array $row): VenueModel => new VenueModel(
                (int)$row['id'],
                (int)$row['event_id'],
                (string)$row['venue_name'],
                $row['address'] ?? null,
                $row['venue_type'] ?? null,
                $row['created_at'] ?? null
            ),
            $rows
        );
    }

    public function getPageBySlug(string $slug): ?Page
    {
        $stmt = $this->db->prepare("SELECT * FROM pages WHERE slug = :slug");
        $stmt->execute(['slug' => $slug]);
        $pageData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pageData) return null;

        $page = new Page(
            $pageData['page_name'] ?? '',
            $pageData['slug'] ?? ''
        );

        $sectionStmt = $this->db->prepare("
            SELECT * FROM page_sections 
            WHERE page_id = :page_id
            ORDER BY order_index
        ");
        $sectionStmt->execute(['page_id' => $pageData['id']]);
        $sections = $sectionStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($sections as $sectionData) {
            $section = new Section(
                (int)($sectionData['id'] ?? 0),
                (string)($sectionData['section_type'] ?? ''),
                (string)($sectionData['title'] ?? ''),
                (string)($sectionData['subtitle'] ?? ''),
                (string)($sectionData['description'] ?? '')
            );

            $items = $this->getSectionItems($section->id);
            foreach ($items as $item) {
                $section->addItem($item);
            }

            $page->sections[] = $section;
        }

        return $page;
    }

    private function getSectionItems(int $sectionId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM section_items
            WHERE section_id = :id
            ORDER BY order_index
        ");

        $stmt->execute(['id' => $sectionId]);
        $items = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $items[] = new SectionItem(
                (int)$row['id'],
                $row['title'] ?? '',
                $row['content'] ?? null,
                $row['image_path'] ?? null,
                $row['link_url'] ?? null,
                $row['item_category'] ?? '',
                $row['duration'] ?? null,
                $row['icon_class'] ?? null,
                $row['item_subtitle'] ?? null,
                (int)($row['order_index'] ?? 0)
            );
        }

        return $items;
    }
}