<?php
namespace App\Controllers;

use App\Mapper\ScheduleViewModelMapper;
use App\Service\Interfaces\IPageService;
use App\Service\Interfaces\IScheduleService;

class StoriesController extends BaseController
{
    private IPageService $pageService;
    private IScheduleService $scheduleService;
    private ScheduleViewModelMapper $scheduleViewModelMapper;

    public function __construct(
        IPageService $pageService,
        IScheduleService $scheduleService,
        ScheduleViewModelMapper $scheduleViewModelMapper
    ) 
    {
        $this->pageService = $pageService;
        $this->scheduleService = $scheduleService;
        $this->scheduleViewModelMapper = $scheduleViewModelMapper;
    }

    public function index(): void
    {
        $pageId = 3;
        
        $page = $this->pageService->buildPage($pageId);
        if (!$page) {
            http_response_code(404);
            $this->render('shared/error', [
                'errorTitle' => 'Page not found',
                'errorMessage' => 'The page you requested does not exist.',
            ]);
            return;
        }

        $scheduleViewModel = $this->scheduleViewModelMapper->mapScheduleData(
            $this->scheduleService->getScheduleDataForEvent('tellingstory', 'Stories Schedule')
        );

        $viewData = [
            'pageTitle' => $page->title,
            'hero'      => $page->getSection('hero'),
            'callout'   => $page->getSection('callout'),
            'grid'      => $page->getSection('grid'),
            'venues'    => $page->getSection('venues'),
            'schedule'  => $page->getSection('schedule'),
            'scheduleData' => $scheduleViewModel,
            'explore'   => $page->getSection('explore'),
            'faq'       => $page->getSection('faq')
        ];
        $this->render('Stories/index', $viewData);
    }

    public function details($slug = null): void
    {
        if (is_array($slug)) {
            $slug = $slug['slug'] ?? $_GET['slug'] ?? null;
        } elseif (!$slug) {
            $slug = $_GET['slug'] ?? null;
        }

        $slug = trim((string)$slug);
        if ($slug === '') {
            http_response_code(404);
            $this->render('shared/error', [
                'errorTitle' => 'Page not found',
                'errorMessage' => 'The page you requested does not exist.',
            ]);
            return;
        }

        $page = $this->pageService->getPageBySlug($slug, ucfirst(str_replace('-', ' ', $slug)));
        if ((int)($page->id ?? 0) <= 0) {
            http_response_code(404);
            $this->render('shared/error', [
                'errorTitle' => 'Page not found',
                'errorMessage' => 'The page you requested does not exist.',
            ]);
            return;
            
        }
        $bookingSessionId = null;
        $bookingPricingType = 'fixed';
        $bookingMinimumPrice = null;

        $scheduleSessions = $this->scheduleService->getScheduleSessionsByPerformerName('tellingstory', $page->title);

        if ($scheduleSessions !== []) {
            $firstSession = $scheduleSessions[0];

            $bookingSessionId = (int) ($firstSession->id ?? 0);

            $numericPrice = (float) ($firstSession->price ?? 0);

            if ($numericPrice <= 0.0) {
                $bookingPricingType = 'pay_as_you_like';
                $bookingMinimumPrice = 5.0;
            }
        }

        $viewData = [
            'pageTitle' => $page->title,
            'hero' => $page->getSection('hero'),
            'about' => $page->getSection('about'),
            'gallery' => $page->getSection('gallery'),
            'featured' => $page->getSection('featured'),
            'booking' => $page->getSection('booking'),
            'bookingSessionId' => $bookingSessionId,
            'bookingPricingType' => $bookingPricingType,
            'bookingMinimumPrice' => $bookingMinimumPrice,
        ];

        $this->render('Stories/details', $viewData);
    }
}
