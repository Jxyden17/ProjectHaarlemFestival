<?php
namespace App\Controllers;

use App\Service\Interfaces\IStoriesPageService;

class StoriesController extends BaseController
{
    public function __construct(private IStoriesPageService $storiesPageService)
    {
    }

    public function index(): void
    {
        try {
            $viewData = $this->storiesPageService->getIndexViewData();
        } catch (\RuntimeException $e) {
            http_response_code(404);
            $this->render('shared/error', [
                'errorTitle' => 'Page not found',
                'errorMessage' => $e->getMessage(),
            ]);
            return;
        }

        $this->render('Stories/index', $viewData);
    }

    public function details($slug = null): void
    {
        if (is_array($slug)) {
            $slug = $slug['slug'] ?? $_GET['slug'] ?? null;
        } elseif (!$slug) {
            $slug = $_GET['slug'] ?? null;
        }

        $slug = trim((string)$slug);
        try {
            $viewData = $this->storiesPageService->getDetailViewData($slug);
        } catch (\RuntimeException $e) {
            http_response_code(404);
            $this->render('shared/error', [
                'errorTitle' => 'Page not found',
                'errorMessage' => $e->getMessage(),
            ]);
            return;
        }

        $this->render('Stories/details', $viewData);
    }
}
