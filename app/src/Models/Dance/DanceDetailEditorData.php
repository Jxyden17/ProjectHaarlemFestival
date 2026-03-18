<?php

namespace App\Models\Dance;

use App\Models\Event\EventDetailPageModel;
use App\Models\Page\Page;

class DanceDetailEditorData
{
    public EventDetailPageModel $detailMeta;
    public Page $contentPage;
    public string $editorTitle;
    public string $performerName;

    public function __construct(EventDetailPageModel $detailMeta, Page $contentPage, string $editorTitle, string $performerName)
    {
        $this->detailMeta = $detailMeta;
        $this->contentPage = $contentPage;
        $this->editorTitle = $editorTitle;
        $this->performerName = $performerName;
    }
}
