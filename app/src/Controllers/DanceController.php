<?php

namespace App\Controllers;

use App\Mapper\DanceViewModelMapper;
use App\Models\Event\EventDetailPageModel;
use App\Service\Interfaces\IDanceService;

class DanceController extends BaseController
{
    private IDanceService $danceService;
    private DanceViewModelMapper $danceViewModelMapper;

    public function __construct(IDanceService $danceService, DanceViewModelMapper $danceViewModelMapper)
    {
        $this->danceService = $danceService;
        $this->danceViewModelMapper = $danceViewModelMapper;
    }

    public function index(): void
    {
        $danceIndexViewModel = $this->danceViewModelMapper->buildIndexViewModel();

        $this->render('dance/index', [
            'title' => $danceIndexViewModel->pageTitle,
            'danceIndexViewModel' => $danceIndexViewModel,
        ]);
    }

    public function detail(array $vars = []): void
    {
        $detailSlug = trim((string)($vars['detailSlug'] ?? ''));
        $detailMeta = $this->danceService->getDanceDetailPageBySlug($detailSlug);

        if (!$detailMeta instanceof EventDetailPageModel) {
            http_response_code(404);
            echo 'Dance detail page not found.';
            return;
        }

        $detailViewModel = $this->danceViewModelMapper->buildDetailViewModel($detailMeta);

        $this->render('dance/detail', [
            'title' => $detailViewModel->pageTitle,
            'danceDetailViewModel' => $detailViewModel,
        ]);
    }
}
