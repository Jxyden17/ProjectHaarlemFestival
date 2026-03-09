<?php

namespace App\Models\Event;

class EventDetailPageModel
{
    public int $id;
    public int $eventId;
    public ?int $performerId;
    public int $pageId;
    public string $pageSlug;
    public string $publicSlug;
    public string $cmsSlug;
    public string $entityType;
    public bool $isPublished;
    public int $displayOrder;
    public ?string $performerName;

    public function __construct(
        int $id,
        int $eventId,
        ?int $performerId,
        int $pageId,
        string $pageSlug,
        string $publicSlug,
        string $cmsSlug,
        string $entityType,
        bool $isPublished,
        int $displayOrder,
        ?string $performerName = null
    ) {
        $this->id = $id;
        $this->eventId = $eventId;
        $this->performerId = $performerId;
        $this->pageId = $pageId;
        $this->pageSlug = $pageSlug;
        $this->publicSlug = $publicSlug;
        $this->cmsSlug = $cmsSlug;
        $this->entityType = $entityType;
        $this->isPublished = $isPublished;
        $this->displayOrder = $displayOrder;
        $this->performerName = $performerName;
    }

    public function getPublicPath(): string
    {
        return '/dance/' . rawurlencode($this->publicSlug);
    }
}
