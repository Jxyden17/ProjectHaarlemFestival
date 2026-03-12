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

    public function updateAPI(array $vars = []): void
    {
        $this->requireAdmin();
        $request = DanceHomeContentRequest::fromArray($_POST);

        try {
            $this->danceService->saveDanceHomePage($request);
            $this->json([
                'success' => true,
                'message' => 'Dance home content updated.',
            ]);
        } catch (\Throwable $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function detail(array $vars = []): void
    {
        $this->requireAdmin();

        $pageSlug = trim((string)($vars['pageSlug'] ?? ''));
        $contentViewModel = $this->danceService->getDanceDetailFormData($pageSlug);

        $this->renderCms('cms/events/dance-detail', [
            'title' => $contentViewModel->editorTitle,
            'contentViewModel' => $contentViewModel,
            'formAction' => '/cms/events/dance-detail/' . $contentViewModel->pageSlug,
            'success' => isset($_GET['saved']),
        ]);
    }

    public function updateDetail(array $vars = []): void
    {
        $this->requireAdmin();

        $pageSlug = trim((string)($vars['pageSlug'] ?? ''));
        $request = DanceDetailContentRequest::fromArray($_POST);

        try {
            $this->danceService->saveDanceDetailPage($pageSlug, $request);
            header('Location: /cms/events/dance-detail/' . rawurlencode($pageSlug) . '?saved=1');
            exit;
        } catch (\Throwable $e) {
            $baseViewModel = $this->danceService->getDanceDetailFormData($pageSlug);
            $contentViewModel = $this->cmsDanceMapper->mapDetailRequestToContentViewModel($request, $baseViewModel);
            $this->renderCms('cms/events/dance-detail', [
                'title' => $contentViewModel->editorTitle,
                'contentViewModel' => $contentViewModel,
                'formAction' => '/cms/events/dance-detail/' . $contentViewModel->pageSlug,
                'error' => $e->getMessage(),
                'success' => false,
            ]);
        }
    }

    public function updateDetailAPI(array $vars = []): void
    {
        $this->requireAdmin();

        $pageSlug = trim((string)($vars['pageSlug'] ?? ''));
        $request = DanceDetailContentRequest::fromArray($_POST);

        try {
            $this->danceService->saveDanceDetailPage($pageSlug, $request);
            $this->json([
                'success' => true,
                'message' => 'Dance detail content updated.',
            ]);
        } catch (\Throwable $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
