<?php

namespace App\Service\Interfaces;

interface ICmsEventEditorService
{
    public function getEditorData(string $eventName): array;

    public function mergePostedEditorData(
        string $eventName,
        array $editorData,
        array $postedVenues,
        array $postedPerformers,
        array $postedSessions
    ): array;
}
