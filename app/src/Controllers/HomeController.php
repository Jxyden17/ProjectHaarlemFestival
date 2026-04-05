<?php

namespace App\Controllers;

use App\Mapper\ScheduleViewModelMapper;
use App\Service\Interfaces\IPageService;
use App\Service\Interfaces\IScheduleService;

class HomeController extends BaseController
{

    private IPageService $pageService;
    private IScheduleService $scheduleService;
    private ScheduleViewModelMapper $scheduleViewModelMapper;

        public function __construct(IPageService $pageService, IScheduleService $scheduleService, ScheduleViewModelMapper $scheduleViewModelMapper) 
    {
        $this->pageService = $pageService;
        $this->scheduleService = $scheduleService;
        $this->scheduleViewModelMapper = $scheduleViewModelMapper;
    }
    public function index(): void
    {
        try{
        $pageID = 15;

        $page = $this->pageService->buildPage($pageID);
        $scheduleData = $this->scheduleViewModelMapper->mapScheduleData(
            $this->scheduleService->getScheduleDataForAllEvents('Festival Schedule')
        );

            $viewData = [
            'title' => $page->title,
            'hero'      => $page->getSection('hero'),
            'about'     => $page->getSection('about'),
            'discover'  => $page->getSection('discover_events'),
            'scheduleData' => $scheduleData,
            'guide'     => $page->getSection('guide'),
            'faq'       => $page->getSection('faq'),
            'map'       => $page->getSection('map_section')
        ];
        $this->render('home/index', $viewData);
        } catch (\Exception $e) {
            http_response_code(500);
            $this->render('shared/error', [
                'errorTitle' => 'An error occurred',
                'errorMessage' => 'Sorry, something went wrong while loading the page.',
            ]);
        }
    }
}
