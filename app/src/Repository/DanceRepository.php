<?php

namespace App\Repository;

use App\Models\Database;
use App\Models\EventModel;
use App\Models\Page\Page;
use App\Models\Page\Section;
use App\Models\Page\SectionItem;
use App\Models\VenueModel;
use PDO;

class DanceRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findDanceEvent(): ?EventModel
    {
        $stmt = $this->db->prepare('SELECT id, name, description FROM events WHERE name = :name LIMIT 1');
        $stmt->execute([':name' => 'Dance']);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new EventModel((int)$row['id'], (string)$row['name'], isset($row['description']) ? (string)$row['description'] : null);
    }

    public function countSessionsByEventId(int $eventId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) AS total FROM sessions WHERE event_id = :event_id');
        $stmt->execute([':event_id' => $eventId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return isset($row['total']) ? (int)$row['total'] : 0;
    }

    public function countDistinctVenuesByEventId(int $eventId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(DISTINCT venue_id) AS total FROM sessions WHERE event_id = :event_id');
        $stmt->execute([':event_id' => $eventId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return isset($row['total']) ? (int)$row['total'] : 0;
    }

    public function getVenuesByEventId(int $eventId): array {
        $stmt = $this->db->prepare('
            SELECT id, event_id, venue_name, address, venue_type, created_at
            FROM venues
            WHERE event_id = :event_id
            ORDER BY venue_name ASC
        ');
        $stmt->execute([':event_id' => $eventId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(static fn(array $row): VenueModel => new VenueModel((int)$row['id'], (int)$row['event_id'], (string)$row['venue_name'], isset($row['address']) ? (string)$row['address'] : null, isset($row['venue_type']) ? (string)$row['venue_type'] : null, isset($row['created_at']) ? (string)$row['created_at'] : null), $rows);
    }

    public function getDanceHomePage(): Page
    {
        $page = new Page('Dance Home', 'dance-home');
        $page->sections = [];

        $pageId = $this->findPageIdBySlug('dance-home');
        if ($pageId === null) {
            return $page;
        }

        $rows = $this->getPageSectionsByPageId($pageId);

        if (empty($rows)) {
            return $page;
        }

        $sectionIds = array_map(static fn(array $row): int => (int)$row['id'], $rows);
        $itemsBySection = $this->getItemsBySectionIds($sectionIds);

        foreach ($rows as $row) {
            $sectionId = (int)$row['id'];
            $section = $this->mapSectionRow($row);

            foreach (($itemsBySection[$sectionId] ?? []) as $itemRow) {
                $section->addItem($this->mapSectionItemRow($itemRow));
            }

            $page->sections[] = $section;
        }

        return $page;
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
            null,
            null,
            null,
            (int)($row['order_index'] ?? 0)
        );
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

    public function ensureDanceHomePage(int $eventId): int
    {
        $pageId = $this->findPageIdBySlug('dance-home');
        if ($pageId !== null) {
            return $pageId;
        }

        $stmt = $this->db->prepare('INSERT INTO pages (event_id, slug, page_name) VALUES (:event_id, :slug, :page_name)');
        $stmt->execute([':event_id' => $eventId, ':slug' => 'dance-home', ':page_name' => 'Dance Home']);

        return (int)$this->db->lastInsertId();
    }

    public function saveOrUpdateSection(int $pageId, string $sectionType, ?string $title, ?string $subtitle, ?string $description, int $orderIndex): int {
        $stmt = $this->db->prepare('SELECT id FROM page_sections WHERE page_id = :page_id AND section_type = :section_type LIMIT 1');
        $stmt->execute([':page_id' => $pageId, ':section_type' => $sectionType]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $update = $this->db->prepare('UPDATE page_sections SET title = :title, subtitle = :subtitle, description = :description, order_index = :order_index WHERE id = :id');
            $update->execute([':title' => $title, ':subtitle' => $subtitle, ':description' => $description, ':order_index' => $orderIndex, ':id' => (int)$row['id']]);
            return (int)$row['id'];
        }

        $insert = $this->db->prepare('INSERT INTO page_sections (page_id, section_type, title, subtitle, description, order_index) VALUES (:page_id, :section_type, :title, :subtitle, :description, :order_index)');
        $insert->execute([':page_id' => $pageId, ':section_type' => $sectionType, ':title' => $title, ':subtitle' => $subtitle, ':description' => $description, ':order_index' => $orderIndex]);

        return (int)$this->db->lastInsertId();
    }

    private function getItemsBySectionIds(array $sectionIds): array
    {
        if (empty($sectionIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($sectionIds), '?'));
        $stmt = $this->db->prepare('SELECT id, section_id, item_category, title, content, image_path, link_url, order_index FROM section_items WHERE section_id IN (' . $placeholders . ') ORDER BY order_index ASC');
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

    public function replaceSectionItems(int $sectionId, array $items): void
    {
        $deleteStmt = $this->db->prepare('DELETE FROM section_items WHERE section_id = :section_id');
        $deleteStmt->execute([':section_id' => $sectionId]);

        if (empty($items)) {
            return;
        }

        $insertStmt = $this->db->prepare('INSERT INTO section_items (section_id, title, content, image_path, link_url, order_index, item_category) VALUES (:section_id, :title, :content, :image_path, :link_url, :order_index, :item_category)');

        foreach ($items as $item) {
            $insertStmt->execute([':section_id' => $sectionId, ':title' => $item['title'], ':content' => $item['content'], ':image_path' => $item['image_path'], ':link_url' => $item['link_url'], ':order_index' => $item['order_index'], ':item_category' => $item['item_category']]);
        }
    }

}
