<?php

namespace App\Service;

use App\Mapper\PageMapper;
use App\Models\Page\Page;
use App\Repository\Interfaces\IPageRepository;
use App\Service\Interfaces\IPageService;

class PageService implements IPageService
{
    private IPageRepository $pageRepo;
    private PageMapper $pageMapper;

    public function __construct(IPageRepository $pageRepo, ?PageMapper $pageMapper = null)
    {
        $this->pageRepo = $pageRepo;
        $this->pageMapper = $pageMapper ?? new PageMapper();
    }

    public function buildPage(int $pageId): ?Page
    {
        $pageGraphRows = $this->pageRepo->findPageGraphRowsById($pageId);
        if (empty($pageGraphRows)) {
            return null;
        }

        return $this->pageMapper->mapPageGraphRows($pageGraphRows);
    }

    public function getPageBySlug(string $slug, string $fallbackTitle = ''): Page
    {
        $pageGraphRows = $this->pageRepo->findPageGraphRowsBySlug($slug);
        if (empty($pageGraphRows)) {
            return new Page($fallbackTitle, $slug);
        }

        return $this->pageMapper->mapPageGraphRows($pageGraphRows);
    }
}
