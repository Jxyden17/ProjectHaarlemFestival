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
    private ?Page $danceHomePage = null;
    private bool $danceHomePageLoaded = false;
    private ?EventModel $danceEvent = null;
    private bool $danceEventLoaded = false;
    private ?array $danceVenues = null;
    private ?array $dancePerformers = null;
    private ?array $publishedDetailPages = null;

    public function __construct(IDanceRepository $danceRepository, IPageService $pageService)
    {
        $this->danceRepository = $danceRepository;
        $this->pageService = $pageService;
    }

    public function getDanceVenues(): array
    {
        if (is_array($this->danceVenues)) {
            return $this->danceVenues;
        }

        $event = $this->getDanceEvent();
        if (!$event instanceof EventModel) {
            return [];
        }

        $this->danceVenues = $this->danceRepository->getVenuesByEventId($event->id);

        return $this->danceVenues;
    }

    public function getDancePerformers(): array
    {
        if (is_array($this->dancePerformers)) {
            return $this->dancePerformers;
        }

        $event = $this->getDanceEvent();
        if (!$event instanceof EventModel) {
            return [];
        }

        $this->dancePerformers = $this->danceRepository->getPerformersByEventId($event->id);

        return $this->dancePerformers;
    }

    public function getDanceHomePage(): Page
    {
        if (!$this->danceHomePageLoaded) {
            $this->danceHomePage = $this->pageService->getPageBySlug('dance-home', 'Dance Home');
            $this->danceHomePageLoaded = true;
        }

        return $this->danceHomePage ?? new Page('Dance Home', 'dance-home');
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
        if (is_array($this->publishedDetailPages)) {
            return $this->publishedDetailPages;
        }

        $event = $this->getDanceEvent();
        if (!$event instanceof EventModel) {
            return [];
        }

        $this->publishedDetailPages = $this->danceRepository->getDetailPagesByEventId($event->id);

        return $this->publishedDetailPages;
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
        if (!$this->danceEventLoaded) {
            $this->danceEvent = $this->danceRepository->findEventByName(self::DANCE_EVENT_NAME);
            $this->danceEventLoaded = true;
        }

        return $this->danceEvent;
    }
}
