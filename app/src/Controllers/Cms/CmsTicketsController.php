<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Models\Enums\Event;
use App\Service\Interfaces\ITicketService;

class CmsTicketsController extends BaseController
{
    private ITicketService $ticketService;

    public function __construct(ITicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function index(): void
    {
        $this->requireAdmin();

        $selectedEvent = $this->getSelectedEvent();
        $this->renderCms('cms/tickets/index', [
            'title' => 'Ticket Management',
            'selectedEvent' => $selectedEvent,
            'eventTypes' => Event::cases(),
            'tickets' => $this->ticketService->getTicketsByEvent($selectedEvent)
        ]);
    }
}
