<?php

namespace App\Service\Cms\Interfaces;

use App\Models\Enums\Event;
use App\Models\Event\PerformerModel;
use App\Models\Event\VenueModel;

interface ICmsEventManagementService
{
    public function getEventCards(): array;
    public function supportsArtists(Event $event): bool;
    public function supportsTickets(Event $event): bool;

    // // Artists
    // public function getArtistesForEvent(int $eventId): array;
    // public function getArtisteById(int $id): ?PerformerModel;
    // public function addArtiste(PerformerModel $artiste): bool;
    // public function updateArtiste(PerformerModel $artiste): bool;
    // public function deleteArtisteById(int $id): bool;

    // // Venues
    // public function getVenuesForEvent(int $eventId): array;
    // public function getVenueById(int $id): ?VenueModel;
    // public function addVenue(int $eventId, string $venueName, ?string $address, ?string $venueType): int;
    // public function updateVenue(int $id, string $venueName, ?string $address, ?string $venueType): bool;
    // public function canDeleteVenue(int $id): bool;
    // public function deleteVenue(int $id): bool;

    // // Schedules (used for ticket create form session dropdown)
    // public function getSchedulesForEvent(int $eventId): array;

    // // Tickets
    // public function getTicketsForEvent(int $eventId): array;
    // public function getTicketById(int $id): ?array;
    // public function createTicketSlot(int $eventId, string $date, string $startTime, int $quantity): int;
    // public function updateTicket(int $id, int $eventId, string $date, string $startTime, int $quantity): bool;
    // public function deleteTicket(int $id, int $eventId): bool;
}
