<?php

namespace App\Models\ViewModels\Shared;

class ScheduleRowViewModel
{
    public int $id;
    public string $date;
    public string $time;
    public string $event;
    public string $eventName;
    public string $location;
    public string $price;
    public string $bookUrl;
    public string $language;
    public string $ageLabel;
    public int $totalTickets;
    public int $availableTickets;
    public int $bookedTickets;


    public function __construct(int $id, string $date, string $time, string $event, string $location, string $price, string $bookUrl, string $language, int $totalTickets, int $bookedTickets, string $eventName, string $ageLabel) {
        $this->id = $id;
        $this->date = $date; 
        $this->time = $time;
        $this->event = $event;
        $this->eventName = $eventName;
        $this->location = $location;
        $this->price = $price;
        $this->language = $language;
        $this->ageLabel = $ageLabel;
        $this->totalTickets = $totalTickets;
        $this->bookedTickets = $bookedTickets;
        $this->availableTickets = $totalTickets - $bookedTickets;
        $this->bookUrl = $bookUrl;
    }
}
