<?php

namespace App\Models;

class VenueModel
{
    public int $id;
    public int $eventId;
    public string $venueName;
    public ?string $address;
    public ?string $venueType;
    public ?string $createdAt;
    public array $sessions;

    public function __construct(
        int $id,
        int $eventId,
        string $venueName,
        ?string $address,
        ?string $venueType,
        ?string $createdAt,
        array $sessions = []
    ) {
        $this->id = $id;
        $this->eventId = $eventId;
        $this->venueName = $venueName;
        $this->address = $address;
        $this->venueType = $venueType;
        $this->createdAt = $createdAt;
        $this->sessions = $sessions;
    }
}
