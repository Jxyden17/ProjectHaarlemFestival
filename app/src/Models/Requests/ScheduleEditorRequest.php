<?php

namespace App\Models\Requests;

use App\Models\Edit\Schedule\SchedulePerformerEditRow;
use App\Models\Edit\Schedule\ScheduleSaveInput;
use App\Models\Edit\Schedule\ScheduleSessionEditRow;
use App\Models\Edit\Schedule\ScheduleVenueEditRow;

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
            self::mapVenues(is_array($input['venues'] ?? null) ? $input['venues'] : []),
            self::mapPerformers(is_array($input['performers'] ?? null) ? $input['performers'] : []),
            self::mapSessions(is_array($input['sessions'] ?? null) ? $input['sessions'] : [])
        );
    }

    private static function mapVenues(array $input): array
    {
        $rows = [];
        foreach ($input as $row) {
            if (!is_array($row)) {
                continue;
            }

            $rows[] = ScheduleVenueEditRow::fromArray($row);
        }

        return $rows;
    }

    private static function mapPerformers(array $input): array
    {
        $rows = [];
        foreach ($input as $row) {
            if (!is_array($row)) {
                continue;
            }

            $rows[] = SchedulePerformerEditRow::fromArray($row);
        }

        return $rows;
    }

    private static function mapSessions(array $input): array
    {
        $rows = [];
        foreach ($input as $row) {
            if (!is_array($row)) {
                continue;
            }

            $rows[] = ScheduleSessionEditRow::fromArray($row);
        }

        return $rows;
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

    public function toSaveInput(): ScheduleSaveInput
    {
        return new ScheduleSaveInput($this->venues(), $this->performers(), $this->sessions());
    }
}
