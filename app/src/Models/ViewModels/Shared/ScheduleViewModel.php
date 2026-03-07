<?php

namespace App\Models\ViewModels\Shared;

class ScheduleViewModel
{
    public string $title;
    public string $eventName;
    public array $dayFilters;
    public array $eventFilters;
    public array $groups;
    public array $languageFilters;
    public bool $hasFilters;
    public bool $hasGroups;

    public function __construct(
        string $title,
        string $eventName,
        array $dayFilters,
        array $eventFilters,
        array $groups,
        array $languageFilters = []
    ) {
        $this->title = $title;
        $this->eventName = $eventName;
        $this->dayFilters = $dayFilters;
        $this->eventFilters = $eventFilters;
        $this->groups = $groups;
        $this->languageFilters = $languageFilters;
        $this->hasFilters = !empty($dayFilters);
        $this->hasGroups = !empty($groups);
    }
}