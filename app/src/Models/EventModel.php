<?php

namespace App\Models;

class EventModel
{
    public int $id;
    public string $name;
    public ?string $description;
    public array $sessions;

    public function __construct(int $id, string $name, ?string $description, array $sessions = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->sessions = $sessions;
    }
}