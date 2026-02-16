<?php

namespace App\Models\ViewModels\Shared;

class ScheduleGroupViewModel
{
    public string $title;
    public string $dayKey;
    public string $subtitle;
    public array $rows;

    public function __construct(string $title, string $dayKey, string $subtitle, array $rows = [])
    {
        $this->title = $title;
        $this->dayKey = $dayKey;
        $this->subtitle = $subtitle;
        $this->rows = $rows;
    }
}
