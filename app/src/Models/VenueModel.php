<?php

namespace App\Models;

class VenueModel
{
    public int $id;
    public int $event_id;
    public string $venue_name;
    public ?string $address;
    public ?string $venue_type;
    public ?string $created_at;

    public function __construct(
        int $id,
        int $event_id,
        string $venue_name,
        ?string $address = null,
        ?string $venue_type = null,
        ?string $created_at = null
    ) {
        $this->id = $id;
        $this->event_id = $event_id;
        $this->venue_name = $venue_name;
        $this->address = $address;
        $this->venue_type = $venue_type;
        $this->created_at = $created_at;
    }
}