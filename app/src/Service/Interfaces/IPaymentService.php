<?php

namespace App\Service\Interfaces;

interface IPaymentService
{
    public function createPayment(int $orderId, int $cartId): string;

    public function handleReturn(int $orderId): array;

    public function handleCancellation(int $orderId): array;

    public function handleWebhook(string $payload, string $signature = ''): void;

    public function getPaymentStatus(string $providerPaymentId): string;
}
