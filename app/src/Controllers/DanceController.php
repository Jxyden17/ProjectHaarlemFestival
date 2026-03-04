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
        $homeContent = $this->danceService->getDanceHomePage();
        $bannerStats = $this->danceService->getDanceBannerStats();
        $scheduleSection = $homeContent->getSection('dance_schedule');
        $scheduleTitle = $scheduleSection !== null ? $scheduleSection->title : '';
        $schedule = $this->scheduleService->getScheduleDataForEvent('Dance', $scheduleTitle);
        $venues = $this->danceService->getDanceVenues();
        $performers = $this->danceService->getDancePerformers();

        $this->render('dance/index', [
            'title' => 'Dance',
            'danceIndexViewModel' => new DanceIndexViewModel(
                $schedule,
                $bannerStats,
                $venues,
                $performers,
                $homeContent
            ),
        ]);
    }
}
