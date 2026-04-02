<?php

namespace App\Mapper;

use App\Models\Page\Page;
use App\Models\Page\Section;
use App\Models\Page\SectionItem;

class PageMapper
{
    public function mapPageRows(array $rows): Page
    {
        $firstRow = $rows[0];
        $page = new Page(
            (string)($firstRow['page_name'] ?? ''),
            (string)($firstRow['slug'] ?? ''),
            (int)($firstRow['page_id'] ?? 0)
        );
        $sectionsById = [];

        foreach ($rows as $row) {
            $sectionId = (int)($row['section_id'] ?? 0);
            if ($sectionId <= 0) {
                continue;
            }

            if (!isset($sectionsById[$sectionId])) {
                $sectionsById[$sectionId] = $this->mapSectionRow($row);
            }

            $itemId = (int)($row['item_id'] ?? 0);
            if ($itemId > 0) {
                $sectionsById[$sectionId]->addItem($this->mapSectionItemRow($row));
            }
        }

        $page->sections = array_values($sectionsById);

        return $page;
    }

    public function mapSectionRow(array $row): Section
    {
        return new Section(
            (int)($row['section_id'] ?? 0),
            (string)($row['section_type'] ?? ''),
            (string)($row['section_title'] ?? ''),
            (string)($row['section_subtitle'] ?? ''),
            (string)($row['section_description'] ?? '')
        );
    }

    public function mapSectionItemRow(array $row): SectionItem
    {
        return new SectionItem(
            (int)($row['item_id'] ?? 0),
            (string)($row['item_title'] ?? ''),
            isset($row['content']) ? (string)$row['content'] : null,
            isset($row['image_path']) ? (string)$row['image_path'] : null,
            isset($row['link_url']) ? (string)$row['link_url'] : null,
            (string)($row['item_category'] ?? ''),
            isset($row['duration']) ? (string)$row['duration'] : null,
            isset($row['icon_class']) ? (string)$row['icon_class'] : null,
            isset($row['item_subtitle']) ? (string)$row['item_subtitle'] : null,
            (int)($row['item_order_index'] ?? 0)
        );
    }
}
