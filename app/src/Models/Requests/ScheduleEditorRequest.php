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

    // Stores the posted schedule editor rows so controllers can convert one request into typed edit inputs.
    private function __construct(array $venues, array $performers, array $sessions)
    {
        $this->venues = $venues;
        $this->performers = $performers;
        $this->sessions = $sessions;
    }

    // Builds a typed schedule editor request from raw POST data so CMS schedule actions can validate and save structured rows.
    public static function fromArray(array $input): self
    {
        return new self(
            self::mapVenues(is_array($input['venues'] ?? null) ? $input['venues'] : []),
            self::mapPerformers(is_array($input['performers'] ?? null) ? $input['performers'] : []),
            self::mapSessions(is_array($input['sessions'] ?? null) ? $input['sessions'] : [])
        );
    }

    // Maps posted venue arrays into typed venue edit rows so later code works with validated accessors instead of raw arrays.
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

    // Maps posted performer arrays into typed performer edit rows so later code works with validated accessors instead of raw arrays.
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

    // Maps posted session arrays into typed session edit rows so later code works with validated accessors instead of raw arrays.
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

    // Returns posted venue rows so CMS schedule code can read the typed venue input collection.
    public function venues(): array
    {
        return $this->venues;
    }

    // Returns posted performer rows so CMS schedule code can read the typed performer input collection.
    public function performers(): array
    {
        return $this->performers;
    }

    // Returns posted session rows so CMS schedule code can read the typed session input collection.
    public function sessions(): array
    {
        return $this->sessions;
    }

    // Builds the consolidated save input so CMS schedule services can persist one typed payload instead of separate arrays.
    public function toSaveInput(): ScheduleSaveInput
    {
        return new ScheduleSaveInput($this->venues(), $this->performers(), $this->sessions());
    }
}
