<?php

namespace App\Service\Interfaces;

interface IYummyService
{
    public function getYummyPage(): ?object;

    public function getYummyVenues(): array;
}