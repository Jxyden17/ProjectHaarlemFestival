<?php

namespace App\Service\Interfaces;   

use App\Models\Event\PerformerModel;
use App\Models\Enums\Event;

interface IArtistesService
{
    public function getAllArtistesForEvent(int $eventId): array;
    public function getArtisteById(int $id): ?PerformerModel;
    public function deleteArtisteById(int $id): bool;
    public function updateArtiste(PerformerModel $artiste): bool;
    public function addArtiste(PerformerModel $artiste): bool;
    public function supportsArtists(Event $event): bool;
}