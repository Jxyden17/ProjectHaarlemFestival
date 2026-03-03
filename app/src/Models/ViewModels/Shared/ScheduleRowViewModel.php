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

    // Tour fields
    public string $language;
    public int $totalTickets;
    public int $availableTickets;
    public int $bookedTickets;


    public function __construct(string $date, string $time, string $event, string $location, string $price, string $bookUrl, string $language = 'Unknown', int $totalTickets = 0, ?int $availableTickets = null, int $bookedTickets = 0) {
        $this->date = $date;
        $this->time = $time;
        $this->event = $event;
        $this->location = $location;
        $this->price = $price;
        $this->language = $language;
        $this->totalTickets = max(0, $totalTickets);
        $this->bookedTickets = max(0, $bookedTickets);

        if ($availableTickets === null) {
            $this->availableTickets = max(0, $this->totalTickets - $this->bookedTickets);
        } else {
            $this->availableTickets = max(0, $availableTickets);
        }

        $this->bookUrl = $bookUrl;
    }
}
