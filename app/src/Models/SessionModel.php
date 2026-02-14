<?php

namespace App\Models;

class SessionModel
{
    public int $id;
    public ?int $eventId;
    public int $venueId;
    public string $date;
    public string $startTime;
    public ?string $language;
    public float $price;
    public int $availableSpots;
    public int $amountSold;
    public ?EventModel $event;
    public ?VenueModel $venue;
    public array $sessionPerformers;

    public function __construct(
        int $id,
        ?int $eventId,
        int $venueId,
        string $date,
        string $startTime,
        ?string $language,
        float $price,
        int $availableSpots,
        int $amountSold,
        ?EventModel $event = null,
        ?VenueModel $venue = null,
        array $sessionPerformers = []
    ) {
        $this->id = $id;
        $this->eventId = $eventId;
        $this->venueId = $venueId;
        $this->date = $date;
        $this->startTime = $startTime;
        $this->language = $language;
        $this->price = $price;
        $this->availableSpots = $availableSpots;
        $this->amountSold = $amountSold;
        $this->event = $event;
        $this->venue = $venue;
        $this->sessionPerformers = $sessionPerformers;
    }
}
