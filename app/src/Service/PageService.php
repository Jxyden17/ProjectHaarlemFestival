<?php

namespace App\Service;

use App\Repository\Interfaces\IPageRepository;
use App\Service\Interfaces\IPageService;
use App\Models\Page\Page;

class PageService implements IPageService
{
    private IPageRepository $pageRepo;

    public function __construct(IPageRepository $pageRepo)
    {
        $this->pageRepo = $pageRepo;
    }

    public function buildPage(int $pageId): ?Page
    {
        return $this->pageRepo->getPageById($pageId);
    }
}
