<?php
namespace App\Repository\Interfaces;

interface IPageRepository
{
    public function findPageRowById(int $pageId): ?array;
    public function findPageRowBySlug(string $slug): ?array;
    public function findPageIdBySlug(string $slug): ?int;
    public function getPageSectionsByPageId(int $pageId): array;
    public function getItemsBySectionIds(array $sectionIds): array;
    public function saveOrUpdateSection(int $pageId, string $sectionType, ?string $title, ?string $subtitle, ?string $description, int $orderIndex): int;
    public function upsertSectionItems(int $sectionId, array $items): void;
}
