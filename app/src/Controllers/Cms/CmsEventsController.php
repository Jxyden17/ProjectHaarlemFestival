<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Service\Cms\Interfaces\ICmsService;
use App\Service\Interfaces\IDanceService;
use App\Service\Interfaces\IPageService;

class CmsEventsController extends BaseController
{
    private ICmsService $cmsService;
    private IDanceService $danceService;
    private IPageService $pageService;

    public function __construct(ICmsService $cmsService, IDanceService $danceService, IPageService $pageService)
    {
        $this->cmsService = $cmsService;
        $this->danceService = $danceService;
        $this->pageService = $pageService;
    }

    public function index(): void
    {
        $this->requireAdmin();
        $storyDetailPages = array_values(array_filter(
            $this->pageService->getPagesByEventId(3),
            static fn (array $page): bool => (int)($page['id'] ?? 0) !== 3
        ));

        $this->renderCms('cms/events/index', [
            'title' => 'Event Management',
            'danceDetailPages' => $this->danceService->getPublishedDanceDetailPages(),
            'storyDetailPages' => $storyDetailPages,
        ]);
    }
}
