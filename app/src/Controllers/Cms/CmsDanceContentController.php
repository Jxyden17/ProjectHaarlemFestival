<?php

namespace App\Controllers\Cms;

use App\Controllers\BaseController;
use App\Models\Page\Page;
use App\Models\Page\SectionItem;
use App\Models\Requests\Cms\DanceHomeContentRequest;
use App\Service\Interfaces\IDanceService;

class CmsDanceContentController extends BaseController
{
    private IDanceService $danceService;

    public function __construct(IDanceService $danceService)
    {
        $this->danceService = $danceService;
    }

    public function index(): void
    {
        $this->requireAdmin();

        $content = $this->danceService->getDanceHomePage();
        $this->renderCms('cms/events/dance-home', [
            'title' => 'Dance Home Content',
            'contentData' => $this->mapPageToFormData($content),
            'success' => isset($_GET['saved']),
        ]);
    }

    public function update(): void
    {
        $this->requireAdmin();
        $request = DanceHomeContentRequest::fromArray($_POST);

        try {
            $this->danceService->saveDanceHomePage($request->toArray());
            header('Location: /cms/events/dance-home?saved=1');
            exit;
        } catch (\Throwable $e) {
            $this->renderCms('cms/events/dance-home', [
                'title' => 'Dance Home Content',
                'contentData' => $this->mapPostToFormData($request->toArray()),
                'error' => $e->getMessage(),
                'success' => false,
            ]);
        }
    }

    private function mapPostToFormData(array $input): array
    {
        return [
            'schedule_title' => (string)($input['schedule_title'] ?? ''),
            'banner_badge' => (string)($input['banner_badge'] ?? ''),
            'banner_title' => (string)($input['banner_title'] ?? ''),
            'banner_description' => (string)($input['banner_description'] ?? ''),
            'artists_title' => (string)($input['artists_title'] ?? ''),
            'artists' => is_array($input['artists'] ?? null) ? $input['artists'] : [],
            'important_information_title' => (string)($input['important_information_title'] ?? ''),
            'important_information_html' => (string)($input['important_information_html'] ?? ''),
            'passes_title' => (string)($input['passes_title'] ?? ''),
            'passes' => is_array($input['passes'] ?? null) ? $input['passes'] : [],
            'capacity_title' => (string)($input['capacity_title'] ?? ''),
            'capacity_html' => (string)($input['capacity_html'] ?? ''),
            'special_title' => (string)($input['special_title'] ?? ''),
            'special_html' => (string)($input['special_html'] ?? ''),
        ];
    }

    private function mapPageToFormData(Page $page): array
    {
        $schedule = $page->getSection('dance_schedule');
        $banner = $page->getSection('dance_banner');
        $artists = $page->getSection('dance_artists');
        $info = $page->getSection('dance_info');
        $passes = $page->getSection('dance_passes');
        $capacity = $page->getSection('dance_capacity');
        $special = $page->getSection('dance_special_session');

        $artistRows = [];
        if ($artists !== null) {
            foreach ($artists->getItemsByCategorie('artist') as $item) {
                if (!$item instanceof SectionItem) {
                    continue;
                }

                $artistRows[] = [
                    'name' => $item->title,
                    'genre' => (string)($item->content ?? ''),
                    'image' => (string)($item->image ?? ''),
                ];
            }
        }

        $passRows = [];
        if ($passes !== null) {
            foreach ($passes->getItemsByCategorie('pass') as $item) {
                if (!$item instanceof SectionItem) {
                    continue;
                }

                $passRows[] = [
                    'label' => $item->title,
                    'price' => (string)($item->content ?? ''),
                    'highlight' => (string)($item->url ?? '') === 'highlight',
                ];
            }
        }

        return [
            'schedule_title' => $schedule !== null ? $schedule->title : '',
            'banner_badge' => $banner !== null ? (string)$banner->subTitle : '',
            'banner_title' => $banner !== null ? $banner->title : '',
            'banner_description' => $banner !== null ? (string)$banner->description : '',
            'artists_title' => $artists !== null ? $artists->title : '',
            'artists' => $artistRows,
            'important_information_title' => $info !== null ? $info->title : '',
            'important_information_html' => $info !== null ? (string)$info->description : '',
            'passes_title' => $passes !== null ? $passes->title : '',
            'passes' => $passRows,
            'capacity_title' => $capacity !== null ? $capacity->title : '',
            'capacity_html' => $capacity !== null ? (string)$capacity->description : '',
            'special_title' => $special !== null ? $special->title : '',
            'special_html' => $special !== null ? (string)$special->description : '',
        ];
    }
}
