<?php

namespace App\Mapper;

use App\Models\Event\EventModel;
use App\Models\Event\PerformerModel;
use App\Models\Event\SessionPerformerModel;
use App\Models\Event\VenueModel;

class EventMapper
{
    public function mapEventRow(array $row): EventModel
    {
        return new EventModel(
            (int)$row['id'],
            (string)$row['name'],
            isset($row['description']) ? (string)$row['description'] : null
        );
    }

    public function mapEventRowFromRows(array $row): EventModel
    {
        return new EventModel(
            (int)$row['event_id'],
            (string)$row['event_name'],
            isset($row['event_description']) ? (string)$row['event_description'] : null
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

    public function mapSessionPerformerRow(array $row): SessionPerformerModel
    {
        return new SessionPerformerModel(
            (int)$row['session_id'],
            (int)$row['performer_id']
        );
    }
}
