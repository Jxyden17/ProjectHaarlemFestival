<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Mapper\CmsStoriesViewModelMapper;
use App\Service\Cms\Interfaces\ICmsEventEditorService;
use App\Service\Interfaces\IPageService;

class CmsStoriesContentController extends BaseController
{
    private const STORIES_PAGE_ID = 3;
    private const STORIES_EVENT_ID = 3;

    private IPageService $pageService;
    private ICmsEventEditorService $cmsEventEditorService;
    private CmsStoriesViewModelMapper $storiesViewModelMapper;

    public function __construct(
        IPageService $pageService,
        ICmsEventEditorService $cmsEventEditorService,
        CmsStoriesViewModelMapper $storiesViewModelMapper
    )
    {
        $this->pageService = $pageService;
        $this->cmsEventEditorService = $cmsEventEditorService;
        $this->storiesViewModelMapper = $storiesViewModelMapper;
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
            'contentViewModel' => $this->storiesViewModelMapper->mapHomePageToEditViewModel($page),
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
            'contentViewModel' => $this->storiesViewModelMapper->mapDetailPageToEditViewModel($page, $pageId),
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

    public function create(): void
    {
        $this->requireAdmin();

        $pageName = trim((string) ($_POST['page_name'] ?? ''));
        if ($pageName === '') {
            $_SESSION['cms_stories_error'] = 'Please enter a title for the new Stories event.';
            header('Location: /cms/events');
            exit;
        }

        try {
            $slug = $this->generateUniqueSlug($pageName);
            $pageId = $this->pageService->createPage(
                self::STORIES_EVENT_ID,
                $pageName,
                $slug,
                $this->buildDetailPageTemplate($pageName)
            );

            header('Location: /cms/events/stories-details?id=' . $pageId);
            exit;
        } catch (\Throwable $e) {
            $_SESSION['cms_stories_error'] = 'Could not create the Stories event. ' . $e->getMessage();
            header('Location: /cms/events');
            exit;
        }
    }

    public function createForm(): void
    {
        $this->requireAdmin();

        $storiesError = $_SESSION['cms_stories_error'] ?? null;
        unset($_SESSION['cms_stories_error']);

        $this->renderCms('cms/events/stories-create', [
            'title' => 'Create Stories Event',
            'storiesError' => is_string($storiesError) ? $storiesError : null,
        ]);
    }

    public function delete(): void
    {
        $this->requireAdmin();

        $pageId = (int) ($_POST['page_id'] ?? 0);
        if ($pageId <= 0) {
            $_SESSION['cms_stories_error'] = 'Missing Stories page identifier.';
            header('Location: /cms/events');
            exit;
        }

        if (!$this->canDeleteStoriesPage($pageId)) {
            $_SESSION['cms_stories_error'] = 'This Stories page cannot be deleted.';
            header('Location: /cms/events');
            exit;
        }

        try {
            $this->pageService->deletePageById($pageId);
            $_SESSION['cms_stories_success'] = 'Stories subpage deleted.';
        } catch (\Throwable $e) {
            $_SESSION['cms_stories_error'] = 'Could not delete the Stories subpage. ' . $e->getMessage();
        }

        header('Location: /cms/events');
        exit;
    }

    private function generateUniqueSlug(string $pageName): string
    {
        $baseSlug = $this->normalizeSlug($pageName);
        $slug = $baseSlug;
        $suffix = 2;

        while ($this->pageService->findPageIdBySlug($slug) !== null) {
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    private function normalizeSlug(string $value): string
    {
        $slug = trim($value);
        $transliterated = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $slug);
        if ($transliterated !== false) {
            $slug = $transliterated;
        }

        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
        $slug = trim($slug, '-');

        return $slug !== '' ? $slug : 'story-event';
    }

    private function buildDetailPageTemplate(string $pageName): array
    {
        return [
            [
                'section_type' => 'hero',
                'title' => $pageName,
                'subtitle' => 'Add a short intro for this Stories event.',
                'description' => 'Update this section with the main hook for your event page.',
                'order_index' => 1,
                'items' => [
                    [
                        'item_category' => 'image',
                        'title' => 'Hero image',
                        'item_subtitle' => null,
                        'content' => '',
                        'image_path' => '',
                        'link_url' => null,
                        'duration' => null,
                        'icon_class' => null,
                        'order_index' => 1,
                    ],
                    [
                        'item_category' => 'tag',
                        'title' => 'Stories',
                        'item_subtitle' => null,
                        'content' => '',
                        'image_path' => null,
                        'link_url' => null,
                        'duration' => null,
                        'icon_class' => null,
                        'order_index' => 2,
                    ],
                ],
            ],
            [
                'section_type' => 'about',
                'title' => 'About this story',
                'subtitle' => '',
                'description' => '',
                'order_index' => 2,
                'items' => [
                    [
                        'item_category' => 'paragraph',
                        'title' => '',
                        'item_subtitle' => null,
                        'content' => 'Add the main description for this Stories event here.',
                        'image_path' => null,
                        'link_url' => null,
                        'duration' => null,
                        'icon_class' => null,
                        'order_index' => 1,
                    ],
                ],
            ],
            [
                'section_type' => 'gallery',
                'title' => 'Gallery',
                'subtitle' => '',
                'description' => '',
                'order_index' => 3,
                'items' => [
                    [
                        'item_category' => 'gallery',
                        'title' => 'Gallery image',
                        'item_subtitle' => null,
                        'content' => '',
                        'image_path' => '',
                        'link_url' => null,
                        'duration' => null,
                        'icon_class' => null,
                        'order_index' => 1,
                    ],
                ],
            ],
            [
                'section_type' => 'featured',
                'title' => 'Featured audio',
                'subtitle' => '',
                'description' => '',
                'order_index' => 4,
                'items' => [
                    [
                        'item_category' => 'Listen',
                        'title' => 'Featured track',
                        'item_subtitle' => null,
                        'content' => 'Add a short teaser or highlight for this event.',
                        'image_path' => null,
                        'link_url' => '',
                        'duration' => null,
                        'icon_class' => null,
                        'order_index' => 1,
                    ],
                ],
            ],
            [
                'section_type' => 'booking',
                'title' => 'Book your experience',
                'subtitle' => 'Choose your ticket and reserve your spot.',
                'description' => 'Update the booking details once the session is ready.',
                'order_index' => 5,
                'items' => [
                    [
                        'item_category' => 'datetime',
                        'title' => 'Add date and time',
                        'item_subtitle' => null,
                        'content' => '',
                        'image_path' => null,
                        'link_url' => '',
                        'duration' => null,
                        'icon_class' => null,
                        'order_index' => 1,
                    ],
                    [
                        'item_category' => 'location',
                        'title' => 'Add location',
                        'item_subtitle' => null,
                        'content' => '',
                        'image_path' => null,
                        'link_url' => '',
                        'duration' => null,
                        'icon_class' => null,
                        'order_index' => 2,
                    ],
                    [
                        'item_category' => 'tag',
                        'title' => '12+',
                        'item_subtitle' => null,
                        'content' => '',
                        'image_path' => null,
                        'link_url' => '',
                        'duration' => null,
                        'icon_class' => null,
                        'order_index' => 3,
                    ],
                    [
                        'item_category' => 'price_label',
                        'title' => 'Price',
                        'item_subtitle' => null,
                        'content' => '',
                        'image_path' => null,
                        'link_url' => '',
                        'duration' => null,
                        'icon_class' => null,
                        'order_index' => 4,
                    ],
                    [
                        'item_category' => 'price',
                        'title' => 'Add price',
                        'item_subtitle' => null,
                        'content' => '',
                        'image_path' => null,
                        'link_url' => '',
                        'duration' => null,
                        'icon_class' => null,
                        'order_index' => 5,
                    ],
                    [
                        'item_category' => 'button',
                        'title' => 'Book now',
                        'item_subtitle' => null,
                        'content' => '',
                        'image_path' => null,
                        'link_url' => '',
                        'duration' => null,
                        'icon_class' => null,
                        'order_index' => 6,
                    ],
                ],
            ],
        ];
    }

    private function canDeleteStoriesPage(int $pageId): bool
    {
        if ($pageId === self::STORIES_PAGE_ID) {
            return false;
        }

        foreach ($this->pageService->getPagesByEventId(self::STORIES_EVENT_ID) as $page) {
            if ((int) ($page['id'] ?? 0) === $pageId) {
                return true;
            }
        }

        return false;
    }
}
