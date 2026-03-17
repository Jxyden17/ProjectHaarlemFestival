<?php

namespace App\Controllers\Cms;

use App\Mapper\CmsDanceViewModelMapper;
use App\Controllers\BaseController;
use App\Models\Requests\UpdateDanceDetailRequest;
use App\Models\Requests\UpdateDanceHomeRequest;
use App\Service\Cms\Interfaces\ICmsDanceService;

class CmsDanceController extends BaseController
{
    private ICmsDanceService $danceService;
    private CmsDanceViewModelMapper $cmsDanceViewModelMapper;

    public function __construct(ICmsDanceService $danceService, CmsDanceViewModelMapper $cmsDanceViewModelMapper)
    {
        $this->danceService = $danceService;
        $this->cmsDanceViewModelMapper = $cmsDanceViewModelMapper;
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
        $request = UpdateDanceHomeRequest::fromArray($_POST);

        try {
            $this->danceService->saveDanceHomePage($request);
            header('Location: /cms/events/dance-home?saved=1');
            exit;
        } catch (\Throwable $e) {
            $contentViewModel = $this->cmsDanceViewModelMapper->mapHomeRequestToContentViewModel($request);
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
        $request = UpdateDanceHomeRequest::fromArray($_POST);

        try {
            $this->danceService->saveDanceHomePage($request);
            $this->json(['success' => true,'message' => 'Dance home content updated.',]);
        } catch (\Throwable $e) {
            $this->json(['success' => false, 'message' => $e->getMessage(),], 422);
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
        $request = UpdateDanceDetailRequest::fromArray($_POST);

        try {
            $this->danceService->saveDanceDetailPage($pageSlug, $request);
            header('Location: /cms/events/dance-detail/' . rawurlencode($pageSlug) . '?saved=1');
            exit;
        } catch (\Throwable $e) {
            $baseViewModel = $this->danceService->getDanceDetailFormData($pageSlug);
            $contentViewModel = $this->cmsDanceViewModelMapper->mapDetailRequestToContentViewModel($request, $baseViewModel);
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
        $request = UpdateDanceDetailRequest::fromArray($_POST);

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
