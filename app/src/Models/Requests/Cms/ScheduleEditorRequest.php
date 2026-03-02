<?php

namespace App\Models\Requests\Cms;

class ScheduleEditorRequest
{
    private array $venues;
    private array $performers;
    private array $sessions;

    private function __construct(array $venues, array $performers, array $sessions)
    {
        $this->venues = $venues;
        $this->performers = $performers;
        $this->sessions = $sessions;
    }

    public static function fromArray(array $input): self
    {
        return new self(
            is_array($input['venues'] ?? null) ? $input['venues'] : [],
            is_array($input['performers'] ?? null) ? $input['performers'] : [],
            is_array($input['sessions'] ?? null) ? $input['sessions'] : []
        );
    }

    public function toArray(): array
    {
        return [
            'venues' => $this->venues,
            'performers' => $this->performers,
            'sessions' => $this->sessions,
        ];
    }

    public function venues(): array
    {
        return $this->venues;
    }

    public function performers(): array
    {
        return $this->performers;
    }

    public function sessions(): array
    {
        return $this->sessions;
    }
}
