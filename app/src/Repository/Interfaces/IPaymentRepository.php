<?php

namespace App\Repository\Interfaces;

interface IPaymentRepository
{
    public function findOrderById(int $orderId): ?array;

    public function createPaymentRecord(int $orderId, int $cartId, string $method, string $status, ?string $providerPaymentId = null): int;

    public function updatePaymentStatus(string $providerPaymentId, string $status): void;

    public function updatePaymentProviderId(int $paymentRecordId, string $providerPaymentId): void;

    public function findPaymentByProviderPaymentId(string $providerPaymentId): ?array;

    public function findPaymentByOrderId(int $orderId): ?array;

    public function updatePaymentStatusByOrderId(int $orderId, string $status): void;

    public function updateOrderStatus(int $orderId, string $status): void;

    public function markOrderAsPaid(int $orderId): void;

    public function markCartAsPaid(int $cartId): void;
}
