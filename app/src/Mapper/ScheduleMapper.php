<?php

namespace App\Mapper;

use App\Models\Enums\Language;
use App\Models\Event\EventModel;
use App\Models\Event\PerformerModel;
use App\Models\Event\SessionModel;
use App\Models\Event\SessionPerformerModel;
use App\Models\Event\VenueModel;

class ScheduleMapper
{
    private EventMapper $eventMapper;

    public function __construct(EventMapper $eventMapper)
    {
        $this->eventMapper = $eventMapper;
    }

    public function mapEventRow(array $row): EventModel
    {
        return $this->eventMapper->mapEventRow($row);
    }

    public function mapSessionRow(array $row): SessionModel
    {
        $languageId = isset($row['language_id']) ? (int) $row['language_id'] : null;

        return new SessionModel(
            (int) $row['id'],
            isset($row['event_id']) ? (int) $row['event_id'] : null,
            (int) $row['venue_id'],
            (string) $row['date'],
            (string) $row['start_time'],
            $languageId !== null ? Language::tryFrom($languageId) : null,
            isset($row['label']) ? (string) $row['label'] : null,
            (float) $row['price'],
            (int) $row['available_spots'],
            (int) $row['amount_sold']
        );
    }

    public function mapVenueModelRow(array $row): VenueModel
    {
        return $this->eventMapper->mapVenueRow($row);
    }

    public function mapPerformerModelRow(array $row): PerformerModel
    {
        return $this->eventMapper->mapPerformerRow($row);
    }

    public function mapSessionPerformerRow(array $row): SessionPerformerModel
    {
        return $this->eventMapper->mapSessionPerformerRow($row);
    }

    public function mapEventRowsToEvent(array $rows, string $eventName): EventModel
    {
        if ($rows === []) {
            throw new \RuntimeException($eventName . ' event not found.');
        }

        $event = $this->eventMapper->mapEventRowFromRows($rows[0]);
        $sessionById = [];
        $venueById = [];
        $performerById = [];
        $seenSessionPerformer = [];

        foreach ($rows as $row) {
            $sessionId = (int) ($row['session_id'] ?? 0);
            if ($sessionId > 0 && !isset($sessionById[$sessionId])) {
                $venueId = (int) ($row['venue_id_ref'] ?? 0);
                $venue = null;

                if ($venueId > 0) {
                    if (!isset($venueById[$venueId])) {
                        $venueById[$venueId] = $this->eventMapper->mapVenueRow([
                            'id' => $venueId,
                            'event_id' => $event->id,
                            'venue_name' => (string) ($row['venue_name'] ?? ''),
                            'address' => isset($row['address']) ? (string) $row['address'] : null,
                            'venue_type' => isset($row['venue_type']) ? (string) $row['venue_type'] : null,
                            'created_at' => isset($row['venue_created_at']) ? (string) $row['venue_created_at'] : null,
                        ]);
                    }

                    $venue = $venueById[$venueId];
                }

                $languageId = isset($row['language_id']) ? (int) $row['language_id'] : null;
                $session = new SessionModel(
                    $sessionId,
                    $event->id,
                    (int) ($row['venue_id'] ?? 0),
                    (string) ($row['date'] ?? ''),
                    (string) ($row['start_time'] ?? ''),
                    $languageId !== null ? Language::tryFrom($languageId) : null,
                    isset($row['label']) ? (string) $row['label'] : null,
                    (float) ($row['price'] ?? 0),
                    (int) ($row['available_spots'] ?? 0),
                    (int) ($row['amount_sold'] ?? 0),
                    $event,
                    $venue
                );

                $sessionById[$sessionId] = $session;
                $event->sessions[] = $session;

                if ($venue !== null) {
                    $venue->sessions[] = $session;
                }
            }

            $spSessionId = (int) ($row['sp_session_id'] ?? 0);
            $spPerformerId = (int) ($row['sp_performer_id'] ?? 0);
            if ($spSessionId <= 0 || $spPerformerId <= 0 || !isset($sessionById[$spSessionId])) {
                continue;
            }

            if (!isset($performerById[$spPerformerId])) {
                $performerById[$spPerformerId] = $this->eventMapper->mapPerformerRow([
                    'id' => $spPerformerId,
                    'event_id' => $event->id,
                    'performer_name' => (string) ($row['performer_name'] ?? ''),
                    'performer_type' => isset($row['performer_type']) ? (string) $row['performer_type'] : null,
                    'description' => isset($row['performer_description']) ? (string) $row['performer_description'] : null,
                    'created_at' => isset($row['performer_created_at']) ? (string) $row['performer_created_at'] : null,
                ]);
            }

            $key = $spSessionId . '-' . $spPerformerId;
            if (isset($seenSessionPerformer[$key])) {
                continue;
            }

            $seenSessionPerformer[$key] = true;
            $sessionPerformer = new SessionPerformerModel(
                $spSessionId,
                $spPerformerId,
                $sessionById[$spSessionId],
                $performerById[$spPerformerId]
            );

            $sessionById[$spSessionId]->sessionPerformers[] = $sessionPerformer;
            $performerById[$spPerformerId]->sessionPerformers[] = $sessionPerformer;
        }

        return $event;
    }

    public function mapEventRowsFromCollections(
        EventModel $event,
        array $venues,
        array $sessions,
        array $performers,
        array $sessionPerformers
    ): EventModel {
        $venueById = [];
        foreach ($venues as $venue) {
            if (!$venue instanceof VenueModel) {
                continue;
            }

            $venue->sessions = [];
            $venueById[$venue->id] = $venue;
        }

        $performerById = [];
        foreach ($performers as $performer) {
            if (!$performer instanceof PerformerModel) {
                continue;
            }

            $performer->sessionPerformers = [];
            $performerById[$performer->id] = $performer;
        }

        $sessionById = [];
        $event->sessions = [];
        foreach ($sessions as $session) {
            if (!$session instanceof SessionModel) {
                continue;
            }

            $session->event = $event;
            $session->venue = $venueById[$session->venueId] ?? null;
            $session->sessionPerformers = [];
            $sessionById[$session->id] = $session;
            $event->sessions[] = $session;

            if ($session->venue !== null) {
                $session->venue->sessions[] = $session;
            }
        }

        foreach ($sessionPerformers as $sessionPerformer) {
            if (!$sessionPerformer instanceof SessionPerformerModel) {
                continue;
            }

            $session = $sessionById[$sessionPerformer->sessionId] ?? null;
            $performer = $performerById[$sessionPerformer->performerId] ?? null;
            $sessionPerformer->session = $session;
            $sessionPerformer->performer = $performer;

            if ($session !== null) {
                $session->sessionPerformers[] = $sessionPerformer;
            }

            if ($performer !== null) {
                $performer->sessionPerformers[] = $sessionPerformer;
            }
        }

        return $event;
    }
}
