<?php

namespace App\Repository\Interfaces;

interface IFavoritesRepository
{
    public function findFavoritesByUserId(int $userId): array;

    public function isFavorite(int $userId, int $sessionId): bool;

    public function addFavorite(int $userId, int $sessionId): void;

    public function removeFavorite(int $userId, int $sessionId): void;
}
