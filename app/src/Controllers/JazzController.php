<?php

namespace App\Controllers;

use App\Models\ViewModels\Jazz\JazzIndexViewModel;
use App\Service\Interfaces\IJazzService;
use App\Service\Interfaces\IScheduleService;


class JazzController extends BaseController
{
    private IJazzService $jazzService;
    private IScheduleService $scheduleService;

    public function __construct( IScheduleService $scheduleService, IJazzService $jazzService)
    {
        $this->jazzService = $jazzService;
        $this->scheduleService = $scheduleService;
    }
   public function index()
    {
        $schedule = $this->scheduleService->getScheduleDataForEvent('Jazz', 'Jazz Schedule');
        $jazzPerformers=$this->jazzService->getAllJazzPerformers();
        $jazzViewModel=new JazzIndexViewModel($schedule,$jazzPerformers);
        $this->render("/Jazz/index",['jazzViewModel' => $jazzViewModel]);
    }


}
?>