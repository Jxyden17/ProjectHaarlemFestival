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

    // Stores CMS dance dependencies so controller actions stay focused on request and response flow.
    public function __construct(ICmsDanceService $danceService, CmsDanceViewModelMapper $cmsDanceViewModelMapper)
    {
        $this->danceService = $danceService;
        $this->cmsDanceViewModelMapper = $cmsDanceViewModelMapper;
    }

    // Renders the CMS dance home editor so admins can edit the stored home-page content.
    public function index(): void
    {
        $this->requireAdmin();
        $contentViewModel = $this->cmsDanceViewModelMapper->mapHomePageToEditViewModel(
            $this->danceService->getDanceHomePage()
        );

        $this->renderCms('cms/events/dance-home', [
            'title' => 'Dance Home Content',
            'contentViewModel' => $contentViewModel,
            'success' => isset($_GET['saved']),
        ]);
    }

    // Handles the form-post dance home save so browser-based CMS edits persist and redirect back with status.
    public function updateHome(): void
    {
        $this->requireAdmin();
        $request = UpdateDanceHomeRequest::fromArray($_POST);

        try {
            $this->danceService->saveDanceHomePage($request);
            header('Location: /cms/events/dance-home?saved=1');
            exit;
        } catch (\Throwable $e) {
            $contentViewModel = $this->cmsDanceViewModelMapper->mapHomeRequestToEditViewModel($request);
            $this->renderCms('cms/events/dance-home', [
                'title' => 'Dance Home Content',
                'contentViewModel' => $contentViewModel,
                'error' => $e->getMessage(),
                'success' => false,
            ]);
        }
    }

    // Handles the API dance home save so async CMS clients get a JSON success or validation error response.
    public function updateHomeAPI(array $vars = []): void
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

    // Renders one CMS dance detail editor so admins can edit a page selected by slug. Example: slug 'urban-echo' -> CMS detail form.
    public function detail(array $vars = []): void
    {
        $this->requireAdmin();

        $pageSlug = trim((string)($vars['pageSlug'] ?? ''));
        try {
            $detailData = $this->danceService->getDanceDetailEditorData($pageSlug);
            $contentViewModel = $this->cmsDanceViewModelMapper->mapDetailDataToEditViewModel($detailData);

            $this->renderCms('cms/events/dance-detail', [
                'title' => $contentViewModel->editorTitle,
                'contentViewModel' => $contentViewModel,
                'success' => isset($_GET['saved']),
            ]);
        } catch (\Throwable $e) {
            http_response_code(404);
            $this->renderCms('shared/error', [
                'errorTitle' => 'Dance detail page not found',
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    // Handles the form-post dance detail save so browser-based CMS edits update the selected detail page.
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
            try {
                $detailData = $this->danceService->getDanceDetailEditorData($pageSlug);
                $baseViewModel = $this->cmsDanceViewModelMapper->mapDetailDataToEditViewModel($detailData);
                $contentViewModel = $this->cmsDanceViewModelMapper->mapDetailRequestToEditViewModel($request, $baseViewModel);
                $this->renderCms('cms/events/dance-detail', [
                    'title' => $contentViewModel->editorTitle,
                    'contentViewModel' => $contentViewModel,
                    'error' => $e->getMessage(),
                    'success' => false,
                ]);
            } catch (\Throwable $inner) {
                http_response_code(404);
                $this->renderCms('shared/error', [
                    'errorTitle' => 'Dance detail page not found',
                    'errorMessage' => $inner->getMessage(),
                ]);
            }
        }
    }

    // Handles the API dance detail save so async CMS clients get a JSON success or validation error response.
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
