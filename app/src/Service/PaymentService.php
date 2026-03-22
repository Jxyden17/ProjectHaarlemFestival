<?php

namespace App\Service;

use App\Repository\Interfaces\IPaymentRepository;
use App\Service\Interfaces\IPaymentService;
use Mollie\Api\MollieApiClient;

class PaymentService implements IPaymentService
{
    private IPaymentRepository $paymentRepository;
    private ?MollieApiClient $mollieClient = null;
    private string $baseUrl;
    private string $mollieApiKey;
    private string $paymentDriver;

    public function __construct(IPaymentRepository $paymentRepository, string $mollieApiKey, string $baseUrl, string $paymentDriver = 'mollie')
    {
        $this->paymentRepository = $paymentRepository;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->mollieApiKey = trim($mollieApiKey);
        $this->paymentDriver = strtolower(trim($paymentDriver)) !== '' ? strtolower(trim($paymentDriver)) : 'mollie';
    }

    public function createPayment(int $orderId): string
    {
        if ($orderId <= 0) {
            throw new \RuntimeException('Invalid order.');
        }

        if (trim($this->baseUrl) === '') {
            throw new \RuntimeException('Application base URL is missing.');
        }

        if ($this->paymentDriver === 'mock') {
            return $this->createMockPayment($orderId);
        }

        if ($this->mollieApiKey === '' || $this->mollieApiKey === 'test_xxxxx') {
            throw new \RuntimeException('Configure a valid MOLLIE_API_KEY in the .env file before starting payments.');
        }

        $order = $this->paymentRepository->findOrderById($orderId);
        if ($order === null) {
            throw new \RuntimeException('Order not found.');
        }

        if ((float) ($order['total_amount'] ?? 0) <= 0) {
            throw new \RuntimeException('Order total must be greater than zero.');
        }

        $payment = $this->getMollieClient()->payments->create([
            'amount' => [
                'currency' => 'EUR',
                'value' => number_format((float) $order['total_amount'], 2, '.', ''),
            ],
            'description' => 'Haarlem Festival order #' . (int) $order['id'],
            'method' => 'ideal',
            'redirectUrl' => $this->baseUrl . '/payment/return?order_id=' . (int) $order['id'],
            'webhookUrl' => $this->baseUrl . '/payment/webhook',
            'metadata' => [
                'order_id' => (int) $order['id'],
            ],
        ]);

        $checkoutUrl = (string) ($payment->getCheckoutUrl() ?? '');
        if ($checkoutUrl === '') {
            throw new \RuntimeException('Could not create a checkout URL for this payment.');
        }

        $this->paymentRepository->createPaymentRecord(
            $orderId,
            'ideal',
            (string) ($payment->status ?? 'open'),
            (string) ($payment->id ?? null)
        );

        return $checkoutUrl;
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

        $payment = $this->getMollieClient()->payments->get($providerPaymentId);
        $status = (string) ($payment->status ?? 'unknown');
        $this->paymentRepository->updatePaymentStatus($providerPaymentId, $status);

        if ($status === 'paid') {
            $this->paymentRepository->markOrderAsPaid($orderId);
        }

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

        $payment = $this->getMollieClient()->payments->get($providerPaymentId);
        $orderId = (int) ($payment->metadata->order_id ?? 0);
        $status = (string) ($payment->status ?? 'unknown');

        if ($orderId <= 0) {
            return;
        }

        $this->paymentRepository->updatePaymentStatus($providerPaymentId, $status);

        if ($status === 'paid') {
            $this->paymentRepository->markOrderAsPaid($orderId);
        }
    }

    public function getPaymentStatus(string $providerPaymentId): string
    {
        $payment = $this->getMollieClient()->payments->get($providerPaymentId);

        return (string) ($payment->status ?? 'unknown');
    }

    public function getMockPaymentData(int $orderId): array
    {
        if ($orderId <= 0) {
            throw new \RuntimeException('Invalid order.');
        }

        $order = $this->paymentRepository->findOrderById($orderId);
        if ($order === null) {
            throw new \RuntimeException('Order not found.');
        }

        $payment = $this->paymentRepository->findPaymentByOrderId($orderId);
        if ($payment === null) {
            throw new \RuntimeException('Payment record not found for this order.');
        }

        return [
            'order' => $order,
            'payment' => $payment,
        ];
    }

    public function completeMockPayment(int $orderId, string $status): array
    {
        if ($this->paymentDriver !== 'mock') {
            throw new \RuntimeException('Mock payment flow is not enabled.');
        }

        $allowedStatuses = ['paid', 'failed', 'canceled'];
        if (!in_array($status, $allowedStatuses, true)) {
            throw new \RuntimeException('Invalid mock payment status.');
        }

        $paymentData = $this->getMockPaymentData($orderId);
        $payment = $paymentData['payment'];
        $providerPaymentId = trim((string) ($payment['provider_payment_id'] ?? ''));

        if ($providerPaymentId !== '') {
            $this->paymentRepository->updatePaymentStatus($providerPaymentId, $status);
        } else {
            $this->paymentRepository->updatePaymentStatusByOrderId($orderId, $status);
        }

        $this->paymentRepository->updateOrderStatus($orderId, $status === 'paid' ? 'paid' : $status);

        return [
            'status' => $status,
            'orderId' => $orderId,
            'isPaid' => $status === 'paid',
        ];
    }

    private function createMockPayment(int $orderId): string
    {
        $order = $this->paymentRepository->findOrderById($orderId);
        if ($order === null) {
            throw new \RuntimeException('Order not found.');
        }

        if ((float) ($order['total_amount'] ?? 0) <= 0) {
            throw new \RuntimeException('Order total must be greater than zero.');
        }

        $existingPayment = $this->paymentRepository->findPaymentByOrderId($orderId);
        if ($existingPayment === null) {
            $this->paymentRepository->createPaymentRecord($orderId, 'mock', 'open', 'mock_' . $orderId);
        } else {
            $this->paymentRepository->updatePaymentStatusByOrderId($orderId, 'open');
            $providerPaymentId = trim((string) ($existingPayment['provider_payment_id'] ?? ''));
            if ($providerPaymentId === '') {
                $this->paymentRepository->updatePaymentProviderId((int) $existingPayment['id'], 'mock_' . $orderId);
            }
        }

        $this->paymentRepository->updateOrderStatus($orderId, 'pending');

        return $this->baseUrl . '/payment/mock/' . $orderId;
    }

    private function getMollieClient(): MollieApiClient
    {
        if ($this->mollieApiKey === '' || $this->mollieApiKey === 'test_xxxxx') {
            throw new \RuntimeException('Configure a valid MOLLIE_API_KEY in the .env file before starting payments.');
        }

        if ($this->mollieClient === null) {
            $this->mollieClient = new MollieApiClient();
            $this->mollieClient->setApiKey($this->mollieApiKey);
        }

        return $this->mollieClient;
    }
}
