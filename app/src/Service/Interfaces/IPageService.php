<?php
namespace App\Service\Interfaces;

use App\Models\Page\Page;
interface IPageService
{
    public function buildPage(int $pageId): ?Page;
    public function getPageBySlug(string $slug, string $fallbackTitle = ''): Page;
    public function getPagesByEventId(int $eventId): array;
    public function findPageIdBySlug(string $slug): ?int;
    public function createPage(int $eventId, string $pageName, string $slug, array $sections): int;
    public function deletePageById(int $pageId): void;
}
