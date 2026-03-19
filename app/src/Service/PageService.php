<?php

namespace App\Service;

use App\Models\Page\Page;
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
        return $this->pageRepo->findPageById($pageId);
    }

    public function getPageBySlug(string $slug, string $fallbackTitle = ''): Page
    {
        $page = $this->pageRepo->findPageBySlug($slug);
        if ($page === null) {
            return new Page($fallbackTitle, $slug);
        }

        return $page;
    }
}
