<?php

namespace App\Service\Cms\Interfaces;   

use App\Models\Event\PerformerModel;

interface IArtistesService
{
    public function getAllArtistesForEvent(int $eventId): array;
    public function getArtisteById(int $id): ?PerformerModel;
    public function deleteArtisteById(int $id): bool;
    public function updateArtiste(PerformerModel $artiste): bool;
    public function getEventNameById(int $eventId): ?string;
    public function addArtiste(PerformerModel $artiste): bool;
}