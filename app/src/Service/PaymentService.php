<?php

namespace App\Service;

use App\Repository\Interfaces\IPaymentRepository;
use App\Service\Interfaces\IPaymentService;

class PaymentService implements IPaymentService
{
    private IPaymentRepository $paymentRepository;
    private string $baseUrl;
    private string $paymentDriver;

    public function __construct(IPaymentRepository $paymentRepository, string $baseUrl, string $paymentDriver = 'stripe')
    {
        $this->paymentRepository = $paymentRepository;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->paymentDriver = strtolower(trim($paymentDriver)) !== '' ? strtolower(trim($paymentDriver)) : 'stripe';
    }

    public function createPayment(int $orderId): string
    {
        if ($orderId <= 0) {
            throw new \RuntimeException('Invalid order.');
        }

        if (trim($this->baseUrl) === '') {
            throw new \RuntimeException('Application base URL is missing.');
        }

        if ($this->paymentDriver === 'stripe') {
            throw new \RuntimeException('Stripe payment flow is not configured yet.');
        }

        throw new \RuntimeException('Unsupported payment driver: ' . $this->paymentDriver);
    }

    public function handleReturn(int $orderId): array
    {
        if ($orderId <= 0) {
            throw new \RuntimeException('Missing order identifier.');
        }

        $paymentRecord = $this->paymentRepository->findPaymentByOrderId($orderId);
        if ($paymentRecord === null) {
            throw new \RuntimeException('Payment record not found for this order.');
        }

        $providerPaymentId = trim((string) ($paymentRecord['provider_payment_id'] ?? ''));
        if ($providerPaymentId === '') {
            throw new \RuntimeException('Payment provider reference is missing for this order.');
        }

        $status = (string) ($paymentRecord['status'] ?? 'unknown');

        return [
            'status' => $status,
            'orderId' => $orderId,
            'isPaid' => $status === 'paid',
        ];
    }

    public function handleWebhook(string $providerPaymentId): void
    {
        if (trim($providerPaymentId) === '') {
            return;
        }

        throw new \RuntimeException('Webhook handling is not configured for the selected payment driver.');
    }

    public function getPaymentStatus(string $providerPaymentId): string
    {
        $paymentRecord = $this->paymentRepository->findPaymentByProviderPaymentId($providerPaymentId);
        if ($paymentRecord === null) {
            throw new \RuntimeException('Payment record not found.');
        }

        return (string) ($paymentRecord['status'] ?? 'unknown');
    }
}
