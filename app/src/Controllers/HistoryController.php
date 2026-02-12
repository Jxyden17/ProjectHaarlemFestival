<?php

namespace App\Controllers;

use App\Service\Interfaces\IPageService;

class HistoryController extends BaseController
{
    private IPageService $pageService;

    public function __construct(IPageService $pageService) 
    {
        $this->pageService = $pageService;
    }
    public function index(): void
    {
        $slug = 'history-stroll';
        
        $page = $this->pageService->buildPage($slug);
        if (!$page) {
            $this->render('errors/404');
            return;
        }

        $viewData = [
        'pageTitle' => $page->title,
        'hero'      => $page->getSection('hero'),
        'stops'     => $page->getSection('stop'),
        'discover'  => $page->getSection('discover'),
        'schedule' => $page->getSection('schedule'),
        'guides'   => $page->getSection('route_guides')
    ];
        $this->render('history/index', $viewData);
    }
}
