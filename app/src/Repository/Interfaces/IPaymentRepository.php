<?php

namespace App\Repository\Interfaces;

interface IPaymentRepository
{
    //Find an order by its ID
    public function findOrderById(int $orderId): ?array;
    //Create a new payment record for an order with the specified method, status, and optional provider payment ID
    public function createPaymentRecord(int $orderId, string $method, string $status, ?string $providerPaymentId = null): int;
    //Update the payment status of a payment record identified by the provider payment ID
    public function updatePaymentStatus(string $providerPaymentId, string $status): void;
    //Update the provider payment ID for a payment record identified by its internal ID
    public function updatePaymentProviderId(int $paymentRecordId, string $providerPaymentId): void;
    //Find a payment record by its provider payment ID
    public function findPaymentByProviderPaymentId(string $providerPaymentId): ?array;
    //Find a payment record by the associated order ID
    public function findPaymentByOrderId(int $orderId): ?array;
    //Mark an order as paid by updating its status in the database
    public function markOrderAsPaid(int $orderId): void;
}
