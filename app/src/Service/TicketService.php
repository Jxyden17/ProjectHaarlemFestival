<?php

namespace App\Service;

use App\Models\Enums\Event;
use App\Service\Interfaces\ITicketService;
use App\Repository\Interfaces\ITicketRepository;

class TicketService implements ITicketService
{
    private ITicketRepository $ticketRepo;
    public function __construct(ITicketRepository $ticketRepo)
    {
        $this->ticketRepo = $ticketRepo;
    }

    public function getTicketsByEvent(Event $event): array
    {
        return $this->ticketRepo->getTicketsByEventId($event->value);
    }
}