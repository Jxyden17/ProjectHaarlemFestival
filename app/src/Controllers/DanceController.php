<?php

namespace App\Controllers;

use App\Mapper\DanceViewModelMapper;
use App\Mapper\ScheduleViewModelMapper;
use App\Service\Interfaces\IDanceService;

class DanceController extends BaseController
{
    private IDanceService $danceService;
    private DanceViewModelMapper $danceViewModelMapper;
    private ScheduleViewModelMapper $scheduleViewModelMapper;

    // Stores the public dance dependencies so page rendering stays thin and orchestration lives in services.
    public function __construct(
        IDanceService $danceService,
        DanceViewModelMapper $danceViewModelMapper,
        ScheduleViewModelMapper $scheduleViewModelMapper
    )
    {
        $this->danceService = $danceService;
        $this->danceViewModelMapper = $danceViewModelMapper;
        $this->scheduleViewModelMapper = $scheduleViewModelMapper;
    }

    // Renders the public dance landing page so visitors get schedule and performer content in one view.
    public function index(): void
    {
        $danceIndexData = $this->danceService->getDanceIndexData();
        $danceIndexViewModel = $this->danceViewModelMapper->buildIndexViewModel(
            $danceIndexData,
            $this->scheduleViewModelMapper->mapScheduleData($danceIndexData->schedule)
        );

        $this->render('dance/index', [
            'title' => $danceIndexViewModel->pageTitle,
            'danceIndexViewModel' => $danceIndexViewModel,
        ]);
    }

    // Renders one public dance detail page by slug so invalid slugs return a 404 instead of a broken page. Example: slug 'urban-echo' -> detail view.
    public function detail(array $vars = []): void
    {
        $pageSlug = trim((string)($vars['pageSlug'] ?? ''));
        $detailData = $this->danceService->getDanceDetailData($pageSlug);
        if ($detailData === null) {
            http_response_code(404);
            echo 'Dance detail page not found.';
            return;
        }

        $detailViewModel = $this->danceViewModelMapper->buildDetailViewModel(
            $detailData,
            $this->scheduleViewModelMapper->mapScheduleRows($detailData->scheduleSessions)
        );

        $this->render('dance/detail', [
            'title' => $detailViewModel->pageTitle,
            'danceDetailViewModel' => $detailViewModel,
        ]);
    }
}
