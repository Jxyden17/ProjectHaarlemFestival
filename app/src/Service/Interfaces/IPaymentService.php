<?php

namespace App\Service\Interfaces;

interface IPaymentService
{
    public function createPayment(int $orderId): string;

    public function handleReturn(int $orderId): array;

    public function handleWebhook(string $providerPaymentId): void;

    public function getPaymentStatus(string $providerPaymentId): string;

    public function getMockPaymentData(int $orderId): array;

    public function completeMockPayment(int $orderId, string $status): array;
}
