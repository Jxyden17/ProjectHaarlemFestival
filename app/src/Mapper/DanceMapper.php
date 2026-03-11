<?php

namespace App\Mapper;

use App\Models\Event\EventDetailPageModel;

class DanceMapper
{
    public function mapDetailPageRow(array $row): EventDetailPageModel
    {
        return new EventDetailPageModel(
            (int)$row['id'],
            (int)$row['event_id'],
            isset($row['performer_id']) ? (int)$row['performer_id'] : null,
            (int)$row['page_id'],
            (string)($row['page_slug'] ?? ''),
            (string)($row['detail_slug'] ?? ''),
            (string)($row['entity_type'] ?? 'performer'),
            (int)($row['display_order'] ?? 0),
            isset($row['performer_name']) ? (string)$row['performer_name'] : null
        );
    }
}
