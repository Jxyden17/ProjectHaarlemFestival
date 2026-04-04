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
}
