<?php

namespace App\Service;

use App\Mapper\ScheduleViewModelMapper;
use App\Service\Interfaces\IPageService;
use App\Service\Interfaces\IScheduleService;
use App\Service\Interfaces\IStoriesPageService;

class StoriesPageService implements IStoriesPageService
{
    public function __construct(
        private IPageService $pageService,
        private IScheduleService $scheduleService,
        private ScheduleViewModelMapper $scheduleViewModelMapper
    ) {
    }

    public function getIndexViewData(): array
    {
        $page = $this->pageService->buildPage(3);
        if ($page === null) {
            throw new \RuntimeException('The page you requested does not exist.');
        }

        $scheduleViewModel = $this->scheduleViewModelMapper->mapScheduleData(
            $this->scheduleService->getScheduleDataForEvent('tellingstory', 'Stories Schedule')
        );

        return [
            'pageTitle' => $page->title,
            'hero' => $page->getSection('hero'),
            'callout' => $page->getSection('callout'),
            'grid' => $page->getSection('grid'),
            'venues' => $page->getSection('venues'),
            'schedule' => $page->getSection('schedule'),
            'scheduleData' => $scheduleViewModel,
            'explore' => $page->getSection('explore'),
            'faq' => $page->getSection('faq'),
        ];
    }

    public function getDetailViewData(string $slug): array
    {
        $normalizedSlug = trim($slug);
        if ($normalizedSlug === '') {
            throw new \RuntimeException('The page you requested does not exist.');
        }

        $page = $this->pageService->getPageBySlug($normalizedSlug, ucfirst(str_replace('-', ' ', $normalizedSlug)));
        if ((int) ($page->id ?? 0) <= 0) {
            throw new \RuntimeException('The page you requested does not exist.');
        }

        $bookingData = $this->resolveBookingData((string) $page->title);

        return [
            'pageTitle' => $page->title,
            'hero' => $page->getSection('hero'),
            'about' => $page->getSection('about'),
            'gallery' => $page->getSection('gallery'),
            'featured' => $page->getSection('featured'),
            'booking' => $page->getSection('booking'),
            'bookingSessionId' => $bookingData['bookingSessionId'],
            'bookingPricingType' => $bookingData['bookingPricingType'],
            'bookingMinimumPrice' => $bookingData['bookingMinimumPrice'],
        ];
    }

    private function resolveBookingData(string $pageTitle): array
    {
        $bookingSessionId = null;
        $bookingPricingType = 'fixed';
        $bookingMinimumPrice = null;

        $scheduleSessions = $this->scheduleService->getScheduleSessionsByPerformerName('tellingstory', $pageTitle);

        if ($scheduleSessions !== []) {
            $firstSession = $scheduleSessions[0];
            $bookingSessionId = (int) ($firstSession->id ?? 0);
            $numericPrice = (float) ($firstSession->price ?? 0);

            if ($numericPrice <= 0.0) {
                $bookingPricingType = 'pay_as_you_like';
                $bookingMinimumPrice = 5.0;
            }
        }

        return [
            'bookingSessionId' => $bookingSessionId,
            'bookingPricingType' => $bookingPricingType,
            'bookingMinimumPrice' => $bookingMinimumPrice,
        ];
    }
}
