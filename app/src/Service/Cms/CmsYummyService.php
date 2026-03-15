<?php 

namespace App\Service\Cms;

use App\Service\Cms\Interfaces\ICmsYummyEditorService;
use App\Service\Cms\Interfaces\ICmsEventEditorService;
use App\Repository\PageRepository;

class CmsYummyEditorService implements ICmsYummyEditorService
{
    private ICmsEventEditorService $eventEditor;
    private PageRepository $pageRepository;

    public function __construct(
        ICmsEventEditorService $eventEditor,
        PageRepository $pageRepository
    ) {
        $this->eventEditor = $eventEditor;
        $this->pageRepository = $pageRepository
    }

    public function saveYummyContent(array $sections, array $items): void
    {
        $page = $this->pageRepository->getPageBySlug('yummy');

        if (!$page) {
            throw new \RuntimeException('Yummy page not found.');
        }

        $this->eventEditor->savePageContent(
            $page->id,
            $sections,
            $items
        );
    }
}