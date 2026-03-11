<?php

namespace App\Models\ViewModels\Cms\Schedule;

class ScheduleEditorVenueRowViewModel
{
    public int $id;
    public string $name;
    public string $address;
    public string $type;

    public function __construct(int $id, string $name, string $address, string $type)
    {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->type = $type;
    }
}
