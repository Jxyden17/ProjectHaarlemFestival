<?php

namespace App\Repository;

use App\Mapper\ScheduleMapper;
use App\Models\Database;
use App\Models\Event\EventModel;
use App\Repository\Interfaces\IScheduleRepository;
use PDO;
use PDOException;

class ScheduleRepository implements IScheduleRepository
{
    private PDO $db;
    private ScheduleMapper $scheduleMapper;

    public function __construct(ScheduleMapper $scheduleMapper)
    {
        $this->db = Database::getInstance();
        $this->scheduleMapper = $scheduleMapper;
    }

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

    public function getAllEvents(): array
    {
        $stmt = $this->db->query('SELECT id, name, description FROM events ORDER BY id ASC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            fn(array $row): EventModel => $this->scheduleMapper->mapEventRow($row),
            $rows
        );
    }

    public function getSessionsByEventId(int $eventId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, event_id, venue_id, date, start_time, language_id, label, price, available_spots, amount_sold
             FROM sessions
             WHERE event_id = :event_id
             ORDER BY date ASC, start_time ASC'
        );
        $stmt->execute([':event_id' => $eventId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn(array $row) => $this->scheduleMapper->mapSessionRow($row), $rows);
    }

    public function getSessionById(int $id): ?\App\Models\Event\SessionModel
    {
        $stmt = $this->db->prepare(
            'SELECT id, event_id, venue_id, date, start_time, language_id, label, price, available_spots, amount_sold
             FROM sessions
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->scheduleMapper->mapSessionRow($row) : null;
    }

    public function createSessionForTicketing(int $eventId, int $venueId, string $date, string $startTime, int $availableSpots): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO sessions (event_id, venue_id, date, start_time, label, price, available_spots, amount_sold)
             VALUES (:event_id, :venue_id, :date, :start_time, NULL, 0, :available_spots, 0)'
        );
        $stmt->execute([
            ':event_id' => $eventId,
            ':venue_id' => $venueId,
            ':date' => $date,
            ':start_time' => $startTime,
            ':available_spots' => $availableSpots,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateSessionForTicketing(int $id, int $eventId, string $date, string $startTime, int $availableSpots): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE sessions
             SET date = :date, start_time = :start_time, available_spots = :available_spots
             WHERE id = :id AND event_id = :event_id'
        );

        return $stmt->execute([
            ':id' => $id,
            ':event_id' => $eventId,
            ':date' => $date,
            ':start_time' => $startTime,
            ':available_spots' => $availableSpots,
        ]);
    }

    public function deleteSessionById(int $id, int $eventId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM sessions WHERE id = :id AND event_id = :event_id');

        return $stmt->execute([
            ':id' => $id,
            ':event_id' => $eventId,
        ]);
    }

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
            $this->syncDetailPageSlugsToPerformers($eventId, $performerRows);
            $this->updateEventSessions($eventId, $sessionRows);
            $this->replaceEventSessionPerformers($eventId, $sessionPerformerRows);
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

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
    private function syncDetailPageSlugsToPerformers(int $eventId, array $performerRows): void
    {
        if ($performerRows === []) {
            return;
        }

        foreach ($performerRows as $row) {
            $slug = trim((string)($row['detail_slug'] ?? ''));
            if ($slug === '') {
                continue;
            }

            $params = [
                ':detail_slug' => $slug,
                ':event_id' => $eventId,
                ':performer_id' => (int)$row['id'],
            ];

            $this->executeSlugUpdateWithFallback($params);
        }
    }

    private function executeSlugUpdateWithFallback(array $params): void
    {
        $queries = [
            'UPDATE event_detail_pages
             SET detail_slug = :detail_slug
             WHERE event_id = :event_id
               AND performer_id = :performer_id',
            'UPDATE event_detail_pages
             SET public_slug = :detail_slug,
                 cms_slug = :detail_slug
             WHERE event_id = :event_id
               AND performer_id = :performer_id',
            'UPDATE pages p
             INNER JOIN event_detail_pages edp ON edp.page_id = p.id
             SET p.slug = :detail_slug
             WHERE edp.event_id = :event_id
               AND edp.performer_id = :performer_id',
        ];

        $lastException = null;

        foreach ($queries as $query) {
            try {
                $stmt = $this->db->prepare($query);
                $stmt->execute($params);
                return;
            } catch (PDOException $exception) {
                $lastException = $exception;
            }
        }

        if ($lastException instanceof PDOException) {
            throw $lastException;
        }
    }
}
