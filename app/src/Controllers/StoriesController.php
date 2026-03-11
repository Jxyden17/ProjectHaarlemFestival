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

        $scheduleViewModel = $this->scheduleService->getScheduleDataForEvent('tellingstory', 'Stories Schedule');

        $viewData = [
            'pageTitle' => $page->title,
            'hero'      => $page->getSection('hero'),
            'callout'   => $page->getSection('callout'),
            'grid'      => $page->getSection('grid'),
            'venues'    => $page->getSection('venues'),
            'schedule'  => $scheduleViewModel,
            'scheduleData' => $scheduleViewModel,
            'explore'   => $page->getSection('explore'),
            'faq'       => $page->getSection('faq')
        ];
        $this->render('Stories/index', $viewData);
    }

    public function details($slug = null): void
    {
        if (!$slug) {
            $slug = isset($_GET['slug']) ? $_GET['slug'] : null;
        }

        if (!$slug) {
            http_response_code(404);
            $this->render('shared/error', [
                'errorTitle' => 'Page not found',
                'errorMessage' => 'The page you requested does not exist.',
            ]);
            return;
        }

        $page = $this->pageService->getPageBySlug($slug);
        if (trim((string) ($page->title ?? '')) === '') {
            http_response_code(404);
            $this->render('shared/error', [
                'errorTitle' => 'Story not found',
                'errorMessage' => 'The story page you requested does not exist.',
            ]);
            return;
        }

        $viewData = [
            'pageTitle' => $page->title,
            'slug' => $slug,
            'hero' => $page->getSection('hero'),
            'gallery' => $page->getSection('gallery'),
            'about' => $page->getSection('about'),
            'featured' => $page->getSection('featured'),
            'booking' => $page->getSection('booking'),
        ];

        $this->render('Stories/details', $viewData);
    }
}
