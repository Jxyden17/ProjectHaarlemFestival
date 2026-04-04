<?php

namespace App\Repository;

use App\Models\Database;
use PDO;

class CmsTicketManagementRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getEventTicketMetrics(): array
    {
        $stmt = $this->db->query(
            'SELECT
                s.event_id,
                COUNT(DISTINCT s.id) AS session_count,
                SUM(CASE WHEN s.available_spots > -1 THEN s.available_spots ELSE 0 END) AS capacity_total,
                SUM(CASE WHEN s.available_spots > -1 THEN GREATEST(s.available_spots - COALESCE(s.amount_sold, 0), 0) ELSE 0 END) AS available_total,
                SUM(COALESCE(s.amount_sold, 0)) AS sold_total,
                SUM(CASE WHEN s.available_spots = -1 THEN 1 ELSE 0 END) AS unlimited_sessions
             FROM sessions s
             GROUP BY s.event_id
             ORDER BY s.event_id ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getIssuedTicketCountsByEvent(): array
    {
        $stmt = $this->db->query(
            'SELECT
                s.event_id,
                COUNT(t.id) AS issued_ticket_count
             FROM tickets t
             INNER JOIN sessions s ON s.id = t.session_id
             GROUP BY s.event_id
             ORDER BY s.event_id ASC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPaymentStatusTotals(): array
    {
        $stmt = $this->db->query(
            'SELECT
                SUM(CASE WHEN status = \'pending\' THEN 1 ELSE 0 END) AS pending_payments,
                SUM(CASE WHEN status = \'paid\' THEN 1 ELSE 0 END) AS paid_payments,
                SUM(CASE WHEN status = \'failed\' THEN 1 ELSE 0 END) AS failed_payments
             FROM payments'
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: [
            'pending_payments' => 0,
            'paid_payments' => 0,
            'failed_payments' => 0,
        ];
    }

    public function getSoldTicketsByEventId(int $eventId): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                t.id AS ticket_id,
                t.order_id,
                t.user_id,
                u.email AS customer_email,
                s.id AS session_id,
                s.label AS session_label,
                s.date AS session_date,
                s.start_time,
                s.price,
                s.available_spots,
                s.amount_sold,
                COALESCE(ts.status_name, \'Unknown\') AS ticket_status,
                COALESCE(p.status, \'unknown\') AS payment_status,
                COALESCE(o.status, \'unknown\') AS order_status,
                t.qr_code
             FROM tickets t
             INNER JOIN sessions s ON s.id = t.session_id
             INNER JOIN users u ON u.id = t.user_id
             LEFT JOIN ticket_statuses ts ON ts.id = t.status_id
             LEFT JOIN payments p ON p.order_id = t.order_id
             LEFT JOIN orders o ON o.id = t.order_id
             WHERE s.event_id = :event_id
             ORDER BY s.date DESC, s.start_time DESC, t.id DESC'
        );

        $stmt->execute([':event_id' => $eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
