<?php
namespace App\Controllers;

use App\Service\Interfaces\IPageService;
use App\Service\Interfaces\IScheduleService;

class StoriesController extends BaseController
{
    private IPageService $pageService;
    private IScheduleService $scheduleService;

    public function __construct(IPageService $pageService, IScheduleService $scheduleService) 
    {
        $this->pageService = $pageService;
        $this->scheduleService = $scheduleService;
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

        $scheduleViewModel = $this->scheduleService->getScheduleDataForEvent('TellingStory', 'Stories Schedule');

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
        $scheduleRows = $this->scheduleService->getScheduleRowsByPerformerName('TellingStory', $page->title);

        if ($scheduleRows !== []) {
            $firstRow = $scheduleRows[0];
            $bookUrl = (string) ($firstRow->bookUrl ?? '');

            $query = parse_url($bookUrl, PHP_URL_QUERY);
            if (is_string($query)) {
                parse_str($query, $params);
                $bookingSessionId = isset($params['session_id']) ? (int) $params['session_id'] : null;
            }

            $priceText = trim((string) ($firstRow->price ?? ''));
            $normalizedPrice = str_replace(',', '.', preg_replace('/[^0-9,.\-]/', '', $priceText) ?? '');
            $numericPrice = (float) $normalizedPrice;

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
