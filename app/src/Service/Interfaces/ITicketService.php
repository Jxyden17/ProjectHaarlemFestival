<?php

namespace App\Service\Interfaces;

use App\Models\Enums\Event;
use App\Models\Ticket\Ticket;
interface ITicketService
{
    public function getTicketsByEvent(Event $event): array;
}