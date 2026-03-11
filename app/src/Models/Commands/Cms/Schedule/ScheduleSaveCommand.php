<?php

namespace App\Models\Commands\Cms\Schedule;

class ScheduleSaveCommand
{
    private array $venues;
    private array $performers;
    private array $sessions;

    public function __construct(array $venues, array $performers, array $sessions)
    {
        $this->venues = $venues;
        $this->performers = $performers;
        $this->sessions = $sessions;
    }

    public function venues(): array { return $this->venues; }
    public function performers(): array { return $this->performers; }
    public function sessions(): array { return $this->sessions; }
}
