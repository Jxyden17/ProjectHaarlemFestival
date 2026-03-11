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
        $this->render('dance/index', [
            'title' => 'Dance',
            'danceIndexViewModel' => $this->danceViewModelMapper->buildIndexViewModel(),
        ]);
    }

    public function detail(array $vars = []): void
    {
        $publicSlug = trim((string)($vars['detailSlug'] ?? ''));
        $detailMeta = $this->danceService->getDanceDetailPageByPublicSlug($publicSlug);

        if (!$detailMeta instanceof EventDetailPageModel) {
            http_response_code(404);
            echo 'Dance detail page not found.';
            return;
        }

        $detailViewModel = $this->danceViewModelMapper->buildDetailViewModel($detailMeta);

        $this->render('dance/detail', [
            'title' => $detailViewModel->performerName === '' ? 'Dance Detail' : $detailViewModel->performerName,
            'danceDetailViewModel' => $detailViewModel,
        ]);
    }
}
