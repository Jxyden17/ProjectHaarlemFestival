<?php

namespace App\Models\Requests\Cms\Schedule;

class ScheduleSessionRowRequest
{
    private int $id;
    private string $date;
    private string $startTime;
    private int $venueId;
    private string $label;
    private string $price;
    private int $availableSpots;
    private int $amountSold;
    private array $performerIds;
    private int $languageId;

    public function __construct(
        int $id,
        string $date,
        string $startTime,
        int $venueId,
        string $label,
        string $price,
        int $availableSpots,
        int $amountSold,
        array $performerIds,
        int $languageId
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
        $this->languageId = $languageId;
    }

    public static function fromArray(array $input): self
    {
        $performerIdsRaw = is_array($input['performer_ids'] ?? null) ? $input['performer_ids'] : [];
        $performerIds = array_values(array_map('intval', $performerIdsRaw));

        return new self(
            (int)($input['id'] ?? 0),
            trim((string)($input['date'] ?? '')),
            trim((string)($input['start_time'] ?? '')),
            (int)($input['venue_id'] ?? 0),
            trim((string)($input['label'] ?? '')),
            trim((string)($input['price'] ?? '')),
            (int)($input['available_spots'] ?? 0),
            (int)($input['amount_sold'] ?? 0),
            $performerIds,
            (int)($input['language_id'] ?? 1)
        );
    }

    public function id(): int { return $this->id; }
    public function date(): string { return $this->date; }
    public function startTime(): string { return $this->startTime; }
    public function venueId(): int { return $this->venueId; }
    public function label(): string { return $this->label; }
    public function price(): string { return $this->price; }
    public function availableSpots(): int { return $this->availableSpots; }
    public function amountSold(): int { return $this->amountSold; }
    public function performerIds(): array { return $this->performerIds; }
    public function languageId(): int { return $this->languageId; }
}
