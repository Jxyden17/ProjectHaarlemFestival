<?php

namespace App\Models\Event;

class EventDetailPageModel
{
    public int $id;
    public int $eventId;
    public ?int $performerId;
    public int $pageId;
    public string $pageSlug;
    public string $entityType;
    public int $displayOrder;
    public ?string $performerName;

    public function __construct(
        int $id,
        int $eventId,
        ?int $performerId,
        int $pageId,
        string $pageSlug,
        string $entityType,
        int $displayOrder,
        ?string $performerName = null
    ) {
        $this->id = $id;
        $this->eventId = $eventId;
        $this->performerId = $performerId;
        $this->pageId = $pageId;
        $this->pageSlug = $pageSlug;
        $this->entityType = $entityType;
        $this->displayOrder = $displayOrder;
        $this->performerName = $performerName;
    }

    public function getPublicPath(): string
    {
        return '/dance/' . rawurlencode($this->pageSlug);
    }
}
