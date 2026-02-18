<?php

namespace App\Models\ViewModels\Shared;

class ScheduleDayFilterViewModel
{
    public string $key;
    public string $label;
    public string $countLabel;
    public bool $isActive;

    public function __construct(string $key, string $label, string $countLabel, bool $isActive)
    {
        $this->key = $key;
        $this->label = $label;
        $this->countLabel = $countLabel;
        $this->isActive = $isActive;
    }
}
