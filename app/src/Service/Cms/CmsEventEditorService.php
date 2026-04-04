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
    private $pageRepository;

    // Stores CMS event editor dependencies so schedule data, page saves, and dance-specific enrichment stay coordinated.
    public function __construct(
        ICmsScheduleService $cmsScheduleService,
        CmsScheduleMapper $cmsScheduleMapper,
        ICmsPageSaveService $pageSaveService,
        IDanceService $danceService,
        $pageRepository
    ) {
        $this->cmsScheduleService = $cmsScheduleService;
        $this->cmsScheduleMapper = $cmsScheduleMapper;
        $this->pageSaveService = $pageSaveService;
        $this->danceService = $danceService;
        $this->pageRepository = $pageRepository;
    }

    // Returns the CMS editor payload for one event and enriches Dance performers with artist image metadata when needed.
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

    // Merges posted schedule rows into the editor payload so failed CMS saves preserve user-entered values.
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

    // Saves linked page content so the event editor can persist page sections through the shared CMS page save service.
    public function savePageContent(int $pageId, array $sections, array $items): void
    {
        $this->pageSaveService->saveEditorPageContent($pageId, $sections, $items);
    }

    // Adds dance artist image metadata to performer rows so the Dance event editor can manage linked artist cards.
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

    // Returns the featured dance artist image items so schedule performer rows can align with the dance home artist section.
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

    public function getTourDetailPages(): array
    {
        $pages = [];
        foreach ($this->pageRepository->getTourDetailPages() as $row) {
            $pages[] = [
                'id' => (int) ($row['id'] ?? 0),
                'name' => (string) ($row['page_name'] ?? ''),
            ];
        }
        return $pages;
    }
}
