<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Service\Cms\Interfaces\ICmsTicketManagementService;

class CmsTicketManagementController extends BaseController
{
    public function __construct(private ICmsTicketManagementService $cmsTicketManagementService)
    {
    }

    public function index(): void
    {
        $this->requireAdmin();

        $dashboardData = $this->cmsTicketManagementService->getDashboardData();

        $this->renderCms('cms/tickets/index', [
            'title' => 'Ticket Management',
            'summary' => $dashboardData['summary'] ?? [],
            'events' => $dashboardData['events'] ?? [],
        ]);
    }
}
