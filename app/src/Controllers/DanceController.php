<?php

namespace App\Controllers;

use App\Service\Interfaces\IScheduleService;

class DanceController extends BaseController
{
    private IScheduleService $scheduleService;

    public function __construct(IScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    public function index(): void
    {
        $this->render('dance/index', [
            'title' => 'Dance',
            'scheduleData' => $this->scheduleService->getDanceScheduleData(),
        ]);
    }
}
