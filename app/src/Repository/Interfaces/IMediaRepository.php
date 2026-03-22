<?php

namespace App\Repository\Interfaces;

interface IMediaRepository
{
    public function updateSectionItemImagePath(
        int $sectionItemId,
        string $imagePath,
        string $pageSlug,
        string $sectionType,
        string $itemCategory
    ): bool;

    public function findSectionItemIdByImagePath(
        string $imagePath,
        string $pageSlug,
        string $sectionType,
        string $itemCategory
    ): ?int;

    public function updateSectionItemLinkUrl(
        int $sectionItemId,
        string $linkUrl,
        string $pageSlug,
        string $sectionType,
        string $itemCategory
    ): bool;

    public function findSectionItemIdByLinkUrl(
        string $linkUrl,
        string $pageSlug,
        string $sectionType,
        string $itemCategory
    ): ?int;

    public function updateSectionItemImagePathBySection(
        int $sectionItemId,
        string $imagePath,
        string $pageSlug,
        string $sectionType
    ): bool;

    public function findSectionItemImagePathById(int $sectionItemId): ?string;
}
