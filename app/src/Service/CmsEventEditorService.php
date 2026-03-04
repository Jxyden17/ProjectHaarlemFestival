<?php

namespace App\Service;

use App\Models\Page\SectionItem;
use App\Service\Interfaces\ICmsEventEditorService;
use App\Service\Interfaces\IDanceService;
use App\Service\Interfaces\IScheduleService;

class CmsEventEditorService implements ICmsEventEditorService
{
    private IScheduleService $scheduleService;
    private IDanceService $danceService;

    public function __construct(IScheduleService $scheduleService, IDanceService $danceService)
    {
        $this->scheduleService = $scheduleService;
        $this->danceService = $danceService;
    }

    public function getEditorData(string $eventName): array
    {
        $editorData = $this->scheduleService->getScheduleEditorData($eventName);

        if (strtolower($eventName) !== 'dance') {
            return $editorData;
        }

        $danceHome = $this->danceService->getDanceHomePage();
        $artistsSection = $danceHome->getSection('dance_artists');
        $artistImageRows = [];

        if ($artistsSection !== null) {
            foreach ($artistsSection->getItemsByCategorie('artist') as $item) {
                if ($item instanceof SectionItem) {
                    $artistImageRows[] = $item;
                }
            }
        }

        $performers = is_array($editorData['performers'] ?? null) ? $editorData['performers'] : [];
        foreach ($performers as $index => $performer) {
            if (!is_array($performer)) {
                continue;
            }

            $imageRow = $artistImageRows[$index] ?? null;
            $performer['artist_section_item_id'] = $imageRow instanceof SectionItem ? $imageRow->id : 0;
            $performer['artist_image_path'] = $imageRow instanceof SectionItem ? (string)($imageRow->image ?? '') : '';
            $performers[$index] = $performer;
        }

        $editorData['performers'] = $performers;
        return $editorData;
    }

    public function mergePostedEditorData(
        string $eventName,
        array $editorData,
        array $postedVenues,
        array $postedPerformers,
        array $postedSessions
    ): array {
        if (!empty($postedVenues)) {
            $editorData['venues'] = $postedVenues;
        }

        if (!empty($postedPerformers)) {
            if (strtolower($eventName) === 'dance') {
                $existingPerformers = is_array($editorData['performers'] ?? null) ? $editorData['performers'] : [];
                foreach ($postedPerformers as $index => $postedPerformer) {
                    if (!is_array($postedPerformer)) {
                        continue;
                    }

                    $existing = is_array($existingPerformers[$index] ?? null) ? $existingPerformers[$index] : [];
                    $postedPerformer['artist_section_item_id'] = (int)($existing['artist_section_item_id'] ?? 0);
                    $postedPerformer['artist_image_path'] = (string)($existing['artist_image_path'] ?? '');
                    $postedPerformers[$index] = $postedPerformer;
                }
            }

            $editorData['performers'] = $postedPerformers;
        }

        if (!empty($postedSessions)) {
            $editorData['sessions'] = $postedSessions;
        }

        return $editorData;
    }
}
