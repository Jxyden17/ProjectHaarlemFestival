<?php
namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Service\Cms\Interfaces\ICmsEventManagementService;

class CmsEventManagementController extends BaseController
{
    public function __construct(private ICmsEventManagementService $cmsEventManagementService)
    {
    }

    public function index(): void
    {
        $this->requireAdmin();
        $events = $this->cmsEventManagementService->getEventCards();

        $this->renderCms('cms/event/index', [
            'title' => 'Event Management',
            'events' => $events,
        ]);
    }
}