<?php

namespace App\Models\ViewModels\Shared;

class ScheduleViewModel
{
    public string $title;
    public array $dayFilters;
    public array $groups;
    public bool $hasFilters;
    public bool $hasGroups;
    // optional language filters (only for tour)
    public array $languageFilters;

    public function __construct(
        string $title,
        array $dayFilters,
        array $groups,
        array $languageFilters = []
    ) {
        $this->title = $title;
        $this->dayFilters = $dayFilters;
        $this->groups = $groups;
        $this->hasFilters = !empty($dayFilters);
        $this->hasGroups = !empty($groups);
        $this->languageFilters = $languageFilters;
    }
}
