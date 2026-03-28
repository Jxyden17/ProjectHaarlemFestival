<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Service\Interfaces\IPageService;
use App\Service\Cms\Interfaces\ICmsEventEditorService;

class CmsHomeContentController extends BaseController
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
        $pageId = 15;
        
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
            'hero'      => $page->getSection('hero'),
            'about'     => $page->getSection('about'),
            'discover'  => $page->getSection('discover_events'),
            'guide'     => $page->getSection('guide'),
            'faq'       => $page->getSection('faq'),
            'map'       => $page->getSection('map_section')
        ];
        $this->renderCms('cms/events/home', $viewData);
    }

    public function update(): void
    {
        $this->requireAdmin();
        $sections = is_array($_POST['sections']) ? $_POST['sections'] : [];
        $items = is_array($_POST['items']) ? $_POST['items'] : [];
        try {
            $this->cmsEventEditorService->savePageContent(15, $sections, $items);
            $_SESSION['cms_home_success'] = 'Home content opgeslagen.';
            header('Location: /cms/events/home?saved=1');
            exit;
        } catch (\Throwable $e) {
             $_SESSION['cms_home_error'] = 'Opslaan mislukt.' . $e->getMessage();
        }
        header('Location: /cms/events/home');
    }

}