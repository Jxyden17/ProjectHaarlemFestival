<?php

namespace App\Models\ViewModels;

class ScheduleViewModel
{
    public string $title;
    public array $dayFilters;
    public array $groups;

    public function __construct(
        string $title,
        array $dayFilters,
        array $groups
    ) {
        $this->title = $title;
        $this->dayFilters = $dayFilters;
        $this->groups = $groups;
    }
}
