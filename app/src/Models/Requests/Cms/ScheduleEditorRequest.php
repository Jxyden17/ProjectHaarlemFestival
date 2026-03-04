<?php

namespace App\Models\Requests\Cms;

use App\Models\Commands\Cms\Schedule\ScheduleSaveCommand;
use App\Models\Requests\Cms\Schedule\SchedulePerformerRowRequest;
use App\Models\Requests\Cms\Schedule\ScheduleSessionRowRequest;
use App\Models\Requests\Cms\Schedule\ScheduleVenueRowRequest;

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

            $rows[] = ScheduleVenueRowRequest::fromArray($row);
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

            $rows[] = SchedulePerformerRowRequest::fromArray($row);
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

            $rows[] = ScheduleSessionRowRequest::fromArray($row);
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

    public function toSaveCommand(): ScheduleSaveCommand
    {
        return new ScheduleSaveCommand($this->venues(), $this->performers(), $this->sessions());
    }
}
