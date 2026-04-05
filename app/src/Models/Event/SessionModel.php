<?php

namespace App\Models\Event;

use App\Models\Enums\Language;

class SessionModel
{
    public int $id;
    public ?int $eventId;
    public int $venueId;
    public string $date;
    public string $startTime;
    public ?Language $language;
    public ?string $label;
    public float $price;
    public int $availableSpots;
    public int $amountSold;
    public bool $isPass;
    public ?EventModel $event;
    public ?VenueModel $venue;
    public array $sessionPerformers;

    public function __construct(
        int $id,
        ?int $eventId,
        int $venueId,
        string $date,
        string $startTime,
        ?Language $language,
        ?string $label,
        float $price,
        int $availableSpots,
        int $amountSold,
        bool $isPass = false,
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
        $this->label = $label;
        $this->price = $price;
        $this->availableSpots = $availableSpots;
        $this->amountSold = $amountSold;
        $this->isPass = $isPass;
        $this->event = $event;
        $this->venue = $venue;
        $this->sessionPerformers = $sessionPerformers;
    }
}
