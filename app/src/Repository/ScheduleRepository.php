<?php

namespace App\Repository;

use App\Mapper\ScheduleMapper;
use App\Models\Database;
use App\Models\Event\EventModel;
use App\Models\Event\SessionModel;
use App\Repository\Interfaces\IScheduleRepository;
use PDO;

class ScheduleRepository implements IScheduleRepository
{
    private PDO $db;
    private ScheduleMapper $scheduleMapper;

    // Stores the database handle and mapper so schedule SQL results can be converted into typed models.
    public function __construct(ScheduleMapper $scheduleMapper)
    {
        $this->db = Database::getInstance();
        $this->scheduleMapper = $scheduleMapper;
    }

    // Finds one event row by name so services can resolve an event before loading related schedule records.
    public function findEventByName(string $name): ?EventModel
    {
        $stmt = $this->db->prepare('SELECT id, name, description FROM events WHERE name = :name LIMIT 1');
        $stmt->execute([':name' => $name]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $this->scheduleMapper->mapEventRow($row);
    }

    // Returns the full joined row set for one event so the mapper can rebuild sessions, venues, and performers together.
    public function getScheduleRowsByEventName(string $name): array
    {
        $stmt = $this->db->prepare(
            'SELECT e.id AS event_id,
                    e.name AS event_name,
                    e.description AS event_description,
                    s.id AS session_id,
                    s.venue_id,
                    s.date,
                    s.start_time,
                    s.language_id,
                    s.label,
                    s.price,
                    s.available_spots,
                    s.amount_sold,
                    s.is_pass,
                    v.id AS venue_id_ref,
                    v.venue_name,
                    v.address,
                    v.venue_type,
                    v.created_at AS venue_created_at,
                    sp.session_id AS sp_session_id,
                    sp.performer_id AS sp_performer_id,
                    p.id AS performer_id_ref,
                    p.performer_name,
                    p.performer_type,
                    p.description AS performer_description,
                    p.created_at AS performer_created_at
             FROM events e
             LEFT JOIN sessions s ON s.event_id = e.id
             LEFT JOIN venues v ON v.id = s.venue_id
             LEFT JOIN session_performers sp ON sp.session_id = s.id
             LEFT JOIN performers p ON p.id = sp.performer_id
             WHERE e.name = :name
             ORDER BY s.date ASC, s.start_time ASC, s.id ASC, sp.performer_id ASC'
        );
        $stmt->execute([':name' => $name]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Returns the joined row set for one event and performer so detail pages can build performer-specific schedules.
    public function getScheduleRowsByEventNameAndPerformerId(string $name, int $performerId): array
    {
        if ($performerId <= 0) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT e.id AS event_id,
                    e.name AS event_name,
                    e.description AS event_description,
                    s.id AS session_id,
                    s.venue_id,
                    s.date,
                    s.start_time,
                    s.language_id,
                    s.label,
                    s.price,
                    s.available_spots,
                    s.amount_sold,
                    s.is_pass,
                    v.id AS venue_id_ref,
                    v.venue_name,
                    v.address,
                    v.venue_type,
                    v.created_at AS venue_created_at,
                    sp.session_id AS sp_session_id,
                    sp.performer_id AS sp_performer_id,
                    p.id AS performer_id_ref,
                    p.performer_name,
                    p.performer_type,
                    p.description AS performer_description,
                    p.created_at AS performer_created_at
             FROM events e
             INNER JOIN sessions s ON s.event_id = e.id
             LEFT JOIN venues v ON v.id = s.venue_id
             INNER JOIN session_performers sp ON sp.session_id = s.id
             LEFT JOIN performers p ON p.id = sp.performer_id
             WHERE e.name = :name
               AND sp.performer_id = :performer_id
             ORDER BY s.date ASC, s.start_time ASC, s.id ASC, sp.performer_id ASC'
        );
        $stmt->execute([
            ':name' => $name,
            ':performer_id' => $performerId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Returns all event rows so callers can build all-events schedule views.
    public function getAllEvents(): array
    {
        $stmt = $this->db->query('SELECT id, name, description FROM events ORDER BY id ASC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            fn(array $row): EventModel => $this->scheduleMapper->mapEventRow($row),
            $rows
        );
    }

    // Returns one mapped session row by id so the CMS schedule editor can preload a single session.
    public function getSessionById(int $id): ?SessionModel
    {
        if ($id <= 0) {
            return null;
        }

        $stmt = $this->db->prepare(
            'SELECT id, event_id, venue_id, date, start_time, language_id, label, price, available_spots, amount_sold, is_pass
             FROM sessions
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return $this->scheduleMapper->mapSessionRow($row);
    }

    // Returns mapped session rows for one event so services can attach standalone session collections to an event.
    public function getSessionsByEventId(int $eventId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, event_id, venue_id, date, start_time, language_id, label, price, available_spots, amount_sold, is_pass
             FROM sessions
             WHERE event_id = :event_id
             ORDER BY date ASC, start_time ASC'
        );
        $stmt->execute([':event_id' => $eventId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $row) => $this->scheduleMapper->mapSessionRow($row), $rows);
    }

    // Returns mapped venue rows for one event so services and CMS editors can work with allowed venues.
    public function getVenuesByEventId(int $eventId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, event_id, venue_name, address, venue_type, created_at
             FROM venues
             WHERE event_id = :event_id
             ORDER BY venue_name ASC'
        );
        $stmt->execute([':event_id' => $eventId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $row) => $this->scheduleMapper->mapVenueModelRow($row), $rows);
    }

    // Returns mapped performer rows for one event so services and CMS editors can work with allowed performers.
    public function getPerformersByEventId(int $eventId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, event_id, performer_name, performer_type, description, created_at
             FROM performers
             WHERE event_id = :event_id
             ORDER BY performer_name ASC'
        );
        $stmt->execute([':event_id' => $eventId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $row) => $this->scheduleMapper->mapPerformerModelRow($row), $rows);
    }

    // Returns mapped session-performer rows for one event so performer assignments can be reattached after loading.
    public function getSessionPerformersByEventId(int $eventId): array
    {
        $stmt = $this->db->prepare(
            'SELECT sp.session_id, sp.performer_id
             FROM session_performers sp
             INNER JOIN sessions s ON s.id = sp.session_id
             WHERE s.event_id = :event_id
             ORDER BY sp.session_id ASC, sp.performer_id ASC'
        );
        $stmt->execute([':event_id' => $eventId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $row) => $this->scheduleMapper->mapSessionPerformerRow($row), $rows);
    }

    // Saves venues, performers, sessions, and assignments for one event in one transaction so schedule edits stay consistent.
    public function saveEventScheduleData(
        int $eventId,
        array $venueRows,
        array $performerRows,
        array $sessionRows,
        array $sessionPerformerRows
    ): void {
        $this->db->beginTransaction();

        try {
            $this->updateEventVenues($eventId, $venueRows);
            $this->updateEventPerformers($eventId, $performerRows);
            $this->syncDetailPagePageSlugsToPerformers($eventId, $performerRows);
            $this->updateEventSessions($eventId, $sessionRows);
            $this->replaceEventSessionPerformers($eventId, $sessionPerformerRows);
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Updates venue records for one event so CMS venue edits persist without touching other events.
    private function updateEventVenues(int $eventId, array $rows): void
    {
        if ($rows === []) {
            return;
        }

        $update = $this->db->prepare(
            'UPDATE venues
             SET venue_name = :venue_name, address = :address, venue_type = :venue_type
             WHERE id = :id AND event_id = :event_id'
        );

        foreach ($rows as $row) {
            $update->execute([
                ':venue_name' => $row['venue_name'],
                ':address' => $row['address'],
                ':venue_type' => $row['venue_type'],
                ':id' => $row['id'],
                ':event_id' => $eventId,
            ]);
        }
    }

    // Updates performer records for one event so CMS performer edits persist and remain scoped to the selected event.
    private function updateEventPerformers(int $eventId, array $rows): void
    {
        if ($rows === []) {
            return;
        }

        $update = $this->db->prepare(
            'UPDATE performers
             SET performer_name = :performer_name, performer_type = :performer_type, description = :description
             WHERE id = :id AND event_id = :event_id'
        );

        foreach ($rows as $row) {
            $update->execute([
                ':performer_name' => $row['performer_name'],
                ':performer_type' => $row['performer_type'],
                ':description' => $row['description'],
                ':id' => $row['id'],
                ':event_id' => $eventId,
            ]);
        }
    }

    // Updates session records for one event so CMS schedule edits persist date, time, venue, and capacity changes.
    private function updateEventSessions(int $eventId, array $rows): void
    {
        if ($rows === []) {
            return;
        }

        $update = $this->db->prepare(
            'UPDATE sessions
             SET venue_id = :venue_id, date = :date, start_time = :start_time, label = :label, price = :price, available_spots = :available_spots
             WHERE id = :id AND event_id = :event_id'
        );

        foreach ($rows as $row) {
            $update->execute([
                ':venue_id' => $row['venue_id'],
                ':date' => $row['date'],
                ':start_time' => $row['start_time'],
                ':label' => $row['label'],
                ':price' => $row['price'],
                ':available_spots' => $row['available_spots'],
                ':id' => $row['id'],
                ':event_id' => $eventId,
            ]);
        }
    }

    // Syncs performer-driven detail page slugs so dance detail URLs stay aligned with renamed performers.
    private function syncDetailPagePageSlugsToPerformers(int $eventId, array $performerRows): void
    {
        if ($performerRows === []) {
            return;
        }

        $update = $this->db->prepare(
            'UPDATE pages p
             INNER JOIN event_detail_pages edp ON edp.page_id = p.id
             SET p.slug = :page_slug
             WHERE edp.event_id = :event_id
               AND edp.performer_id = :performer_id'
        );

        foreach ($performerRows as $row) {
            $pageSlug = trim((string)($row['page_slug'] ?? ''));
            if ($pageSlug === '') {
                continue;
            }

            $update->execute([
                ':page_slug' => $pageSlug,
                ':event_id' => $eventId,
                ':performer_id' => (int)$row['id'],
            ]);
        }
    }

    // Replaces performer assignments for one event so the saved session-performer links exactly match the posted state.
    private function replaceEventSessionPerformers(int $eventId, array $rows): void
    {
        $delete = $this->db->prepare(
            'DELETE sp
             FROM session_performers sp
             INNER JOIN sessions s ON s.id = sp.session_id
             WHERE s.event_id = :event_id'
        );
        $delete->execute([':event_id' => $eventId]);

        if ($rows === []) {
            return;
        }

        $insert = $this->db->prepare(
            'INSERT INTO session_performers (session_id, performer_id)
             VALUES (:session_id, :performer_id)'
        );

        foreach ($rows as $row) {
            $insert->execute([
                ':session_id' => $row['session_id'],
                ':performer_id' => $row['performer_id'],
            ]);
        }
    }

    // Finds one event by id so CMS schedule edit flows can rebuild the correct event context.
    public function findEventById(int $id): ?EventModel
    {
        $stmt = $this->db->prepare('SELECT id, name, description FROM events WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->scheduleMapper->mapEventRow($row) : null;
    }

    // Updates one schedule row so standalone CMS schedule edits reuse the same repository.
    public function editSchedule(int $id, int $eventId, int $venueId, string $date, string $startTime, int $availableSpots, ?string $label, ?float $price, ?int $language, array $performerIds = []): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE sessions
            SET venue_id = :venue_id, event_id = :event_id, date = :date, start_time = :start_time, available_spots = :available_spots, label = :label, price = :price, language_id = :language
            WHERE id = :id'
        );

        $updated = $stmt->execute([
            ':id' => $id,
            ':event_id' => $eventId,
            ':venue_id' => $venueId,
            ':date' => $date,
            ':start_time' => $startTime,
            ':available_spots' => $availableSpots,
            ':label' => $label ?? 'None',
            ':language' => $language ?? 1,
            ':price' => $price ?? -1,
        ]);

        if (!$updated) {
            return false;
        }

        $this->replaceSchedulePerformers($id, $performerIds);

        return true;
    }

    // Creates one schedule row and links selected performers so the standalone CMS schedule editor can add sessions.
    public function createSchedule(int $eventId, int $venueId, string $date, string $startTime, int $availableSpots, ?string $label, ?float $price, ?int $language, array $performerIds = []): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO sessions (event_id, venue_id, date, start_time, available_spots, label, price, language_id)
             VALUES (:event_id, :venue_id, :date, :start_time, :available_spots, :label, :price, :language)'
        );

        $success = $stmt->execute([
            ':event_id' => $eventId,
            ':venue_id' => $venueId,
            ':date' => $date,
            ':start_time' => $startTime,
            ':available_spots' => $availableSpots,
            ':label' => $label ?? 'None',
            ':price' => $price ?? -1,
            ':language' => $language ?? 1,
        ]);

        if (!$success) {
            return false;
        }

        $sessionId = (int)$this->db->lastInsertId();

        $this->replaceSchedulePerformers($sessionId, $performerIds);

        return true;
    }

    // Deletes one schedule row after clearing performer links so the standalone CMS schedule editor does not leave orphan assignments.
    public function deleteSchedule(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM session_performers WHERE session_id = :session_id');
        $stmt->execute([':session_id' => $id]);

        $stmt = $this->db->prepare('DELETE FROM sessions WHERE id = :id');
        return $stmt->execute([
            ':id' => $id,
        ]);
    }

    private function replaceSchedulePerformers(int $sessionId, array $performerIds): void
    {
        $delete = $this->db->prepare('DELETE FROM session_performers WHERE session_id = :session_id');
        $delete->execute([':session_id' => $sessionId]);

        if ($performerIds === []) {
            return;
        }

        $insert = $this->db->prepare(
            'INSERT INTO session_performers (session_id, performer_id) VALUES (:session_id, :performer_id)'
        );

        foreach ($performerIds as $performerId) {
            $insert->execute([
                ':session_id' => $sessionId,
                ':performer_id' => (int)$performerId,
            ]);
        }
    }
}
