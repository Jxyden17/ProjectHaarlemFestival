<?php

namespace App\Repository;

use App\Models\Database;
use App\Repository\Interfaces\ITicketRepository;
use PDO;

class TicketRepository implements ITicketRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function listByEventId(int $eventId): array
    {
        $stmt = $this->db->prepare(
            'SELECT t.id AS ticket_id,
                    t.order_id,
                    t.user_id,
                    u.email AS user_email,
                    t.session_id,
                    s.date AS session_date,
                    s.start_time,
                    s.price,
                    COALESCE(ts.status_name, \'Unknown\') AS status_name,
                    t.status_id,
                    t.qr_code
             FROM tickets t
             INNER JOIN sessions s ON s.id = t.session_id
             INNER JOIN users u ON u.id = t.user_id
             LEFT JOIN ticket_statuses ts ON ts.id = t.status_id
             WHERE s.event_id = :event_id
             ORDER BY s.date ASC, s.start_time ASC, t.id ASC'
        );
        $stmt->execute([':event_id' => $eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT t.id AS ticket_id,
                    t.order_id,
                    t.user_id,
                    u.email AS user_email,
                    t.session_id,
                    s.date AS session_date,
                    s.start_time,
                    s.event_id,
                    COALESCE(ts.status_name, \'Unknown\') AS status_name,
                    t.status_id,
                    t.qr_code
             FROM tickets t
             INNER JOIN sessions s ON s.id = t.session_id
             INNER JOIN users u ON u.id = t.user_id
             LEFT JOIN ticket_statuses ts ON ts.id = t.status_id
             WHERE t.id = :id'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createBulk(int $userId, int $orderId, int $sessionId, int $quantity): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO tickets (user_id, order_id, session_id, status_id)
             VALUES (:user_id, :order_id, :session_id, 1)'
        );
        $count = 0;
        for ($i = 0; $i < $quantity; $i++) {
            $stmt->execute([
                ':user_id'    => $userId,
                ':order_id'   => $orderId,
                ':session_id' => $sessionId,
            ]);
            $count++;
        }
        return $count;
    }

    public function update(int $id, int $statusId, ?string $qrCode): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE tickets SET status_id = :status_id, qr_code = :qr_code WHERE id = :id'
        );
        return $stmt->execute([
            ':status_id' => $statusId,
            ':qr_code'   => $qrCode,
            ':id'        => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM tickets WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
