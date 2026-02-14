<?php

namespace App\Controllers;

use App\Models\ViewModels\Dance\DanceIndexViewModel;
use App\Service\Interfaces\IDanceService;
use App\Service\Interfaces\IScheduleService;

class DanceController extends BaseController
{
    private IScheduleService $scheduleService;
    private IDanceService $danceService;

    public function __construct(IScheduleService $scheduleService, IDanceService $danceService)
    {
        $this->scheduleService = $scheduleService;
        $this->danceService = $danceService;
    }

    public function index(): void
    {
        $bannerStats = $this->danceService->getDanceBannerStats();
        $schedule = $this->scheduleService->getScheduleDataForEvent('Dance', 'DANCE! Festival Schedule');
        $venues = $this->danceService->getDanceVenues();

        $this->render('dance/index', [
            'title' => 'Dance',
            'danceIndexViewModel' => new DanceIndexViewModel(
                $schedule,
                $bannerStats,
                $venues
            ),
        ]);
    }
}
