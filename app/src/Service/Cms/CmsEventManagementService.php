<?php
namespace App\Service\Cms;

use App\Models\Enums\Event;
use App\Service\Cms\Interfaces\ICmsEventManagementService;

class CmsEventManagementService implements ICmsEventManagementService
{
    public function getEventCards(): array
    {
        return array_map(
            fn(Event $event): array => [
                'slug'            => strtolower($event->label()),
                'id'       => $event->value,
                'label'           => $event->label(),
                'supportsArtists' => $this->supportsArtists($event),
                'supportsTickets' => $this->supportsTickets($event),
            ],
            Event::cases()
        );
    }

    public function supportsArtists(Event $event): bool
    {
        return $event !== Event::Yummy;
    }

    public function supportsTickets(Event $event): bool
    {
        return $event !== Event::Yummy;
    }
}

