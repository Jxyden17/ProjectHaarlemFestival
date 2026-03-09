<?php

namespace App\Service;

use App\Models\Event\EventDetailPageModel;
use App\Models\Page\Page;
use App\Models\ViewModels\Dance\DanceBannerStatsViewModel;
use App\Repository\Interfaces\IDanceRepository;
use App\Service\Interfaces\IDanceService;
use App\Service\Interfaces\IPageService;

class DanceService implements IDanceService
{
    private IDanceRepository $danceRepository;
    private IPageService $pageService;

    public function __construct(IDanceRepository $danceRepository, IPageService $pageService)
    {
        $this->danceRepository = $danceRepository;
        $this->pageService = $pageService;
    }

    public function getDanceBannerStats(): DanceBannerStatsViewModel
    {
        $event = $this->danceRepository->findDanceEvent();

        if ($event === null) {
            throw new \RuntimeException('Dance event not found.');
        }

        return new DanceBannerStatsViewModel(
            $this->danceRepository->countSessionsByEventId($event->id),
            $this->danceRepository->countDistinctVenuesByEventId($event->id)
        );
    }

    public function getDanceVenues(): array
    {
        $event = $this->danceRepository->findDanceEvent();

        if ($event === null) {
            return [];
        }

        return $this->danceRepository->getVenuesByEventId($event->id);
    }

    public function getDancePerformers(): array
    {
        $event = $this->danceRepository->findDanceEvent();

        if ($event === null) {
            return [];
        }

        return $this->danceRepository->getPerformersByEventId($event->id);
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
        $event = $this->danceRepository->findDanceEvent();
        if ($event === null) {
            return [];
        }

        return $this->danceRepository->getPublishedDetailPagesByEventId($event->id);
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
}
