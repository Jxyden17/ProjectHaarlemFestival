<?php

namespace App\Models\ViewModels\Cms\Schedule;

class ScheduleEditorViewModel
{
    public string $eventName;
    public array $venues;
    public array $performers;
    public array $sessions;

    public function __construct(string $eventName, array $venues, array $performers, array $sessions)
    {
        $this->eventName = $eventName;
        $this->venues = $venues;
        $this->performers = $performers;
        $this->sessions = $sessions;
    }
}
