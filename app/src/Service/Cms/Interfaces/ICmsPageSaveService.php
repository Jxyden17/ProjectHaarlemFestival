<?php

namespace App\Service\Cms\Interfaces;

interface ICmsPageSaveService
{
    public function savePageContent(int $pageId, ?string $pageTitle, array $sections): void;

    public function saveEditorPageContent(int $pageId, array $sections, array $items): void;

    public function savePageContentBySlug(
        string $pageSlug,
        ?string $pageTitle,
        array $sections,
        string $missingPageMessage = 'Page not found.'
    ): void;
}
