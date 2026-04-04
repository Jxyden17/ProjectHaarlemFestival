<?php

namespace App\Service\Cms\Interfaces;

use App\Models\ViewModels\Cms\Schedule\ScheduleEditorViewModel;

interface ICmsEventEditorService
{
    // Returns the combined CMS editor payload for one event so the schedule screen can preload schedule and event-specific extras.
    public function getEditorData(string $eventName): ScheduleEditorViewModel;

    // Merges posted schedule rows back into the editor payload so validation errors can re-render submitted values.
    public function mergePostedEditorData(
        string $eventName,
        ScheduleEditorViewModel $editorData,
        array $postedVenues,
        array $postedPerformers,
        array $postedSessions
    ): ScheduleEditorViewModel;

    // Saves linked page content for the event editor so schedule-adjacent page edits can reuse the shared page save flow.
    public function savePageContent(int $pageId, array $sections, array $items): void;
    public function getTourDetailPages(): array;
}
