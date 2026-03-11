<?php

namespace App\Mapper;

use App\Models\Event\EventDetailPageModel;
use App\Models\Event\EventModel;
use App\Models\Event\PerformerModel;
use App\Models\Event\VenueModel;

class DanceMapper
{
    private EventMapper $eventMapper;

    public function __construct(?EventMapper $eventMapper = null)
    {
        $this->eventMapper = $eventMapper ?? new EventMapper();
    }

    public function mapEventRow(array $row): EventModel
    {
        return $this->eventMapper->mapEventRow($row);
    }

    public function mapVenueRow(array $row): VenueModel
    {
        return $this->eventMapper->mapVenueRow($row);
    }

    public function mapPerformerRow(array $row): PerformerModel
    {
        return $this->eventMapper->mapPerformerRow($row);
    }

    public function mapDetailPageRow(array $row): EventDetailPageModel
    {
        return new EventDetailPageModel(
            (int)$row['id'],
            (int)$row['event_id'],
            isset($row['performer_id']) ? (int)$row['performer_id'] : null,
            (int)$row['page_id'],
            (string)($row['page_slug'] ?? ''),
            (string)($row['public_slug'] ?? ''),
            (string)($row['cms_slug'] ?? ''),
            (string)($row['entity_type'] ?? 'performer'),
            (int)($row['display_order'] ?? 0),
            isset($row['performer_name']) ? (string)$row['performer_name'] : null
        );
    }
}
