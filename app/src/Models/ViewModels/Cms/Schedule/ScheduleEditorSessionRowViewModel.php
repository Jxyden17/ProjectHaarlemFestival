<?php

namespace App\Models\ViewModels\Cms\Schedule;

class ScheduleEditorSessionRowViewModel
{
    public int $id;
    public string $date;
    public string $startTime;
    public int $venueId;
    public string $label;
    public string $price;
    public int $availableSpots;
    public int $amountSold;
    public array $performerIds;

    public function __construct(
        int $id,
        string $date,
        string $startTime,
        int $venueId,
        string $label,
        string $price,
        int $availableSpots,
        int $amountSold,
        array $performerIds
    ) {
        $this->id = $id;
        $this->date = $date;
        $this->startTime = $startTime;
        $this->venueId = $venueId;
        $this->label = $label;
        $this->price = $price;
        $this->availableSpots = $availableSpots;
        $this->amountSold = $amountSold;
        $this->performerIds = $performerIds;
    }
}
