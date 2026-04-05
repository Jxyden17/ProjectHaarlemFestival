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

    public function createPaymentRecord(int $orderId, int $cartId, string $method, string $status, ?string $providerPaymentId = null): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO payments (order_id, cart_id, method, status, provider_payment_id)
             VALUES (:order_id, :cart_id, :method, :status, :provider_payment_id)'
        );

        $stmt->execute([
            ':order_id' => $orderId,
            ':cart_id' => $cartId,
            ':method' => $method,
            ':status' => $status,
            ':provider_payment_id' => $providerPaymentId,
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
        $stmt = $this->db->prepare(
            'UPDATE payments
             SET provider_payment_id = :provider_payment_id
             WHERE id = :id'
        );

        $stmt->execute([
            ':provider_payment_id' => $providerPaymentId,
            ':id' => $paymentRecordId,
        ]);
    }

    public function findPaymentByProviderPaymentId(string $providerPaymentId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, order_id, cart_id, method, status, provider_payment_id
             FROM payments
             WHERE provider_payment_id = :provider_payment_id
             ORDER BY id DESC
             LIMIT 1'
        );

        $stmt->execute([':provider_payment_id' => $providerPaymentId]);

        $payment = $stmt->fetch(PDO::FETCH_ASSOC);

        return $payment ?: null;
    }

    public function findPaymentByOrderId(int $orderId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, order_id, cart_id, method, status, provider_payment_id
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
        $this->updateOrderStatus($orderId, 'paid');
    }

    public function markCartAsPaid(int $cartId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE shopping_carts
             SET status = :status
             WHERE id = :id'
        );

        $stmt->execute([
            ':status' => 'paid',
            ':id' => $cartId,
        ]);
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

    public function updateOrderStatus(int $orderId, string $status): void
    {
        $stmt = $this->db->prepare(
            'UPDATE orders
             SET status = :status
             WHERE id = :id'
        );

        $stmt->execute([
            ':status' => $status,
            ':id' => $orderId,
        ]);
    }

    public function acquireFulfillmentLock(int $orderId, int $timeoutSeconds = 10): bool
    {
        $stmt = $this->db->prepare('SELECT GET_LOCK(:lock_name, :timeout_seconds) AS lock_acquired');
        $stmt->execute([
            ':lock_name' => $this->buildFulfillmentLockName($orderId),
            ':timeout_seconds' => max(1, $timeoutSeconds),
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) ($result['lock_acquired'] ?? 0) === 1;
    }

    public function releaseFulfillmentLock(int $orderId): void
    {
        $stmt = $this->db->prepare('SELECT RELEASE_LOCK(:lock_name)');
        $stmt->execute([
            ':lock_name' => $this->buildFulfillmentLockName($orderId),
        ]);
    }

    private function buildFulfillmentLockName(int $orderId): string
    {
        return 'ticket_fulfillment_order_' . $orderId;
    }
}
