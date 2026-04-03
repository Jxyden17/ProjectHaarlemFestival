<?php

namespace App\Service;

use App\Models\Page\Page;
use App\Repository\Interfaces\IPageRepository;
use App\Service\Interfaces\IPageService;

class PageService implements IPageService
{
    private IPageRepository $pageRepo;

    // Stores the page repository so page lookups stay behind one service boundary.
    public function __construct(IPageRepository $pageRepo)
    {
        $this->pageRepo = $pageRepo;
    }

    // Builds one page by id so callers can request a fully mapped page object from storage.
    public function buildPage(int $pageId): ?Page
    {
        return $this->pageRepo->findPageById($pageId);
    }

    // Finds one page by slug so callers can branch on null when content does not exist. Example: slug 'urban-echo' -> Page or null.
    public function findPageBySlug(string $slug): ?Page
    {
        return $this->pageRepo->findPageBySlug($slug);
    }

    // Returns one required page by slug so flows that depend on real content fail fast with a clear message.
    public function requirePageBySlug(string $slug, string $missingMessage = 'Page not found.'): Page
    {
        $page = $this->findPageBySlug($slug);
        if ($page === null) {
            throw new \RuntimeException($missingMessage);
        }

        return $page;
    }

    // Returns a page by slug or a fallback stub for legacy callers that still expect a predictable page object. Example: slug 'dance-home' -> Page.
    public function getPageBySlug(string $slug, string $fallbackTitle = ''): Page
    {
        $page = $this->findPageBySlug($slug);
        if ($page === null) {
            return new Page($fallbackTitle, $slug);
        }

        return $page;
    }

    public function getPagesByEventId(int $eventId): array
    {
        return $this->pageRepo->findPagesByEventId($eventId);
    }
}
