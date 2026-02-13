<?php

namespace App\Controllers;

use App\Service\Interfaces\IPageService;

class TourController extends BaseController
{
    private IPageService $pageService;

    public function __construct(IPageService $pageService) 
    {
        $this->pageService = $pageService;
    }
    public function index(): void
    {
        $pageId = 1;
        
        $page = $this->pageService->buildPage($pageId);
        if (!$page) {
            $this->render('errors/404');
            return;
        }

        $viewData = [
        'pageTitle' => $page->title,
        'hero'      => $page->getSection('hero'),
        'stops'     => $page->getSection('tour_overview'),
        'discover'  => $page->getSection('discover'),
        'schedule' => $page->getSection('schedule'),
        'guide'   => $page->getSection('guide')
    ];
        $this->render('Tour/index', $viewData);
    }

    public function details(): void
    {
        $pageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$pageId) 
            {
                $this->render('errors/404');
                return;
            }

        $page = $this->pageService->buildPage($pageId);

        $viewData = [
        'pageTitle' => $page->title,
        'header'      => $page->getSection('header'),
        'history'     => $page->getSection('history'),
        'did_you_know'  => $page->getSection('did_you_know'),
        'contact_info' => $page->getSection('contact_info')
    ];

        $this->render('Tour/details', $viewData);
    }
}
