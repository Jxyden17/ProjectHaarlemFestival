<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Service\Interfaces\IPageService;
use App\Service\Cms\Interfaces\ICmsYummyService;

class CmsYummyContentController extends BaseController
{
    private IPageService $pageService;
    private ICmsYummyService $cmsYummyService;

    public function __construct(
        IPageService $pageService,
        ICmsYummyService $cmsYummyService
    ) {
        $this->pageService = $pageService;
        $this->cmsYummyService = $cmsYummyService;
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

    public function update(): void
    {
        $this->requireAdmin();

        $sections = $_POST['sections'] ?? [];
        $items = $_POST['items'] ?? [];

        try {

            $this->cmsYummyService->saveYummyContent(
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

    public function detail(array $vars = []): void
    {
        $this->requireAdmin();

        $slug = $vars['slug'] ?? '';

        $page = $this->pageService->getPageBySlug($slug);

        if (!$page) {
            http_response_code(404);
            return;
        }

        $heroSection = $page->getSection('restaurant_hero');
        $introSection = $page->getSection('restaurant_concept');
        $contactSection = $page->getSection('restaurant_contact');

        $this->render('cms/events/yummy-detail', [
            'page' => $page,
            'heroSection' => $heroSection,
            'introSection' => $introSection,
            'contactSection' => $contactSection
        ]);
    }

    public function detailUpdate(array $vars = []): void
    {
        $this->requireAdmin();

        $slug = $vars['slug'] ?? '';

        $sections = $_POST['sections'] ?? [];
        $items = $_POST['items'] ?? [];

        $this->cmsYummyService->saveYummyContent(
            $slug,
            $sections,
            $items
        );

        header("Location: /cms/events/yummy-detail/$slug?saved=1");
        exit;
    }
}