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

    public function sold(): void
    {
        $this->requireAdmin();

        $eventId = isset($_GET['event_id']) ? (int) $_GET['event_id'] : null;
        $paymentStatus = trim((string) ($_GET['payment_status'] ?? 'all'));
        $soldTicketsData = $this->cmsTicketManagementService->getSoldTicketsData($eventId, $paymentStatus);

        $this->renderCms('cms/tickets/sold', [
            'title' => 'Sold Tickets',
            'selectedEvent' => $soldTicketsData['selectedEvent'] ?? null,
            'eventTypes' => $soldTicketsData['eventTypes'] ?? [],
            'tickets' => $soldTicketsData['tickets'] ?? [],
            'paymentStatusFilter' => $soldTicketsData['paymentStatusFilter'] ?? 'all',
            'summary' => $soldTicketsData['summary'] ?? [],
        ]);
    }
}
