<?php
namespace App\Service\Interfaces;

use App\Models\Page\Page;
interface IPageService
{
    // Builds one page by id from storage so callers can work with the fully mapped page object.
    public function buildPage(int $pageId): ?Page;

    // Finds one page by slug so callers can handle missing content honestly with null. Example: slug 'urban-echo' -> Page or null.
    public function findPageBySlug(string $slug): ?Page;

    // Returns one required page by slug so flows that must have content fail fast with a clear message.
    public function requirePageBySlug(string $slug, string $missingMessage = 'Page not found.'): Page;

    // Returns a page by slug or a fallback stub for legacy callers that still want fake-success behavior. Example: slug 'dance-home' -> Page.
    public function getPageBySlug(string $slug, string $fallbackTitle = ''): Page;
    public function getPagesByEventId(int $eventId): array;
    public function findPageIdBySlug(string $slug): ?int;
    public function createPage(int $eventId, string $pageName, string $slug, array $sections): int;
    public function deletePageById(int $pageId): void;
}
