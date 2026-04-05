<?php

namespace App\Controllers;

use App\Mapper\ScheduleViewModelMapper;
use App\Service\Interfaces\IPageService;
use App\Service\Interfaces\IScheduleService;

class TourController extends BaseController
{
    private IPageService $pageService;
    private IScheduleService $scheduleService;
    private ScheduleViewModelMapper $scheduleViewModelMapper;

    public function __construct(
        IPageService $pageService,
        IScheduleService $scheduleService,
        ScheduleViewModelMapper $scheduleViewModelMapper
    ) 
    {
        $this->pageService = $pageService;
        $this->scheduleService = $scheduleService;
        $this->scheduleViewModelMapper = $scheduleViewModelMapper;
    }
    public function index(): void
    {
        $pageId = 1;
        
        $page = $this->pageService->buildPage($pageId);
        if (!$page) {
            http_response_code(404);
            $this->render('shared/error', [
                'errorTitle' => 'Page not found',
                'errorMessage' => 'The page you requested does not exist.',
            ]);
            return;
        }
    $scheduleViewmodel = $this->scheduleViewModelMapper->mapScheduleData(
        $this->scheduleService->getScheduleDataForEvent('A Stroll Through History', 'Tour Schedule')
    );
        $viewData = [
        'title' => $page->title,
        'hero'      => $page->getSection('hero'),
        'stops'     => $page->getSection('tour_overview'),
        'discover'  => $page->getSection('discover'),
        'scheduleData' => $scheduleViewmodel,
        'guide'   => $page->getSection('guide')
    ];
        $this->render('Tour/index', $viewData);
    }

    public function details(): void
    {
        $pageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$pageId) 
            {
                http_response_code(404);
                $this->render('shared/error', [
                    'errorTitle' => 'Page not found',
                    'errorMessage' => 'The page you requested does not exist.',
                ]);
                return;
            }

        $page = $this->pageService->buildPage($pageId);

        $viewData = [
        'title' => $page->title,
        'header'      => $page->getSection('header'),
        'history'     => $page->getSection('history'),
        'didYouKnow'  => $page->getSection('did_you_know'),
        'openingTime' => $page->getSection('openings_time')
    ];

        $this->render('Tour/details', $viewData);
    }
}
