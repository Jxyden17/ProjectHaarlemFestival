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

        $viewData = [
            'pageTitle' => $page->title,
            'hero' => $page->getSection('hero'),
            'about' => $page->getSection('about'),
            'gallery' => $page->getSection('gallery'),
            'featured' => $page->getSection('featured'),
            'booking' => $page->getSection('booking'),
        ];

        $this->render('Stories/details', $viewData);
    }
}
