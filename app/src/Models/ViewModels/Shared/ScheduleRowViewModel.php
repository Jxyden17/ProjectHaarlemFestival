<?php

namespace App\Models\ViewModels\Shared;

class ScheduleRowViewModel
{
    public string $date;
    public string $time;
    public string $event;
    public string $location;
    public string $price;
    public string $bookUrl;

    public function __construct(string $date, string $time, string $event, string $location, string $price, string $bookUrl) {
        $this->date = $date;
        $this->time = $time;
        $this->event = $event;
        $this->location = $location;
        $this->price = $price;
        $this->bookUrl = $bookUrl;
    }
}
