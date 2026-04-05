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

    public function getSoldTicketById(int $ticketId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT
                t.id AS ticket_id,
                t.order_id,
                t.user_id,
                u.email AS customer_email,
                s.id AS session_id,
                s.event_id,
                s.label AS session_label,
                s.date AS session_date,
                s.start_time,
                s.price,
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
             WHERE t.id = :ticket_id
             LIMIT 1'
        );

        $stmt->execute([':ticket_id' => $ticketId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function getOrders(?int $eventId = null): array
    {
        $sql = $this->buildOrderSummaryBaseQuery();
        $params = [];

        if ((int) $eventId > 0) {
            $sql .= '
             WHERE EXISTS (
                SELECT 1
                FROM shopping_cart_items sci_filter
                INNER JOIN sessions s_filter ON s_filter.id = sci_filter.session_id
                WHERE sci_filter.cart_id = p.cart_id
                  AND s_filter.event_id = :event_id
             )';
            $params[':event_id'] = (int) $eventId;
        }

        $sql .= ' ORDER BY o.created_at DESC, o.id DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderById(int $orderId): ?array
    {
        $stmt = $this->db->prepare($this->buildOrderSummaryBaseQuery() . '
             WHERE o.id = :order_id
             LIMIT 1');

        $stmt->execute([':order_id' => $orderId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function getOrderCartItems(int $cartId): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                sci.id AS cart_item_id,
                sci.session_id,
                sci.quantity,
                sci.unit_price,
                s.event_id,
                e.name AS event_name,
                s.label AS session_label,
                s.date AS session_date,
                s.start_time,
                v.venue_name
             FROM shopping_cart_items sci
             INNER JOIN sessions s ON s.id = sci.session_id
             LEFT JOIN events e ON e.id = s.event_id
             LEFT JOIN venues v ON v.id = s.venue_id
             WHERE sci.cart_id = :cart_id
             ORDER BY s.date ASC, s.start_time ASC, sci.id ASC'
        );

        $stmt->execute([':cart_id' => $cartId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderTickets(int $orderId): array
    {
        $stmt = $this->db->prepare(
            'SELECT
                t.id AS ticket_id,
                t.order_id,
                t.user_id,
                u.email AS customer_email,
                s.id AS session_id,
                s.event_id,
                e.name AS event_name,
                s.label AS session_label,
                s.date AS session_date,
                s.start_time,
                v.venue_name,
                COALESCE(ts.status_name, \'Unknown\') AS ticket_status,
                COALESCE(p.status, \'unknown\') AS payment_status,
                t.qr_code
             FROM tickets t
             INNER JOIN users u ON u.id = t.user_id
             INNER JOIN sessions s ON s.id = t.session_id
             LEFT JOIN events e ON e.id = s.event_id
             LEFT JOIN venues v ON v.id = s.venue_id
             LEFT JOIN ticket_statuses ts ON ts.id = t.status_id
             LEFT JOIN payments p ON p.order_id = t.order_id
             WHERE t.order_id = :order_id
             ORDER BY s.date ASC, s.start_time ASC, t.id ASC'
        );

        $stmt->execute([':order_id' => $orderId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function buildOrderSummaryBaseQuery(): string
    {
        return '
            SELECT
                o.id AS order_id,
                o.user_id,
                o.total_amount,
                o.status AS order_status,
                o.created_at,
                u.email AS customer_email,
                COALESCE(p.status, \'unknown\') AS payment_status,
                COALESCE(p.method, \'\') AS payment_method,
                p.cart_id,
                p.provider_payment_id,
                COALESCE(cart_summary.item_quantity, 0) AS expected_ticket_count,
                COALESCE(cart_summary.session_count, 0) AS session_count,
                COALESCE(cart_summary.event_names, \'\') AS event_names,
                COALESCE(ticket_summary.issued_ticket_count, 0) AS issued_ticket_count
             FROM orders o
             LEFT JOIN users u ON u.id = o.user_id
             LEFT JOIN payments p ON p.order_id = o.id
             LEFT JOIN (
                SELECT
                    sci.cart_id,
                    SUM(sci.quantity) AS item_quantity,
                    COUNT(DISTINCT sci.session_id) AS session_count,
                    GROUP_CONCAT(DISTINCT e.name ORDER BY e.name SEPARATOR \', \') AS event_names
                FROM shopping_cart_items sci
                INNER JOIN sessions s ON s.id = sci.session_id
                LEFT JOIN events e ON e.id = s.event_id
                GROUP BY sci.cart_id
             ) cart_summary ON cart_summary.cart_id = p.cart_id
             LEFT JOIN (
                SELECT
                    t.order_id,
                    COUNT(t.id) AS issued_ticket_count
                FROM tickets t
                GROUP BY t.order_id
             ) ticket_summary ON ticket_summary.order_id = o.id';
    }
}
