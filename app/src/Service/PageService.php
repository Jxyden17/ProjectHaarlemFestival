<?php

namespace App\Service;

use App\Models\Page\Page;
use App\Models\Page\Section;
use App\Models\Page\SectionItem;
use App\Repository\Interfaces\IPageRepository;
use App\Service\Interfaces\IPageService;

class PageService implements IPageService
{
    private IPageRepository $pageRepo;

    public function __construct(IPageRepository $pageRepo)
    {
        $this->pageRepo = $pageRepo;
    }

    public function buildPage(int $pageId): ?Page
    {
        $pageRow = $this->pageRepo->findPageRowById($pageId);
        if ($pageRow === null) {
            return null;
        }

        return $this->mapPageRow($pageRow);
    }

    public function getPageBySlug(string $slug, string $fallbackTitle = ''): Page
    {
        $pageRow = $this->pageRepo->findPageRowBySlug($slug);
        if ($pageRow === null) {
            return new Page($fallbackTitle, $slug);
        }

        return $this->mapPageRow($pageRow);
    }

    private function mapPageRow(array $pageRow): Page
    {
        $pageId = (int)($pageRow['id'] ?? 0);
        $page = new Page((string)($pageRow['page_name'] ?? ''), (string)($pageRow['slug'] ?? ''));
        $page->sections = $this->buildSectionsForPage($pageId);

        return $page;
    }

    private function buildSectionsForPage(int $pageId): array
    {
        $sectionRows = $this->pageRepo->getPageSectionsByPageId($pageId);
        if (empty($sectionRows)) {
            return [];
        }

        $sectionIds = array_map(static fn(array $row): int => (int)($row['id'] ?? 0), $sectionRows);
        $itemsBySection = $this->pageRepo->getItemsBySectionIds($sectionIds);
        $sections = [];

        foreach ($sectionRows as $row) {
            $sectionId = (int)($row['id'] ?? 0);
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
