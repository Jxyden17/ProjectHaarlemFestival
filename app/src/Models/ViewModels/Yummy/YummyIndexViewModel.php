<?php

namespace App\Models\ViewModels\Yummy;

class YummyIndexViewModel
{
    public array $venues;
    public ?object $hero;
    public ?object $map;
    public ?object $restaurants;

    public function __construct(array $venues, $hero, $map, $restaurants)
    {
        $this->venues = $venues;
        $this->hero = $hero;
        $this->map = $map;
        $this->restaurants = $restaurants;
    }
}