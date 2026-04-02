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

    public function saveEditorPageContent(int $pageId, array $sections, array $items): void
    {
        $existingItems = $this->loadExistingItemMetadata($pageId);
        $normalizedSections = [];
        $sectionOrder = 0;

        foreach ($sections as $sectionType => $sectionData) {
            if (!is_array($sectionData)) {
                continue;
            }

            $sectionItems = is_array($items[$sectionType] ?? null) ? $items[$sectionType] : [];
            $normalizedSections[] = [
                'type' => (string) $sectionType,
                'title' => $this->normalizeOptionalString($sectionData['title'] ?? null),
                'subtitle' => $this->normalizeOptionalString($sectionData['subtitle'] ?? $sectionData['subTitle'] ?? null),
                'description' => $this->normalizeOptionalString($sectionData['description'] ?? null),
                'order_index' => $sectionOrder++,
                'items' => $this->normalizeSectionItems($sectionItems, $existingItems),
            ];
        }

        $this->savePageContent($pageId, null, $normalizedSections);
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

    private function loadExistingItemMetadata(int $pageId): array
    {
        $metadata = [];
        $page = $this->pageRepository->findPageById($pageId);
        if ($page === null) {
            return $metadata;
        }

        foreach ($page->sections as $section) {
            foreach ($section->items as $item) {
                $itemId = (int) ($item->id ?? 0);
                if ($itemId <= 0) {
                    continue;
                }

                $metadata[$itemId] = [
                    'item_category' => (string) ($item->category ?? ''),
                    'image_path' => isset($item->image) ? (string) $item->image : null,
                ];
            }
        }

        return $metadata;
    }

    private function normalizeSectionItems(array $sectionItems, array $existingItems): array
    {
        $normalizedItems = [];
        $orderIndex = 0;

        foreach ($sectionItems as $itemData) {
            if (!is_array($itemData)) {
                continue;
            }

            $itemId = (int) ($itemData['id'] ?? 0);
            if ($itemId <= 0) {
                continue;
            }

            $existingItem = $existingItems[$itemId] ?? null;
            $itemCategory = trim((string) ($itemData['item_category'] ?? ($existingItem['item_category'] ?? '')));
            if ($itemCategory === '') {
                continue;
            }

            $normalizedItems[] = [
                'id' => $itemId,
                'title' => $this->normalizeOptionalString($itemData['title'] ?? null) ?? '',
                'item_subtitle' => $this->normalizeOptionalString($itemData['item_subtitle'] ?? null),
                'content' => $this->normalizeOptionalString($itemData['content'] ?? null),
                'image_path' => $this->resolveOptionalField($itemData, 'image_path', $existingItem['image_path'] ?? null),
                'link_url' => $this->normalizeOptionalString($itemData['link_url'] ?? null),
                'duration' => $this->normalizeOptionalString($itemData['duration'] ?? null),
                'icon_class' => $this->normalizeOptionalString($itemData['icon_class'] ?? null),
                'order_index' => array_key_exists('order_index', $itemData)
                    ? (int) $itemData['order_index']
                    : $orderIndex++,
                'item_category' => $itemCategory,
            ];
        }

        return $normalizedItems;
    }

    private function resolveOptionalField(array $itemData, string $key, ?string $fallback): ?string
    {
        if (!array_key_exists($key, $itemData)) {
            return $fallback;
        }

        return $this->normalizeOptionalString($itemData[$key]);
    }

    private function normalizeOptionalString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);
        return $normalized === '' ? null : $normalized;
    }
}
