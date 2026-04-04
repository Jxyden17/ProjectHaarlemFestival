<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Service\Cms\Interfaces\ICmsService;
use App\Service\Cms\Interfaces\ICmsEventEditorService;
use App\Service\Interfaces\IDanceService;
use App\Service\Interfaces\IPageService;

class CmsEventsController extends BaseController
{
    private ICmsService $cmsService;
    private IDanceService $danceService;
    private IPageService $pageService;
    private ICmsEventEditorService $cmsEventEditorService;

    public function __construct(
        ICmsService $cmsService,
        IDanceService $danceService,
        IPageService $pageService,
        ICmsEventEditorService $cmsEventEditorService
    )
    {
        $this->cmsService = $cmsService;
        $this->danceService = $danceService;
        $this->pageService = $pageService;
        $this->cmsEventEditorService = $cmsEventEditorService;
    }

    public function index(): void
    {
        $this->requireAdmin();
        $storiesSuccess = $_SESSION['cms_stories_success'] ?? null;
        $storiesError = $_SESSION['cms_stories_error'] ?? null;
        unset($_SESSION['cms_stories_success'], $_SESSION['cms_stories_error']);

        $storyDetailPages = array_values(array_filter(
            $this->pageService->getPagesByEventId(3),
            static fn (array $page): bool => (int)($page['id'] ?? 0) !== 3
        ));

        $this->renderCms('cms/events/index', [
            'title' => 'Page Management',
            'danceDetailPages' => $this->danceService->getDanceDetailPages(),
            'storyDetailPages' => $storyDetailPages,
            'storiesSuccess' => is_string($storiesSuccess) ? $storiesSuccess : null,
            'storiesError' => is_string($storiesError) ? $storiesError : null,
        ]);
    }
}
