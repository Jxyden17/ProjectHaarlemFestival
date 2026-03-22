<?php

namespace App\Models\Schedule;

class ScheduleData
{
    public string $title;
    public string $eventName;
    public array $sessions;
    public bool $includeEventFilters;

    public function __construct(string $title, string $eventName, array $sessions, bool $includeEventFilters = false)
    {
        $this->title = $title;
        $this->eventName = $eventName;
        $this->sessions = $sessions;
        $this->includeEventFilters = $includeEventFilters;
    }
}
