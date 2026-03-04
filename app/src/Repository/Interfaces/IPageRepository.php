<?php
namespace App\Repository\Interfaces;

use App\Models\Page\Page;

interface IPageRepository
{
    public function getPageDataById(int $PageId);
    public function getPageBySlug(string $slug, string $fallbackTitle = ''): Page;
    public function ensurePageBySlug(int $eventId, string $slug, string $pageName): int;
    public function saveOrUpdateSection(int $pageId, string $sectionType, ?string $title, ?string $subtitle, ?string $description, int $orderIndex): int;
    public function upsertSectionItems(int $sectionId, array $items): void;
}
