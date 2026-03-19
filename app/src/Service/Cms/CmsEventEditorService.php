<?php

namespace App\Service\Cms;

use App\Mapper\CmsScheduleMapper;
use App\Models\Page\SectionItem;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorPerformerRowViewModel;
use App\Models\ViewModels\Cms\Schedule\ScheduleEditorViewModel;
use App\Service\Cms\Interfaces\ICmsEventEditorService;
use App\Service\Cms\Interfaces\ICmsScheduleService;
use App\Service\Cms\Interfaces\ICmsPageSaveService;
use App\Service\Interfaces\IDanceService;

class CmsEventEditorService implements ICmsEventEditorService
{
    private ICmsScheduleService $cmsScheduleService;
    private CmsScheduleMapper $cmsScheduleMapper;
    private ICmsPageSaveService $pageSaveService;
    private IDanceService $danceService;

    public function __construct(
        ICmsScheduleService $cmsScheduleService,
        CmsScheduleMapper $cmsScheduleMapper,
        ICmsPageSaveService $pageSaveService,
        IDanceService $danceService
    ) {
        $this->cmsScheduleService = $cmsScheduleService;
        $this->cmsScheduleMapper = $cmsScheduleMapper;
        $this->pageSaveService = $pageSaveService;
        $this->danceService = $danceService;
    }

    public function getEditorData(string $eventName): ScheduleEditorViewModel
    {
        $editorViewModel = $this->cmsScheduleService->getScheduleEditorData($eventName);
        if (strtolower($eventName) !== 'dance') {
            return $editorViewModel;
        }

        return new ScheduleEditorViewModel(
            $editorViewModel->eventName,
            $editorViewModel->venues,
            $this->enrichDancePerformers($editorViewModel->performers),
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
        $this->pageSaveService->saveEditorPageContent($pageId, $sections, $items);
    }

    private function enrichDancePerformers(array $performers): array
    {
        $artistItems = $this->getDanceFeaturedArtistImageRows();
        $result = [];

        foreach ($performers as $index => $performer) {
            if (!$performer instanceof ScheduleEditorPerformerRowViewModel) {
                continue;
            }

            $artistItem = $artistItems[$index] ?? null;
            $artistSectionItemId = $artistItem instanceof SectionItem ? $artistItem->id : 0;
            $artistImagePath = $artistItem instanceof SectionItem ? (string) ($artistItem->image ?? '') : '';

            $result[] = new ScheduleEditorPerformerRowViewModel(
                $performer->id,
                $performer->name,
                $performer->type,
                $performer->description,
                $artistSectionItemId,
                $artistImagePath
            );
        }

        return $result;
    }

    private function getDanceFeaturedArtistImageRows(): array
    {
        $danceHome = $this->danceService->getDanceHomePage();
        $artistsSection = $danceHome->getSection('dance_artists');
        $featuredArtistImageRows = [];

        if ($artistsSection === null) {
            return $featuredArtistImageRows;
        }

        foreach ($artistsSection->getItemsByCategorie('artist') as $item) {
            if ($item instanceof SectionItem) {
                $featuredArtistImageRows[] = $item;
            }
        }

        return $featuredArtistImageRows;
    }
}
