<?php

namespace App\Models\Dance;

use App\Models\Event\EventDetailPageModel;
use App\Models\Page\Page;

class DanceDetailData
{
    public Page $contentPage;
    public EventDetailPageModel $detailMeta;
    public array $scheduleSessions;

    public function __construct(Page $contentPage, EventDetailPageModel $detailMeta, array $scheduleSessions)
    {
        $this->contentPage = $contentPage;
        $this->detailMeta = $detailMeta;
        $this->scheduleSessions = $scheduleSessions;
    }
}
