<?php

namespace App\Controllers\Cms;

use App\Mapper\CmsDanceMapper;
use App\Controllers\BaseController;
use App\Models\Requests\Cms\DanceDetailContentRequest;
use App\Models\Requests\Cms\DanceHomeContentRequest;
use App\Service\Cms\Interfaces\ICmsDanceService;

class CmsDanceContentController extends BaseController
{
    private ICmsDanceService $danceService;
    private CmsDanceMapper $cmsDanceMapper;

    public function __construct(ICmsDanceService $danceService, CmsDanceMapper $cmsDanceMapper)
    {
        $this->danceService = $danceService;
        $this->cmsDanceMapper = $cmsDanceMapper;
    }

    public function index(): void
    {
        $this->requireAdmin();
        $contentViewModel = $this->danceService->getDanceHomeFormData();

        $this->renderCms('cms/events/dance-home', [
            'title' => 'Dance Home Content',
            'contentViewModel' => $contentViewModel,
            'success' => isset($_GET['saved']),
        ]);
    }

    public function update(): void
    {
        $this->requireAdmin();
        $request = DanceHomeContentRequest::fromArray($_POST);

        try {
            $this->danceService->saveDanceHomePage($request);
            header('Location: /cms/events/dance-home?saved=1');
            exit;
        } catch (\Throwable $e) {
            $contentViewModel = $this->cmsDanceMapper->mapHomeRequestToContentViewModel($request);
            $this->renderCms('cms/events/dance-home', [
                'title' => 'Dance Home Content',
                'contentViewModel' => $contentViewModel,
                'error' => $e->getMessage(),
                'success' => false,
            ]);
        }
    }

    public function detail(array $vars = []): void
    {
        $this->requireAdmin();

        $detailSlug = trim((string)($vars['detailSlug'] ?? ''));
        $contentViewModel = $this->danceService->getDanceDetailFormData($detailSlug);

        $this->renderCms('cms/events/dance-detail', [
            'title' => $contentViewModel->editorTitle,
            'contentViewModel' => $contentViewModel,
            'formAction' => '/cms/events/dance-detail/' . $contentViewModel->detailSlug,
            'success' => isset($_GET['saved']),
        ]);
    }

    public function updateDetail(array $vars = []): void
    {
        $this->requireAdmin();

        $detailSlug = trim((string)($vars['detailSlug'] ?? ''));
        $request = DanceDetailContentRequest::fromArray($_POST);

        try {
            $this->danceService->saveDanceDetailPage($detailSlug, $request);
            header('Location: /cms/events/dance-detail/' . rawurlencode($detailSlug) . '?saved=1');
            exit;
        } catch (\Throwable $e) {
            $baseViewModel = $this->danceService->getDanceDetailFormData($detailSlug);
            $contentViewModel = $this->cmsDanceMapper->mapDetailRequestToContentViewModel($request, $baseViewModel);
            $this->renderCms('cms/events/dance-detail', [
                'title' => $contentViewModel->editorTitle,
                'contentViewModel' => $contentViewModel,
                'formAction' => '/cms/events/dance-detail/' . $contentViewModel->detailSlug,
                'error' => $e->getMessage(),
                'success' => false,
            ]);
        }
    }
}
