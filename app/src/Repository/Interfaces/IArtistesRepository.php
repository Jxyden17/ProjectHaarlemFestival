<?php

namespace App\Repository\Interfaces;

use App\Models\Event\PerformerModel;

interface IArtistesRepository
{
    public function getAllArtistesForEvent(int $eventId): array;
    public function getArtisteById(int $id): ?PerformerModel;
    public function deleteArtisteById(int $id): bool;
    public function updateArtiste(PerformerModel $artiste): bool;
    public function addArtiste(PerformerModel $artiste): bool;

}