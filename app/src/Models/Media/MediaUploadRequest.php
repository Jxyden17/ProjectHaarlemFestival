<?php

namespace App\Models\Media;

class MediaUploadRequest
{
    public MediaModuleConfig $moduleConfig;
    public string $currentPath;
    public string $tmpPath;
    public string $extension;
    public ?int $sectionItemId;

    public function __construct(
        MediaModuleConfig $moduleConfig,
        string $currentPath,
        string $tmpPath,
        string $extension,
        ?int $sectionItemId
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->currentPath = $currentPath;
        $this->tmpPath = $tmpPath;
        $this->extension = $extension;
        $this->sectionItemId = $sectionItemId;
    }
}
