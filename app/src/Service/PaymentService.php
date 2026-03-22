<?php

namespace App\Service;

use App\Repository\Interfaces\IPaymentRepository;
use App\Service\Interfaces\IPaymentService;
use Mollie\Api\MollieApiClient;

class PaymentService implements IPaymentService
{
    private IPaymentRepository $paymentRepository;
    private MollieApiClient $mollieClient;
    private string $baseUrl;
    private string $mollieApiKey;

    public function __construct(IPaymentRepository $paymentRepository, string $mollieApiKey, string $baseUrl)
    {
        $this->paymentRepository = $paymentRepository;
        $this->mollieClient = new MollieApiClient();
        $this->mollieClient->setApiKey($mollieApiKey);
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->mollieApiKey = trim($mollieApiKey);
    }

    public function createPayment(int $orderId): string
    {
        if ($orderId <= 0) {
            throw new \RuntimeException('Invalid order.');
        }

        if (trim($this->baseUrl) === '') {
            throw new \RuntimeException('Application base URL is missing.');
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

        $payment = $this->mollieClient->payments->create([
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

        $_SESSION['mollie_payment_ids'][$orderId] = (string) ($payment->id ?? '');

        return $checkoutUrl;
    }

    public function handleReturn(int $orderId): array
    {
        if ($orderId <= 0) {
            throw new \RuntimeException('Missing order identifier.');
        }

        $providerPaymentId = trim((string) ($_SESSION['mollie_payment_ids'][$orderId] ?? ''));
        $status = 'unknown';

        if ($providerPaymentId !== '') {
            $payment = $this->mollieClient->payments->get($providerPaymentId);
            $status = (string) ($payment->status ?? 'unknown');
            $this->paymentRepository->updatePaymentStatusByOrderId($orderId, $status);

            if ($status === 'paid') {
                $this->paymentRepository->markOrderAsPaid($orderId);
            }
        } else {
            $paymentRecord = $this->paymentRepository->findPaymentByOrderId($orderId);
            if ($paymentRecord !== null) {
                $status = (string) ($paymentRecord['status'] ?? 'unknown');
            }
        }

        unset($_SESSION['mollie_payment_ids'][$orderId]);

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

        $payment = $this->mollieClient->payments->get($providerPaymentId);
        $orderId = (int) ($payment->metadata->order_id ?? 0);
        $status = (string) ($payment->status ?? 'unknown');

        if ($orderId <= 0) {
            return;
        }

        $this->paymentRepository->updatePaymentStatusByOrderId($orderId, $status);

        if ($status === 'paid') {
            $this->paymentRepository->markOrderAsPaid($orderId);
        }
    }

    public function getPaymentStatus(string $providerPaymentId): string
    {
        $payment = $this->mollieClient->payments->get($providerPaymentId);

        return (string) ($payment->status ?? 'unknown');
    }
}
