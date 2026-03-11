<?php

namespace App\Validator\Cms;

class CmsScheduleValidator
{
    public function validateSessionRowsNotEmpty(array $sessionRows): void
    {
        if (count($sessionRows) === 0) {
            throw new \InvalidArgumentException('No schedule rows were provided.');
        }
    }

    public function validateVenueRow(int $id, string $name): void
    {
        if ($id <= 0 || $name === '') {
            throw new \InvalidArgumentException('Each venue row requires id and venue name.');
        }
    }

    public function validatePerformerRow(int $id, string $name, string $slug, array $seenSlugs): void
    {
        if ($id <= 0 || $name === '') {
            throw new \InvalidArgumentException('Each performer row requires id and name.');
        }

        if ($slug === '') {
            throw new \InvalidArgumentException('Each performer name must contain letters or numbers.');
        }

        if (isset($seenSlugs[$slug])) {
            throw new \InvalidArgumentException('Performer names must produce unique slugs for detail pages.');
        }
    }

    public function validateSessionRow(
        int $id,
        int $venueId,
        string $date,
        string $startTime,
        string $priceRaw,
        int $spots,
        int $amountSold,
        array $allowedVenueIds,
        array $allowedSessionIds
    ): void {
        if ($id <= 0 || $venueId <= 0 || $date === '' || $startTime === '' || $priceRaw === '') {
            throw new \InvalidArgumentException('All schedule rows must include id, venue, date, time, and price.');
        }

        if (!in_array($id, $allowedSessionIds, true)) {
            throw new \InvalidArgumentException('One or more session ids are invalid for this event.');
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw new \InvalidArgumentException('Date must be in YYYY-MM-DD format.');
        }

        if (!preg_match('/^\d{2}:\d{2}$/', $startTime)) {
            throw new \InvalidArgumentException('Start time must be in HH:MM format.');
        }

        if (!in_array($venueId, $allowedVenueIds, true)) {
            throw new \InvalidArgumentException('Selected venue is not valid for this event.');
        }

        if (!is_numeric($priceRaw)) {
            throw new \InvalidArgumentException('Price must be numeric.');
        }

        if ((float)$priceRaw < 0) {
            throw new \InvalidArgumentException('Price cannot be negative.');
        }

        if ($spots < $amountSold) {
            throw new \InvalidArgumentException('Available spots cannot be lower than amount sold.');
        }
    }

    public function validatePerformerIdAllowed(int $performerId, array $allowedPerformerIds): void
    {
        if (!in_array($performerId, $allowedPerformerIds, true)) {
            throw new \InvalidArgumentException('One or more selected performers are invalid for this event.');
        }
    }
}
