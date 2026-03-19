<?php

namespace App\Service\Cms;

use App\Mapper\CmsScheduleMapper;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorViewModel;
use App\Repository\Interfaces\IPageRepository;
use App\Service\Cms\Interfaces\ICmsEventEditorService;
use App\Service\Cms\Interfaces\ICmsPageSaveService;
use App\Service\Interfaces\IScheduleService;

class CmsEventEditorService implements ICmsEventEditorService
{
    private IScheduleService $scheduleService;
    private CmsScheduleMapper $cmsScheduleMapper;
    private IPageRepository $pageRepository;
    private ICmsPageSaveService $pageSaveService;

    public function __construct(
        IScheduleService $scheduleService,
        CmsScheduleMapper $cmsScheduleMapper,
        IPageRepository $pageRepository,
        ICmsPageSaveService $pageSaveService
    ) {
        $this->scheduleService = $scheduleService;
        $this->cmsScheduleMapper = $cmsScheduleMapper;
        $this->pageRepository = $pageRepository;
        $this->pageSaveService = $pageSaveService;
    }

    public function getEditorData(string $eventName): ScheduleEditorViewModel
    {
        $editorViewModel = $this->scheduleService->getScheduleEditorData($eventName);
        if (strtolower($eventName) !== 'dance') {
            return $editorViewModel;
        }

        return new ScheduleEditorViewModel(
            $editorViewModel->eventName,
            $editorViewModel->venues,
            $this->cmsScheduleMapper->applyDanceFeaturedArtistImageMetadata($eventName, $editorViewModel->performers),
            $editorViewModel->sessions
        );
    }

    public function mergePostedEditorData(
        string $eventName,
        ScheduleEditorViewModel $editorData,
        array $postedVenues,
        array $postedPerformers,
        array $postedSessions
    ): ScheduleEditorViewModel {
        $venues = $editorData->venues;
        if (!empty($postedVenues)) {
            $venues = $this->cmsScheduleMapper->mapVenueViewModels($postedVenues);
        }

        $performers = $editorData->performers;
        if (!empty($postedPerformers)) {
            $existingPerformers = strtolower($eventName) === 'dance' ? $editorData->performers : [];
            $performers = $this->cmsScheduleMapper->mapPerformerViewModels($postedPerformers, $existingPerformers);
        }

        $sessions = $editorData->sessions;
        if (!empty($postedSessions)) {
            $sessions = $this->cmsScheduleMapper->mapSessionViewModels($postedSessions);
        }

        return new ScheduleEditorViewModel($editorData->eventName, $venues, $performers, $sessions);
    }

    public function savePageContent(int $pageId, array $sections, array $items): void
    {
        $existingItems = $this->loadExistingItemMetadata($pageId);
        $normalizedSections = [];
        $sectionOrder = 0;

        foreach ($sections as $sectionType => $sectionData) {
            if (!is_array($sectionData)) {
                continue;
            }

            $sectionItems = is_array($items[$sectionType] ?? null) ? $items[$sectionType] : [];
            $normalizedItems = $this->normalizeSectionItems($sectionItems, $existingItems);

            $normalizedSections[] = [
                'type' => (string) $sectionType,
                'title' => $this->normalizeOptionalString($sectionData['title'] ?? null),
                'subtitle' => $this->normalizeOptionalString($sectionData['subtitle'] ?? $sectionData['subTitle'] ?? null),
                'description' => $this->normalizeOptionalString($sectionData['description'] ?? null),
                'order_index' => $sectionOrder++,
                'items' => $normalizedItems,
            ];
        }

        $this->pageSaveService->savePageContent($pageId, null, $normalizedSections);
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
                    'order_index' => (int) ($item->position ?? 0),
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
