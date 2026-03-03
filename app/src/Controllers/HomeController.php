<?php

namespace App\Controllers;

use App\Service\Interfaces\IPageService;
use App\Service\Interfaces\IScheduleService;

class HomeController extends BaseController
{

    private IPageService $pageService;
    private IScheduleService $scheduleService;

        public function __construct(IPageService $pageService, IScheduleService $scheduleService) 
    {
        $this->pageService = $pageService;
        $this->scheduleService = $scheduleService;
    }
    public function index(): void
    {
        $pageID = 15;

        $page = $this->pageService->buildPage($pageID);

            $viewData = [
            'pageTitle' => $page->title,
            'hero'      => $page->getSection('hero'),
            'about'     => $page->getSection('about'),
            'discover'  => $page->getSection('discover_events'),
            'guide'     => $page->getSection('guide'),
            'faq'       => $page->getSection('faq'),
            'map'       => $page->getSection('map_section')
        ];
        $this->render('home/index', $viewData);
    }
}
