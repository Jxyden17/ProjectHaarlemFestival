<?php

namespace App\Service\Cms\Interfaces;

use App\Models\Enums\Event;

interface ICmsEventManagementService
{
    public function getEventCards(): array;
    public function supportsArtists(Event $event): bool;
    public function supportsTickets(Event $event): bool;

}
