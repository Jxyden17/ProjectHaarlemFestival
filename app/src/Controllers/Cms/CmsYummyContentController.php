<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Service\Interfaces\IPageService;
use App\Service\Cms\Interfaces\ICmsEventEditorService;

class CmsYummyContentController extends BaseController
{
    private IPageService $pageService;
    private ICmsEventEditorService $editorService;

    public function __construct(
        IPageService $pageService,
        ICmsEventEditorService $editorService
    ) {
        $this->pageService = $pageService;
        $this->editorService = $editorService;
    }

    public function index(): void
    {
        $this->requireAdmin();

        $page = $this->pageService->GetPageBySlug('yummy');

        if (!$page) {
            http_response_code(404);
            return;
        }

        $this->render('cms/events/yummy-home', [
            'heroSection' => $page->getSection('yummy_header'),
            'mapSection' => $page->getSection('yummy-map'),
            'restaurantSection' => $page->getSection('yummy-restaurants')
        ]);
    }

    public function save(): void
    {
        $this->requireAdmin();

        $sections = $_POST['sections'] ?? [];
        $items = $_POST['items'] ?? [];

        try {

            $this->editorService->savePageContentBySlug(
                'yummy',
                $sections,
                $items
            );

            header('Location: /cms/events/yummy-home?saved=1');
            exit;

        } catch (\Throwable $e) {

            $_SESSION['cms_error'] = $e->getMessage();
            header('Location: /cms/events/yummy-home');
        }
    }
}