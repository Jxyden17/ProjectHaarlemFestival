<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Service\Cms\Interfaces\ICmsService;
use App\Service\Cms\Interfaces\ICmsEventEditorService;
use App\Service\Interfaces\IDanceService;

class CmsEventsController extends BaseController
{
    private ICmsService $cmsService;
    private IDanceService $danceService;
    private ICmsEventEditorService $cmsEventEditorService;

    public function __construct(ICmsService $cmsService, IDanceService $danceService, ICmsEventEditorService $cmsEventEditorService)
    {
        $this->cmsService = $cmsService;
        $this->danceService = $danceService;
        $this->cmsEventEditorService = $cmsEventEditorService;
    }

    public function index(): void
    {
        $this->requireAdmin();
        $this->renderCms('cms/events/index', [
            'title' => 'Event Management',
            'danceDetailPages' => $this->danceService->getDanceDetailPages(),
            'tourDetailPages' => $this->cmsEventEditorService->getTourDetailPages(),
        ]);
    }
}
