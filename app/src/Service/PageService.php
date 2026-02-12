<?php

namespace App\Service;

use App\Repository\Interfaces\IPageRepository;
use App\Service\Interfaces\IPageService;
use App\Models\Page\Page;
use App\Models\Page\Section;
use App\Models\Page\SectionItem;

class PageService implements IPageService
{
    private IPageRepository $pageRepo;

    public function __construct(IPageRepository $pageRepo)
    {
        $this->pageRepo = $pageRepo;
    }

    public function buildPage(string $slug): ?Page
    {
     $rows = $this->pageRepo->getPageDataByTitle($slug);
     if (!$rows) return null;

     $page = new Page($rows[0]['page_title'], $slug);
     $sections = [];

     foreach ($rows as $row) 
        {
          $sId = $row['section_id'];

          if(!isset($sections[$sId]))
            {
                $sections[$sId] = new Section($sId, $row['section_type'], $row['section_title'], $row['subtitle'] ?? '', $row['description'] ?? '');
            }
            $this->addItemToSection($sections[$sId], $row);
        }
        $page->sections= $sections;
        return $page;
    }

    private function addItemToSection(Section $section, array $row): void 
    {
        if (!$row['item_id']) return;

        $item = new SectionItem(
            (int)$row['item_id'],
            $row['item_title'],
            $row['content'],
            $row['image_path'],
            $row['link_url'] ?? null,
            $row['item_category']
        );
            
        $section->addItem($item);
    }
}