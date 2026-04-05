<?php

namespace App\Service\Interfaces;

interface IStoriesPageService
{
    public function getIndexViewData(): array;

    public function getDetailViewData(string $slug): array;
}
