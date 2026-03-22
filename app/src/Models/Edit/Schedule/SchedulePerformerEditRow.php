<?php

namespace App\Models\Edit\Schedule;

class SchedulePerformerEditRow
{
    private int $id;
    private string $name;
    private string $type;
    private string $description;

    public function __construct(int $id, string $name, string $type, string $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->description = $description;
    }

    public static function fromArray(array $input): self
    {
        return new self(
            (int)($input['id'] ?? 0),
            trim((string)($input['name'] ?? '')),
            trim((string)($input['type'] ?? '')),
            trim((string)($input['description'] ?? ''))
        );
    }

    public function id(): int { return $this->id; }
    public function name(): string { return $this->name; }
    public function type(): string { return $this->type; }
    public function description(): string { return $this->description; }
}
