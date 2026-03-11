<?php

namespace App\Service\Cms;

use App\Repository\Interfaces\IPageRepository;
use App\Service\Cms\Interfaces\ICmsPageSaveService;

class CmsPageSaveService implements ICmsPageSaveService
{
    private IPageRepository $pageRepository;

    public function __construct(IPageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    public function savePageContent(int $pageId, ?string $pageTitle, array $sections): void
    {
        if ($pageTitle !== null) {
            $this->pageRepository->updatePageName($pageId, $pageTitle);
        }

        $sectionIdsByType = $this->pageRepository->findSectionIdsByPageId($pageId);

        foreach ($sections as $section) {
            if (!is_array($section)) {
                continue;
            }

            $sectionType = (string)($section['type'] ?? '');
            $sectionId = $sectionIdsByType[$sectionType] ?? null;
            if ($sectionId === null) {
                throw new \RuntimeException('Missing section for update: ' . $sectionType);
            }

            $this->pageRepository->updateSectionById(
                $sectionId,
                isset($section['title']) ? (string)$section['title'] : null,
                isset($section['subtitle']) ? (string)$section['subtitle'] : null,
                isset($section['description']) ? (string)$section['description'] : null,
                (int)($section['order_index'] ?? 0)
            );

            $items = is_array($section['items'] ?? null) ? $section['items'] : [];
            if ($items !== []) {
                $this->pageRepository->saveOrUpdateSectionItems($sectionId, $items);
            }
        }
    }

    public function savePageContentBySlug(
        string $pageSlug,
        ?string $pageTitle,
        array $sections,
        string $missingPageMessage = 'Page not found.'
    ): void {
        $pageId = $this->pageRepository->findPageIdBySlug($pageSlug);
        if ($pageId === null) {
            throw new \RuntimeException($missingPageMessage);
        }

        $this->savePageContent($pageId, $pageTitle, $sections);
    }
}
