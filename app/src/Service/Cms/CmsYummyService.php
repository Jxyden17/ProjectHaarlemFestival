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

    public function saveYummyContent(string $slug, array $sections, array $items): void
    {
        $slug = $slug ?? 'yummy';

        $pageId = $this->pageRepository->findPageIdBySlug($slug);

        if (!$pageId) {
            throw new \RuntimeException('Page not found: ' . $slug);
        }

        $this->eventEditor->savePageContent(
            $pageId,
            $sections,
            $items
        );
    }
}