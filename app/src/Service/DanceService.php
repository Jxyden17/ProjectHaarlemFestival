<?php

namespace App\Service;

use App\Models\Dance\DanceDetailPageInput;
use App\Models\Dance\DanceIndexPageInput;
use App\Models\Event\EventDetailPageModel;
use App\Models\Event\EventModel;
use App\Models\Page\Page;
use App\Repository\Interfaces\IDanceRepository;
use App\Repository\Interfaces\IScheduleRepository;
use App\Service\Interfaces\IDanceService;
use App\Service\Interfaces\IPageService;
use App\Service\Interfaces\IScheduleService;

class DanceService implements IDanceService
{
    private const DANCE_EVENT_NAME = 'Dance';

    private IDanceRepository $danceRepository;
    private IScheduleRepository $scheduleRepository;
    private IPageService $pageService;
    private IScheduleService $scheduleService;

    public function __construct(IDanceRepository $danceRepository, IScheduleRepository $scheduleRepository, IPageService $pageService, IScheduleService $scheduleService)
    {
        $this->danceRepository = $danceRepository;
        $this->scheduleRepository = $scheduleRepository;
        $this->pageService = $pageService;
        $this->scheduleService = $scheduleService;
    }

    public function getDanceIndexPage(): DanceIndexPageInput
    {
        $homePage = $this->getDanceHomeContentPage();
        $event = $this->getDanceEvent();
        $performers = [];
        $detailPages = [];
        $venues = [];

        if ($event instanceof EventModel) {
            $performers = $this->scheduleRepository->getPerformersByEventId($event->id);
            $detailPages = $this->danceRepository->getDetailPagesByEventId($event->id);
            $venues = $this->scheduleRepository->getVenuesByEventId($event->id);
        }

        return new DanceIndexPageInput(
            $homePage,
            $this->scheduleService->getScheduleDataForEvent(
                self::DANCE_EVENT_NAME,
                $this->resolveScheduleTitle($homePage)
            ),
            $performers,
            $detailPages,
            $venues
        );
    }

    public function getDanceDetailPage(EventDetailPageModel $detailMeta): DanceDetailPageInput
    {
        return new DanceDetailPageInput(
            $this->getDanceDetailContentPage($detailMeta->pageSlug),
            $detailMeta,
            $detailMeta->performerId === null
                ? []
                : $this->scheduleService->getScheduleRowsByPerformerId(self::DANCE_EVENT_NAME, $detailMeta->performerId)
        );
    }

    public function getDanceHomeContentPage(): Page
    {
        return $this->pageService->getPageBySlug('dance-home', 'Dance Home');
    }

    private function getDanceDetailContentPage(string $slug): Page
    {
        return $this->pageService->getPageBySlug($slug, 'Dance Detail');
    }

    public function getDanceDetailPageBySlug(string $pageSlug): ?EventDetailPageModel
    {
        return $this->danceRepository->findDetailPageByPageSlug($pageSlug);
    }

    public function getPublishedDanceDetailPages(): array
    {
        $event = $this->getDanceEvent();
        if (!$event instanceof EventModel) {
            return [];
        }

        return $this->danceRepository->getDetailPagesByEventId($event->id);
    }

    private function getDanceEvent(): ?EventModel
    {
        return $this->scheduleRepository->findEventByName(self::DANCE_EVENT_NAME);
    }

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
