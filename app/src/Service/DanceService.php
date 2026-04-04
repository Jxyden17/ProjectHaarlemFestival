<?php

namespace App\Service;

use App\Models\Dance\DanceDetailData;
use App\Models\Dance\DanceIndexData;
use App\Models\Event\EventDetailPageModel;
use App\Models\Event\EventModel;
use App\Models\Page\Page;
use App\Models\Schedule\ScheduleData;
use App\Repository\Interfaces\IDanceRepository;
use App\Service\Interfaces\IDanceService;
use App\Service\Interfaces\IPageService;
use App\Service\Interfaces\IScheduleService;

class DanceService implements IDanceService
{
    private const DANCE_EVENT_NAME = 'Dance';

    private IDanceRepository $danceRepository;
    private IPageService $pageService;
    private IScheduleService $scheduleService;

    // Stores dance dependencies so public dance orchestration stays centralized in one service.
    public function __construct(IDanceRepository $danceRepository, IPageService $pageService, IScheduleService $scheduleService)
    {
        $this->danceRepository = $danceRepository;
        $this->pageService = $pageService;
        $this->scheduleService = $scheduleService;
    }

    // Builds the full public dance index payload so the controller does not need to coordinate pages, schedule, performers, and venues itself.
    public function getDanceIndexData(): DanceIndexData
    {
        $homePage = $this->getDanceHomePage();
        $scheduleTitle = $this->resolveScheduleTitle($homePage);
        $resources = $this->scheduleService->getEventResources(self::DANCE_EVENT_NAME, $scheduleTitle);

        if (!is_array($resources)) {
            return new DanceIndexData(
                $homePage,
                new ScheduleData($scheduleTitle, self::DANCE_EVENT_NAME, [], false),
                [],
                [],
                []
            );
        }

        $event = $resources['event'] ?? null;
        $schedule = $resources['schedule'] instanceof ScheduleData
            ? $resources['schedule']
            : new ScheduleData($scheduleTitle, self::DANCE_EVENT_NAME, [], false);
        $performers = is_array($resources['performers'] ?? null) ? $resources['performers'] : [];
        $venues = is_array($resources['venues'] ?? null) ? $resources['venues'] : [];
        $detailPages = $event instanceof EventModel
            ? $this->danceRepository->getDetailPagesByEventId($event->id)
            : [];

        return new DanceIndexData(
            $homePage,
            $schedule,
            $performers,
            $detailPages,
            $venues
        );
    }

    // Builds one public dance detail payload from a slug so the caller gets page content, metadata, and performer sessions together. Example: slug 'urban-echo' -> DanceDetailData.
    public function getDanceDetailData(string $pageSlug): ?DanceDetailData
    {
        $detailMeta = $this->danceRepository->findDetailPageByPageSlug($pageSlug);
        if (!$detailMeta instanceof EventDetailPageModel) {
            return null;
        }

        $contentPage = $this->getDanceDetailContentPage($detailMeta->pageSlug);
        if (!$contentPage instanceof Page) {
            return null;
        }

        return new DanceDetailData(
            $contentPage,
            $detailMeta,
            $detailMeta->performerId === null
                ? []
                : $this->scheduleService->getScheduleSessionsByPerformerId(self::DANCE_EVENT_NAME, $detailMeta->performerId)
        );
    }

    // Returns the dance home content page so public and CMS flows use the same underlying page record.
    public function getDanceHomePage(): Page
    {
        return $this->pageService->getPageBySlug('dance-home', 'Dance Home');
    }

    // Finds the content page behind one dance detail slug so public detail routes can return null when the page content is missing.
    private function getDanceDetailContentPage(string $slug): ?Page
    {
        return $this->pageService->findPageBySlug($slug);
    }

    // Finds dance detail metadata by slug so media uploads and other callers can target a page without loading all detail data. Example: slug 'urban-echo' -> EventDetailPageModel.
    public function getDanceDetailPageBySlug(string $pageSlug): ?EventDetailPageModel
    {
        return $this->danceRepository->findDetailPageByPageSlug($pageSlug);
    }

    // Returns all dance detail pages for the Dance event so CMS lists can link to each detail editor.
    public function getDanceDetailPages(): array
    {
        $event = $this->getDanceEvent();
        if (!$event instanceof EventModel) {
            return [];
        }

        return $this->danceRepository->getDetailPagesByEventId($event->id);
    }

    // Finds the Dance event record so other dance queries can hang off the correct event id.
    private function getDanceEvent(): ?EventModel
    {
        return $this->scheduleService->findEventByName(self::DANCE_EVENT_NAME);
    }

    // Resolves the homepage schedule heading so the dance page can fall back to a safe default when content is blank.
    private function resolveScheduleTitle(Page $homePage): string
    {
        $scheduleSection = $homePage->getSection('dance_schedule');
        $scheduleTitle = $scheduleSection !== null ? trim((string)$scheduleSection->title) : '';

        if ($scheduleTitle === '') {
            return 'DANCE! Festival Schedule';
        }

        return $scheduleTitle;
    }
}
