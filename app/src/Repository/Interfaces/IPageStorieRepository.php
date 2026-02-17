<?php

namespace App\Repository\Interfaces;

interface IPageStorieRepository
{
    public function findBySlug(string $slug): ?array;

    public function getSections(int $pageId): array;

    public function getSectionItems(int $sectionId): array;
}
