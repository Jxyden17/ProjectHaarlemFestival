<?php

namespace App\Models\ViewModels\Cms\Schedule;

class ScheduleEditorPerformerRowViewModel
{
    public int $id;
    public string $name;
    public string $type;
    public string $description;
    public int $artistSectionItemId;
    public string $artistImagePath;

    public function __construct(int $id, string $name, string $type, string $description, int $artistSectionItemId, string $artistImagePath)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->description = $description;
        $this->artistSectionItemId = $artistSectionItemId;
        $this->artistImagePath = $artistImagePath;
    }
}
