<?php

namespace App\Service;

use App\Models\Event\PerformerModel;
use App\Repository\Interfaces\IArtistesRepository;
use App\Service\Interfaces\IArtistesService;
use App\Models\Enums\Event;

class ArtistesService implements IArtistesService
{
    private IArtistesRepository $artistesRepository;

    public function __construct(IArtistesRepository $artistesRepository)
    {
        $this->artistesRepository = $artistesRepository;
    }

    public function getAllArtistesForEvent(int $eventId): array
    {
        return $this->artistesRepository->getAllArtistesForEvent($eventId);
    }

    public function getArtisteById(int $id): ?PerformerModel
    {
        return $this->artistesRepository->getArtisteById($id);
    }

    public function deleteArtisteById(int $id): bool
    {
        return $this->artistesRepository->deleteArtisteById($id);
    }

    public function updateArtiste(PerformerModel $artiste): bool
    {
        return $this->artistesRepository->updateArtiste($artiste);
    }

    public function addArtiste(PerformerModel $artiste): bool
    {
        return $this->artistesRepository->addArtiste($artiste);
    }

    //Delete Yummy van list voor artisten
    public function supportsArtists(Event $event): bool
    {
        return $event !== Event::Yummy;
    }
}