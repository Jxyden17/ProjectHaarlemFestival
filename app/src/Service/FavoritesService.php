<?php

namespace App\Service;

use App\Repository\Interfaces\ICartRepository;
use App\Repository\Interfaces\IFavoritesRepository;
use App\Service\Interfaces\IFavoritesService;

class FavoritesService implements IFavoritesService
{
    private IFavoritesRepository $favoritesRepository;
    private ICartRepository $cartRepository;

    public function __construct(IFavoritesRepository $favoritesRepository, ICartRepository $cartRepository)
    {
        $this->favoritesRepository = $favoritesRepository;
        $this->cartRepository = $cartRepository;
    }

    public function getFavorites(): array
    {
        return $this->favoritesRepository->findFavoritesByUserId($this->getUserId());
    }

    public function isFavorite(int $sessionId): bool
    {
        if ($sessionId <= 0) {
            return false;
        }

        return $this->favoritesRepository->isFavorite($this->getUserId(), $sessionId);
    }

    public function addFavorite(int $sessionId): void
    {
        if ($sessionId <= 0) {
            throw new \RuntimeException('Invalid session.');
        }

        $session = $this->cartRepository->findSessionById($sessionId);
        if ($session === null) {
            throw new \RuntimeException('Session not found.');
        }

        $this->favoritesRepository->addFavorite($this->getUserId(), $sessionId);
    }

    public function removeFavorite(int $sessionId): void
    {
        if ($sessionId <= 0) {
            throw new \RuntimeException('Invalid session.');
        }

        $this->favoritesRepository->removeFavorite($this->getUserId(), $sessionId);
    }

    private function getUserId(): int
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            throw new \RuntimeException('Please log in to use favorites.');
        }

        return $userId;
    }
}
