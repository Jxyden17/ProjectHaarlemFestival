<?php 

namespace App\Service\Cms;

use App\Service\Cms\Interfaces\ICmsYummyService;
use App\Service\Cms\Interfaces\ICmsEventEditorService;
use App\Repository\PageRepository;

class CmsYummyService implements ICmsYummyService
{
    private ICmsEventEditorService $eventEditor;
    private PageRepository $pageRepository;

    public function __construct(
        ICmsEventEditorService $eventEditor,
        PageRepository $pageRepository
    ) {
        $this->eventEditor = $eventEditor;
        $this->pageRepository = $pageRepository;
    }

    public function saveYummyContent(array $sections, array $items): void
    {
        $pageId = $this->pageRepository->findPageIdBySlug('yummy');

        if (!$pageId) {
            throw new \RuntimeException('Yummy page not found.');
        }

        $this->eventEditor->savePageContent(
            $pageId,
            $sections,
            $items
        );
    }

    public function savePageContentBySlug(string $slug, array $sections, array $items): void
    {
        $page = $this->pageRepository->getPageBySlug($slug);

        if (!$page) {
            throw new \Exception("Page not found");
        }

        $this->pageRepository->updatePageContent(
            $page->getId(),
            $sections,
            $items
        );
    }
}