<?php
namespace App\Service\Interfaces;

use App\Models\Page\Page;
interface IPageService
{
    public function buildPage(int $pageId): ?Page;
    public function getPageBySlug(string $slug, string $fallbackTitle = ''): Page;
    public function getPagesByEventId(int $eventId): array;
}
