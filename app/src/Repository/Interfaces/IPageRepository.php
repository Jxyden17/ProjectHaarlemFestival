<?php

namespace App\Repository\Interfaces;

use App\Models\Page\Page;

interface IPageRepository
{
    public function findPageById(int $pageId): ?Page;

    public function findPageBySlug(string $slug): ?Page;

    public function findPagesByEventId(int $eventId): array;

    public function findPageIdBySlug(string $slug): ?int;

    public function createPage(int $eventId, string $pageName, string $slug, array $sections): int;

    public function findSectionIdsByPageId(int $pageId): array;

    public function updatePageName(int $pageId, string $pageName): void;

    public function updateSectionById(
        int $sectionId,
        ?string $title,
        ?string $subtitle,
        ?string $description,
        int $orderIndex
    ): void;

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
