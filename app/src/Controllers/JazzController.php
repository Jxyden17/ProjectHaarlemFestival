<?php

namespace App\Controllers;

use App\Mapper\ScheduleViewModelMapper;
use App\Models\ViewModels\Jazz\JazzIndexViewModel;
use App\Service\Interfaces\IJazzService;
use App\Service\Interfaces\IScheduleService;
use App\Service\Interfaces\IPageService;


class JazzController extends BaseController
{
    private IJazzService $jazzService;
    private IScheduleService $scheduleService;
    private ScheduleViewModelMapper $scheduleViewModelMapper;
    private IPageService $pageService;

    public function __construct(
        IScheduleService $scheduleService,
        IJazzService $jazzService,
        ScheduleViewModelMapper $scheduleViewModelMapper,
        IPageService $pageService
    )
    {
        $this->jazzService = $jazzService;
        $this->scheduleService = $scheduleService;
        $this->scheduleViewModelMapper = $scheduleViewModelMapper;
        $this->pageService = $pageService;
    }
   public function index()
    {
        $schedule = $this->scheduleViewModelMapper->mapScheduleData(
            $this->scheduleService->getScheduleDataForEvent('Jazz', 'Jazz Schedule')
        );
        $jazzPerformers=$this->jazzService->getAllJazzPerformers();
        $page=$this->pageService->buildPage(28);
        $jazzViewModel=new JazzIndexViewModel($schedule,$jazzPerformers,$page);
        $this->render("/Jazz/index",['jazzViewModel' => $jazzViewModel]);
    }


}
?>
