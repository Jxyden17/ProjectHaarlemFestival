<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Service\Cms\Interfaces\ICmsEventEditorService;
use App\Service\Interfaces\IPageService;

class CmsTourContentController extends BaseController
{
    private IPageService $pageService;
    private ICmsEventEditorService $cmsEventEditorService;

    public function __construct(IPageService $pageService, ICmsEventEditorService $cmsEventEditorService)
    {
        $this->pageService = $pageService;
        $this->cmsEventEditorService = $cmsEventEditorService;
    }

    public function index(): void
    {
        $this->requireAdmin();
        $pageId = 1;
        
        $page = $this->pageService->buildPage($pageId);
        if (!$page) {
            http_response_code(404);
            $this->render('shared/error', [
                'errorTitle' => 'Page not found',
                'errorMessage' => 'The page you requested does not exist.',
            ]);
            return;
        }

        $viewData = [
        'pageTitle' => $page->title,
        'pageSlug' => $page->slug,
        'hero'      => $page->getSection('hero'),
        'stops'     => $page->getSection('tour_overview'),
        'discover'  => $page->getSection('discover'),
        'schedule' => $page->getSection('schedule'),
        'guide'   => $page->getSection('guide')
    ];
        $this->render('cms/events/tour-home', $viewData);
    }

     public function update(): void
    {
        $this->requireAdmin();
        $sections = is_array($_POST['sections']) ? $_POST['sections'] : [];
        $items = is_array($_POST['items']) ? $_POST['items'] : [];

        // $items = $this->applyUploadedImages($items, $_FILES['item_images'] ?? []);

        try {
            $this->cmsEventEditorService->savePageContent(1, $sections, $items);
            $_SESSION['cms_tour_success'] = 'Tour content opgeslagen.';
            header('Location: /cms/events/tour-home?saved=1');
            exit;
        } catch (\Throwable $e) {
             $_SESSION['cms_tour_error'] = 'Opslaan mislukt.' . $e->getMessage();
        }
        header('Location: /cms/events/tour-home');
    }

    public function details(): void
    {
        $this->requireAdmin();
        $pageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        $page = $this->pageService->buildPage($pageId);
        if (!$page) {
            http_response_code(404);
            $this->render('shared/error', [
                'errorTitle' => 'Page not found',
                'errorMessage' => 'The page you requested does not exist.',
            ]);
            return;
        }

        $viewData = [
        'pageTitle' => $page->title,
        'pageSlug' => $page->slug,
        'pageId' => $pageId,
        'header'      => $page->getSection('header'),
        'history'     => $page->getSection('history'),
        'didYouKnow'  => $page->getSection('did_you_know'),
        'openingTime' => $page->getSection('openings_time')
    ];
        $this->render('cms/events/tour-details', $viewData);
    }

     public function detailsUpdate(): void
    {
        $this->requireAdmin();
        $pageId = (int)($_POST['page_id'] ?? $_GET['id'] ?? 0);
        $sections = is_array($_POST['sections']) ? $_POST['sections'] : [];
        $items = is_array($_POST['items']) ? $_POST['items'] : [];

        try {
            $this->cmsEventEditorService->savePageContent($pageId, $sections, $items);
            $_SESSION['cms_tour_success'] = 'Tour content opgeslagen.';
            header('Location: /cms/events');
            exit;
        } catch (\Throwable $e) {
             $_SESSION['cms_tour_error'] = 'Opslaan mislukt.' . $e->getMessage();
        }
        header('Location: /cms/events');

    }
}
