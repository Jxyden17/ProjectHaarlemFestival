<?php

namespace App\Service\Interfaces;

interface IYummyService
{
    public function getYummyPage(): ?object;

    public function getYummyVenues(): array;

    public function getVenuePage(string $slug);

    public function getRestaurantPage(string $slug);
}