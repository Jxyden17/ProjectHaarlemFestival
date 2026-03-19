<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Service\Cms\Interfaces\ICmsEventEditorService;
use App\Service\Interfaces\IPageService;

class CmsStoriesContentController extends BaseController
{
    private const STORIES_PAGE_ID = 3;

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

        $page = $this->pageService->buildPage(self::STORIES_PAGE_ID);
        if (!$page) {
            http_response_code(404);
            $this->render('shared/error', [
                'errorTitle' => 'Page not found',
                'errorMessage' => 'The page you requested does not exist.',
            ]);
            return;
        }

        $this->renderCms('cms/events/stories-home', [
            'title' => 'Stories Home Content',
            'pageTitle' => $page->title,
            'hero' => $page->getSection('hero'),
            'grid' => $page->getSection('grid'),
            'venues' => $page->getSection('venues'),
            'schedule' => $page->getSection('schedule'),
            'explore' => $page->getSection('explore'),
            'faq' => $page->getSection('faq'),
            'success' => isset($_GET['saved']),
        ]);
    }

    public function update(): void
    {
        $this->requireAdmin();

        $sections = is_array($_POST['sections'] ?? null) ? $_POST['sections'] : [];
        $items = is_array($_POST['items'] ?? null) ? $_POST['items'] : [];

        try {
            $this->cmsEventEditorService->savePageContent(self::STORIES_PAGE_ID, $sections, $items);
            $_SESSION['cms_stories_success'] = 'Stories content opgeslagen.';
            header('Location: /cms/events/stories-home?saved=1');
            exit;
        } catch (\Throwable $e) {
            $_SESSION['cms_stories_error'] = 'Opslaan mislukt.' . $e->getMessage();
        }

        header('Location: /cms/events/stories-home');
    }

    public function details(): void
    {
        $this->requireAdmin();

        $pageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $page = $pageId > 0 ? $this->pageService->buildPage($pageId) : null;

        if (!$page) {
            http_response_code(404);
            $this->render('shared/error', [
                'errorTitle' => 'Page not found',
                'errorMessage' => 'The page you requested does not exist.',
            ]);
            return;
        }

        $this->renderCms('cms/events/stories-details', [
            'title' => 'Stories Detail Content',
            'pageTitle' => $page->title,
            'pageId' => $pageId,
            'publicPath' => '/stories/' . ltrim((string)$page->slug, '/'),
            'hero' => $page->getSection('hero'),
            'about' => $page->getSection('about'),
            'gallery' => $page->getSection('gallery'),
            'featured' => $page->getSection('featured'),
            'booking' => $page->getSection('booking'),
            'success' => isset($_GET['saved']),
        ]);
    }

    public function detailsUpdate(): void
    {
        $this->requireAdmin();

        $pageId = (int)($_POST['page_id'] ?? $_GET['id'] ?? 0);
        $sections = is_array($_POST['sections'] ?? null) ? $_POST['sections'] : [];
        $items = is_array($_POST['items'] ?? null) ? $_POST['items'] : [];

        try {
            $this->cmsEventEditorService->savePageContent($pageId, $sections, $items);
            $_SESSION['cms_stories_success'] = 'Stories detail opgeslagen.';
            header('Location: /cms/events');
            exit;
        } catch (\Throwable $e) {
            $_SESSION['cms_stories_error'] = 'Opslaan mislukt.' . $e->getMessage();
        }

        header('Location: /cms/events/stories-details?id=' . $pageId);
    }
}
