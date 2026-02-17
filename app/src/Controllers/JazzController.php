<?php

namespace App\Controllers;

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
        $jazzEvents=$this->jazzService->getAllJazzEvents();
        $schedule = $this->scheduleService->getScheduleDataForEvent('Jazz', 'Jazz Schedule');
        $this->render("/Jazz/index",['jazzEvents' => $jazzEvents,'schedule'=>$schedule]);

    }


}
?>