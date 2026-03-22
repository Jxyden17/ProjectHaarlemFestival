<?php

namespace App\Repository;

use App\Models\Database;
use App\Repository\Interfaces\IPaymentRepository;
use PDO;

class PaymentRepository implements IPaymentRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findOrderById(int $orderId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, user_id, total_amount, created_at
             FROM orders
             WHERE id = :id
             LIMIT 1'
        );

        $stmt->execute([':id' => $orderId]);

        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        return $order ?: null;
    }

    public function createPaymentRecord(int $orderId, string $method, string $status, ?string $providerPaymentId = null): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO payments (order_id, method, status)
             VALUES (:order_id, :method, :status)'
        );

        $stmt->execute([
            ':order_id' => $orderId,
            ':method' => $method,
            ':status' => $status,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updatePaymentStatus(string $providerPaymentId, string $status): void
    {
        $payment = $this->findPaymentByProviderPaymentId($providerPaymentId);
        if ($payment === null) {
            return;
        }

        $stmt = $this->db->prepare(
            'UPDATE payments
             SET status = :status
             WHERE id = :id'
        );

        $stmt->execute([
            ':status' => $status,
            ':id' => (int) $payment['id'],
        ]);
    }

    public function updatePaymentProviderId(int $paymentRecordId, string $providerPaymentId): void
    {
        // Current schema does not have a dedicated provider_payment_id column.
        // We keep the local record minimal and rely on Mollie metadata for correlation.
    }

    public function findPaymentByProviderPaymentId(string $providerPaymentId): ?array
    {
        // Current schema does not persist provider_payment_id yet.
        return null;
    }

    public function findPaymentByOrderId(int $orderId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, order_id, method, status
             FROM payments
             WHERE order_id = :order_id
             ORDER BY id DESC
             LIMIT 1'
        );

        $stmt->execute([':order_id' => $orderId]);

        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        return $payment ?: null;
    }

    public function markOrderAsPaid(int $orderId): void
    {
        // Current orders schema does not have a status column yet.
        // This method is intentionally a no-op until that column is added.
    }

    public function updatePaymentStatusByOrderId(int $orderId, string $status): void
    {
        $stmt = $this->db->prepare(
            'UPDATE payments
             SET status = :status
             WHERE order_id = :order_id'
        );

        $stmt->execute([
            ':status' => $status,
            ':order_id' => $orderId,
        ]);
    }
}
