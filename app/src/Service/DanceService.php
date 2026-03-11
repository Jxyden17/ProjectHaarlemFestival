<?php

namespace App\Service;

use App\Models\Event\EventDetailPageModel;
use App\Models\Event\EventModel;
use App\Models\Page\Page;
use App\Repository\Interfaces\IDanceRepository;
use App\Repository\Interfaces\IScheduleRepository;
use App\Service\Interfaces\IDanceService;
use App\Service\Interfaces\IPageService;

class DanceService implements IDanceService
{
    private const DANCE_EVENT_NAME = 'Dance';

    private IDanceRepository $danceRepository;
    private IScheduleRepository $scheduleRepository;
    private IPageService $pageService;

    public function __construct(
        IDanceRepository $danceRepository,
        IScheduleRepository $scheduleRepository,
        IPageService $pageService
    )
    {
        $this->danceRepository = $danceRepository;
        $this->scheduleRepository = $scheduleRepository;
        $this->pageService = $pageService;
    }

    public function getDanceVenues(): array
    {
        $event = $this->getDanceEvent();
        if (!$event instanceof EventModel) {
            return [];
        }

        return $this->scheduleRepository->getVenuesByEventId($event->id);
    }

    public function getDancePerformers(): array
    {
        $event = $this->getDanceEvent();
        if (!$event instanceof EventModel) {
            return [];
        }

        return $this->scheduleRepository->getPerformersByEventId($event->id);
    }

    public function getDanceIndexData(): array
    {
        $event = $this->getDanceEvent();
        
        return [
            'venues' => $this->scheduleRepository->getVenuesByEventId($event->id),
            'performers' => $this->scheduleRepository->getPerformersByEventId($event->id),
            'detailPages' => $this->danceRepository->getDetailPagesByEventId($event->id),
        ];
    }

    public function getDanceHomePage(): Page
    {
        return $this->pageService->getPageBySlug('dance-home', 'Dance Home');
    }

    public function getDanceDetailPage(string $slug): Page
    {
        return $this->pageService->getPageBySlug($slug, 'Dance Detail');
    }

    public function getDanceDetailPageBySlug(string $detailSlug): ?EventDetailPageModel
    {
        return $this->danceRepository->findDetailPageBySlug($detailSlug);
    }

    public function getPublishedDanceDetailPages(): array
    {
        $event = $this->getDanceEvent();
        if (!$event instanceof EventModel) {
            return [];
        }

        return $this->danceRepository->getDetailPagesByEventId($event->id);
    }

    public function getDanceScheduleTitle(): string
    {
        $homeContent = $this->getDanceHomePage();
        $scheduleSection = $homeContent->getSection('dance_schedule');
        $scheduleTitle = $scheduleSection !== null ? trim((string)$scheduleSection->title) : '';

        if ($scheduleTitle === '') {
            return 'DANCE! Festival Schedule';
        }

        return $scheduleTitle;
    }

    private function getDanceEvent(): ?EventModel
    {
        return $this->scheduleRepository->findEventByName(self::DANCE_EVENT_NAME);
    }
}
