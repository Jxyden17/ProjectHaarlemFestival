<?php

namespace App\Mapper;

use App\Models\Event\EventDetailPageModel;
use App\Models\Event\EventModel;
use App\Models\Event\PerformerModel;
use App\Models\Event\VenueModel;

class DanceMapper
{
    public function mapEventRow(array $row): EventModel
    {
        return new EventModel(
            (int)$row['id'],
            (string)$row['name'],
            isset($row['description']) ? (string)$row['description'] : null
        );
    }

    public function mapVenueRow(array $row): VenueModel
    {
        return new VenueModel(
            (int)$row['id'],
            (int)$row['event_id'],
            (string)$row['venue_name'],
            isset($row['address']) ? (string)$row['address'] : null,
            isset($row['venue_type']) ? (string)$row['venue_type'] : null,
            isset($row['created_at']) ? (string)$row['created_at'] : null
        );
    }

    public function mapPerformerRow(array $row): PerformerModel
    {
        return new PerformerModel(
            (int)$row['id'],
            (int)$row['event_id'],
            (string)$row['performer_name'],
            isset($row['performer_type']) ? (string)$row['performer_type'] : null,
            isset($row['description']) ? (string)$row['description'] : null,
            isset($row['created_at']) ? (string)$row['created_at'] : null
        );
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
