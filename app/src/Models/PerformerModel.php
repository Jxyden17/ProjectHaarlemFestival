<?php

namespace App\Models;

class PerformerModel
{
    public int $id;
    public int $eventId;
    public string $performerName;
    public ?string $performerType;
    public ?string $description;
    public ?string $createdAt;
    public array $sessionPerformers;

    public function __construct(
        int $id,
        int $eventId,
        string $performerName,
        ?string $performerType,
        ?string $description,
        ?string $createdAt,
        array $sessionPerformers = []
    ) {
        $this->id = $id;
        $this->eventId = $eventId;
        $this->performerName = $performerName;
        $this->performerType = $performerType;
        $this->description = $description;
        $this->createdAt = $createdAt;
        $this->sessionPerformers = $sessionPerformers;
    }
}
