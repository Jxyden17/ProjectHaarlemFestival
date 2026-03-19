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

    public function __construct(IDanceRepository $danceRepository, IPageService $pageService, IScheduleService $scheduleService)
    {
        $this->danceRepository = $danceRepository;
        $this->pageService = $pageService;
        $this->scheduleService = $scheduleService;
    }

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

    public function getDanceDetailData(EventDetailPageModel $detailMeta): DanceDetailData
    {
        return new DanceDetailData(
            $this->getDanceDetailContentPage($detailMeta->pageSlug),
            $detailMeta,
            $detailMeta->performerId === null
                ? []
                : $this->scheduleService->getScheduleSessionsByPerformerId(self::DANCE_EVENT_NAME, $detailMeta->performerId)
        );
    }

    public function getDanceHomePage(): Page
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

    public function getDanceDetailPages(): array
    {
        $event = $this->getDanceEvent();
        if (!$event instanceof EventModel) {
            return [];
        }

        return $this->danceRepository->getDetailPagesByEventId($event->id);
    }

    private function getDanceEvent(): ?EventModel
    {
        return $this->scheduleService->findEventByName(self::DANCE_EVENT_NAME);
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
