<?php

namespace App\Models\Dance;

use App\Models\Event\EventDetailPageModel;
use App\Models\Page\Page;

class DanceDetailData
{
    public Page $contentPage;
    public EventDetailPageModel $detailMeta;
    public array $scheduleRows;

    public function __construct(Page $contentPage, EventDetailPageModel $detailMeta, array $scheduleRows)
    {
        $this->contentPage = $contentPage;
        $this->detailMeta = $detailMeta;
        $this->scheduleRows = $scheduleRows;
    }
}
