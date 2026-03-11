<?php

namespace App\Repository\Interfaces;

interface IPageRepository
{
    public function findPageRowsById(int $pageId): array;

    public function findPageRowsBySlug(string $slug): array;

    public function findPageIdBySlug(string $slug): ?int;

    public function saveOrUpdateSection(
        int $pageId,
        string $sectionType,
        ?string $title,
        ?string $subtitle,
        ?string $description,
        int $orderIndex
    ): int;

    public function saveOrUpdateSectionItems(int $sectionId, array $items): void;
}
