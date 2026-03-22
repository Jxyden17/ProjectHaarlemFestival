<?php

namespace App\Controllers;

use App\Mapper\DanceViewModelMapper;
use App\Mapper\ScheduleViewModelMapper;
use App\Models\Event\EventDetailPageModel;
use App\Service\Interfaces\IDanceService;

class DanceController extends BaseController
{
    private IDanceService $danceService;
    private DanceViewModelMapper $danceViewModelMapper;
    private ScheduleViewModelMapper $scheduleViewModelMapper;

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

    public function detail(array $vars = []): void
    {
        $pageSlug = trim((string)($vars['pageSlug'] ?? ''));
        $detailMeta = $this->danceService->getDanceDetailPageBySlug($pageSlug);

        if (!$detailMeta instanceof EventDetailPageModel) {
            http_response_code(404);
            echo 'Dance detail page not found.';
            return;
        }

        $detailData = $this->danceService->getDanceDetailData($detailMeta);
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
