<?php

namespace App\Models\Requests\Cms\Schedule;

class ScheduleVenueRowRequest
{
    private int $id;
    private string $name;
    private string $address;
    private string $type;

    public function __construct(int $id, string $name, string $address, string $type)
    {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->type = $type;
    }

    public static function fromArray(array $input): self
    {
        return new self(
            (int)($input['id'] ?? 0),
            trim((string)($input['name'] ?? '')),
            trim((string)($input['address'] ?? '')),
            trim((string)($input['type'] ?? ''))
        );
    }

    public function id(): int { return $this->id; }
    public function name(): string { return $this->name; }
    public function address(): string { return $this->address; }
    public function type(): string { return $this->type; }
}
