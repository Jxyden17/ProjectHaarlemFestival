<?php
namespace App\Repository\Interfaces;

interface IPageRepository
{
    public function findPageGraphRowsById(int $pageId): array;
    public function findPageGraphRowsBySlug(string $slug): array;
    public function findPageIdBySlug(string $slug): ?int;
    public function saveOrUpdateSection(int $pageId, string $sectionType, ?string $title, ?string $subtitle, ?string $description, int $orderIndex): int;
    public function upsertSectionItems(int $sectionId, array $items): void;
}
