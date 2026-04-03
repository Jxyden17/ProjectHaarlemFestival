<?php

namespace App\Service\Interfaces;

interface IFavoritesService
{
    public function getFavorites(): array;

    public function isFavorite(int $sessionId): bool;

    public function addFavorite(int $sessionId): void;

    public function removeFavorite(int $sessionId): void;
}
