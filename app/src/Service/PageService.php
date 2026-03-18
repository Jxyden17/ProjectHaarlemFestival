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

    public function __construct(IPageRepository $pageRepo, PageMapper $pageMapper)
    {
        $this->pageRepo = $pageRepo;
        $this->pageMapper = $pageMapper;
    }

    public function buildPage(int $pageId): ?Page
    {
        $pageRows = $this->pageRepo->findPageRowsById($pageId);
        if (empty($pageRows)) {
            return null;
        }

        return $this->pageMapper->mapPageRows($pageRows);
    }

    public function getPageBySlug(string $slug, string $fallbackTitle = ''): Page
    {
        $pageRows = $this->pageRepo->findPageRowsBySlug($slug);
        if (empty($pageRows)) {
            return new Page($fallbackTitle, $slug);
        }

        return $this->pageMapper->mapPageRows($pageRows);
    }

    public function getPagesByEventId(int $eventId): array
    {
        return $this->pageRepo->findPagesByEventId($eventId);
    }
}
