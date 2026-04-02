<?php

namespace App\Service;

use App\Repository\Interfaces\IPaymentRepository;
use App\Service\Interfaces\IPaymentService;

class PaymentService implements IPaymentService
{
    private IPaymentRepository $paymentRepository;
    private string $baseUrl;
    private string $paymentDriver;
    private string $stripeSecretKey;

    public function __construct(
        IPaymentRepository $paymentRepository,
        string $baseUrl,
        string $paymentDriver = 'stripe',
        string $stripeSecretKey = ''
    )
    {
        $this->paymentRepository = $paymentRepository;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->paymentDriver = strtolower(trim($paymentDriver)) !== '' ? strtolower(trim($paymentDriver)) : 'stripe';
        $this->stripeSecretKey = trim($stripeSecretKey);
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
            return $this->createStripeCheckoutSession($orderId);
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

        if ($this->paymentDriver === 'stripe') {
            $session = $this->fetchStripeCheckoutSession($providerPaymentId);
            $status = $this->mapStripeStatus($session);
            $this->paymentRepository->updatePaymentStatusByOrderId($orderId, $status);

            if ($status === 'paid') {
                $this->paymentRepository->markOrderAsPaid($orderId);
            } else {
                $this->paymentRepository->updateOrderStatus($orderId, $status);
            }
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

    private function createStripeCheckoutSession(int $orderId): string
    {
        if ($this->stripeSecretKey === '') {
            throw new \RuntimeException('Stripe secret key is missing. Configure STRIPE_SECRET_KEY in the environment.');
        }

        $order = $this->paymentRepository->findOrderById($orderId);
        if ($order === null) {
            throw new \RuntimeException('Order not found.');
        }

        $totalAmount = (float) ($order['total_amount'] ?? 0);
        $unitAmount = (int) round($totalAmount * 100);

        if ($unitAmount <= 0) {
            throw new \RuntimeException('Order total must be greater than zero.');
        }

        $payload = [
            'mode' => 'payment',
            'success_url' => $this->baseUrl . '/payment/return?order_id=' . $orderId . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $this->baseUrl . '/payment/return?order_id=' . $orderId . '&cancelled=1',
            'client_reference_id' => (string) $orderId,
            'payment_method_types[0]' => 'ideal',
            'payment_method_types[1]' => 'card',
            'line_items[0][price_data][currency]' => 'eur',
            'line_items[0][price_data][product_data][name]' => 'Haarlem Festival order #' . $orderId,
            'line_items[0][price_data][product_data][description]' => 'Checkout payment for your selected festival tickets.',
            'line_items[0][price_data][unit_amount]' => (string) $unitAmount,
            'line_items[0][quantity]' => '1',
            'metadata[order_id]' => (string) $orderId,
        ];

        $response = $this->sendStripeRequest('POST', '/v1/checkout/sessions', $payload);
        $checkoutUrl = trim((string) ($response['url'] ?? ''));
        $sessionId = trim((string) ($response['id'] ?? ''));

        if ($checkoutUrl === '' || $sessionId === '') {
            throw new \RuntimeException('Stripe did not return a checkout URL.');
        }

        $this->paymentRepository->createPaymentRecord($orderId, 'stripe', 'pending', $sessionId);
        $this->paymentRepository->updateOrderStatus($orderId, 'pending');

        return $checkoutUrl;
    }

    private function fetchStripeCheckoutSession(string $sessionId): array
    {
        if ($this->stripeSecretKey === '') {
            throw new \RuntimeException('Stripe secret key is missing. Configure STRIPE_SECRET_KEY in the environment.');
        }

        return $this->sendStripeRequest('GET', '/v1/checkout/sessions/' . rawurlencode($sessionId));
    }

    private function mapStripeStatus(array $session): string
    {
        $paymentStatus = strtolower(trim((string) ($session['payment_status'] ?? '')));
        $sessionStatus = strtolower(trim((string) ($session['status'] ?? '')));

        if ($paymentStatus === 'paid') {
            return 'paid';
        }

        if ($sessionStatus === 'expired') {
            return 'expired';
        }

        if ($sessionStatus === 'complete') {
            return 'processing';
        }

        return 'pending';
    }

    private function sendStripeRequest(string $method, string $path, array $payload = []): array
    {
        $url = 'https://api.stripe.com' . $path;
        $curl = curl_init($url);

        if ($curl === false) {
            throw new \RuntimeException('Unable to initialize Stripe request.');
        }

        $headers = [
            'Authorization: Bearer ' . $this->stripeSecretKey,
        ];

        if ($method === 'POST') {
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payload));
        }

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $rawResponse = curl_exec($curl);
        $httpStatus = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        curl_close($curl);

        if ($rawResponse === false) {
            throw new \RuntimeException('Stripe request failed: ' . $curlError);
        }

        $response = json_decode($rawResponse, true);
        if (!is_array($response)) {
            throw new \RuntimeException('Stripe returned an unreadable response.');
        }

        if ($httpStatus >= 400) {
            $message = (string) ($response['error']['message'] ?? 'Stripe request failed.');
            throw new \RuntimeException($message);
        }

        return $response;
    }
}
