<?php

namespace App\Service\Cms;

use App\Models\Event\PerformerModel;
use App\Repository\Interfaces\IArtistesRepository;
use App\Service\Cms\Interfaces\IArtistesService;

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
        try
        {
            return $this->artistesRepository->updateArtiste($artiste);
        }
        catch (\Exception $e)
        {
            error_log("Error updating artiste: " . $e->getMessage());
            return false;
        }
    }

    public function addArtiste(PerformerModel $artiste): bool
    {
        return $this->artistesRepository->addArtiste($artiste);
    }
    public function getEventNameById(int $eventId): ?string
    {
        return null;
    }
}