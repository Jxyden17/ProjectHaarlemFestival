<?php

namespace App\Service;

use App\Models\Event\EventDetailPageModel;
use App\Models\Event\EventModel;
use App\Models\Page\Page;
use App\Repository\Interfaces\IDanceRepository;
use App\Service\Interfaces\IDanceService;
use App\Service\Interfaces\IPageService;

class DanceService implements IDanceService
{
    private const DANCE_EVENT_NAME = 'Dance';

    private IDanceRepository $danceRepository;
    private IPageService $pageService;

    public function __construct(IDanceRepository $danceRepository, IPageService $pageService)
    {
        $this->danceRepository = $danceRepository;
        $this->pageService = $pageService;
    }

    public function getDanceVenues(): array
    {
        $event = $this->getDanceEvent();
        if (!$event instanceof EventModel) {
            return [];
        }

        return $this->danceRepository->getVenuesByEventId($event->id);
    }

    public function getDancePerformers(): array
    {
        $event = $this->getDanceEvent();
        if (!$event instanceof EventModel) {
            return [];
        }

        return $this->danceRepository->getPerformersByEventId($event->id);
    }

    public function getDanceIndexData(): array
    {
        $event = $this->getDanceEvent();
        
        return [
            'venues' => $this->danceRepository->getVenuesByEventId($event->id),
            'performers' => $this->danceRepository->getPerformersByEventId($event->id),
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

    public function getDanceDetailPageByPublicSlug(string $publicSlug): ?EventDetailPageModel
    {
        return $this->danceRepository->findDetailPageByPublicSlug($publicSlug);
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
        return $this->danceRepository->findEventByName(self::DANCE_EVENT_NAME);
    }
}
