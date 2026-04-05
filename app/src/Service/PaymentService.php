<?php

namespace App\Service;

use App\Repository\Interfaces\IPaymentRepository;
use App\Service\Interfaces\ITicketService;
use App\Service\Interfaces\IPaymentService;

class PaymentService implements IPaymentService
{
    private const WEBHOOK_TOLERANCE_IN_SECONDS = 300;

    private IPaymentRepository $paymentRepository;
    private string $baseUrl;
    private string $paymentDriver;
    private string $stripeSecretKey;
    private string $stripeWebhookSecret;
    private ITicketService $ticketService;

    public function __construct(
        IPaymentRepository $paymentRepository,
        ITicketService $ticketService,
        string $baseUrl,
        string $paymentDriver = 'stripe',
        string $stripeSecretKey = '',
        string $stripeWebhookSecret = ''
    )
    {
        $this->paymentRepository = $paymentRepository;
        $this->ticketService = $ticketService;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->paymentDriver = strtolower(trim($paymentDriver)) !== '' ? strtolower(trim($paymentDriver)) : 'stripe';
        $this->stripeSecretKey = trim($stripeSecretKey);
        $this->stripeWebhookSecret = trim($stripeWebhookSecret);
    }

    public function createPayment(int $orderId, int $cartId): string
    {
        if ($orderId <= 0) {
            throw new \RuntimeException('Invalid order.');
        }

        if ($cartId <= 0) {
            throw new \RuntimeException('Invalid cart.');
        }

        if (trim($this->baseUrl) === '') {
            throw new \RuntimeException('Application base URL is missing.');
        }

        if ($this->paymentDriver === 'stripe') {
            return $this->createStripeCheckoutSession($orderId, $cartId);
        }

        throw new \RuntimeException('Unsupported payment driver: ' . $this->paymentDriver);
    }

    public function handleReturn(int $orderId, string $sessionId = ''): array
    {
        if ($orderId <= 0) {
            throw new \RuntimeException('Missing order identifier.');
        }

        $paymentRecord = $this->paymentRepository->findPaymentByOrderId($orderId);
        if ($paymentRecord === null) {
            throw new \RuntimeException('Payment record not found for this order.');
        }

        $providerPaymentId = trim((string) ($paymentRecord['provider_payment_id'] ?? ''));
        $resolvedSessionId = trim($sessionId) !== '' ? trim($sessionId) : $providerPaymentId;

        if ($resolvedSessionId === '') {
            throw new \RuntimeException('Payment provider reference is missing for this order.');
        }

        $status = strtolower(trim((string) ($paymentRecord['status'] ?? 'unknown')));

        if ($this->paymentDriver === 'stripe' && $this->stripeSecretKey !== '') {
            $session = $this->fetchStripeCheckoutSession($resolvedSessionId);
            $liveStatus = $this->mapStripeStatus($session);

            if ($liveStatus === 'paid' && $status !== 'paid') {
                $cartId = (int) ($paymentRecord['cart_id'] ?? 0);
                $this->ticketService->fulfillPaidOrder($orderId, $cartId);
                $status = 'paid';
            } elseif ($liveStatus !== '' && $liveStatus !== $status && $status !== 'paid') {
                $this->paymentRepository->updatePaymentStatusByOrderId($orderId, $liveStatus);
                $this->paymentRepository->updateOrderStatus($orderId, $liveStatus);
                $status = $liveStatus;
            }
        }

        return [
            'status' => $status,
            'orderId' => $orderId,
            'isPaid' => $status === 'paid',
        ];
    }

    public function handleCancellation(int $orderId): array
    {
        if ($orderId <= 0) {
            throw new \RuntimeException('Missing order identifier.');
        }

        $paymentRecord = $this->paymentRepository->findPaymentByOrderId($orderId);
        $currentStatus = strtolower(trim((string) ($paymentRecord['status'] ?? '')));

        if ($currentStatus === 'paid') {
            return [
                'status' => 'paid',
                'orderId' => $orderId,
                'isPaid' => true,
            ];
        }

        if ($paymentRecord !== null) {
            $this->paymentRepository->updatePaymentStatusByOrderId($orderId, 'cancelled');
        }

        $this->paymentRepository->updateOrderStatus($orderId, 'cancelled');

        return [
            'status' => 'cancelled',
            'orderId' => $orderId,
            'isPaid' => false,
        ];
    }

    public function handleWebhook(string $payload, string $signature = ''): void
    {
        if (trim($payload) === '') {
            return;
        }

        if ($this->paymentDriver === 'stripe') {
            $this->processStripeWebhook($payload, $signature);
            return;
        }
    }

    public function getPaymentStatus(string $providerPaymentId): string
    {
        $paymentRecord = $this->paymentRepository->findPaymentByProviderPaymentId($providerPaymentId);
        if ($paymentRecord === null) {
            throw new \RuntimeException('Payment record not found.');
        }

        return (string) ($paymentRecord['status'] ?? 'unknown');
    }

    private function createStripeCheckoutSession(int $orderId, int $cartId): string
    {
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
            'line_items[0][price_data][product_data][name]' => 'Haarlem Festival tickets',
            'line_items[0][price_data][product_data][description]' => 'Order #' . $orderId,
            'line_items[0][price_data][unit_amount]' => (string) $unitAmount,
            'line_items[0][quantity]' => '1',
            'metadata[order_id]' => (string) $orderId,
            'metadata[cart_id]' => (string) $cartId,
        ];

        $response = $this->sendStripeRequest('POST', '/v1/checkout/sessions', $payload);
        $checkoutUrl = trim((string) ($response['url'] ?? ''));
        $sessionId = trim((string) ($response['id'] ?? ''));

        if ($checkoutUrl === '' || $sessionId === '') {
            throw new \RuntimeException('Stripe did not return a checkout URL.');
        }

        $this->paymentRepository->createPaymentRecord($orderId, $cartId, 'stripe', 'pending', $sessionId);
        $this->paymentRepository->updateOrderStatus($orderId, 'pending');

        return $checkoutUrl;
    }

    private function fetchStripeCheckoutSession(string $sessionId): array
    {
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

    private function processStripeWebhook(string $payload, string $signature): void
    {
        if ($this->stripeWebhookSecret === '') {
            throw new \RuntimeException('Stripe webhook secret is missing. Configure STRIPE_WEBHOOK_SECRET in the environment.');
        }

        $this->assertValidStripeSignature($payload, $signature);

        $event = json_decode($payload, true);
        if (!is_array($event)) {
            throw new \RuntimeException('Stripe webhook payload could not be decoded.');
        }

        $eventType = (string) ($event['type'] ?? '');
        $session = $event['data']['object'] ?? null;

        if (!is_array($session)) {
            return;
        }

        $sessionId = trim((string) ($session['id'] ?? ''));
        if ($sessionId === '') {
            return;
        }

        $paymentRecord = $this->paymentRepository->findPaymentByProviderPaymentId($sessionId);
        if ($paymentRecord === null) {
            return;
        }

        $orderId = (int) ($paymentRecord['order_id'] ?? 0);
        if ($orderId <= 0) {
            return;
        }

        $status = match ($eventType) {
            'checkout.session.completed' => $this->mapStripeStatus($session),
            'checkout.session.async_payment_succeeded' => 'paid',
            'checkout.session.async_payment_failed' => 'failed',
            'checkout.session.expired' => 'expired',
            default => '',
        };

        if ($status === '') {
            return;
        }

        if ($status === 'paid') {
            $cartId = (int) ($paymentRecord['cart_id'] ?? 0);
            $this->ticketService->fulfillPaidOrder($orderId, $cartId);
            return;
        }

        $this->paymentRepository->updatePaymentStatusByOrderId($orderId, $status);
        $this->paymentRepository->updateOrderStatus($orderId, $status);
    }

    private function assertValidStripeSignature(string $payload, string $signatureHeader): void
    {
        if ($signatureHeader === '') {
            throw new \RuntimeException('Missing Stripe signature header.');
        }

        $parts = [];
        foreach (explode(',', $signatureHeader) as $chunk) {
            [$key, $value] = array_pad(explode('=', trim($chunk), 2), 2, '');
            if ($key !== '' && $value !== '') {
                $parts[$key][] = $value;
            }
        }

        $timestamp = isset($parts['t'][0]) ? (int) $parts['t'][0] : 0;
        $signatures = $parts['v1'] ?? [];

        if ($timestamp <= 0 || $signatures === []) {
            throw new \RuntimeException('Invalid Stripe signature header.');
        }

        if (abs(time() - $timestamp) > self::WEBHOOK_TOLERANCE_IN_SECONDS) {
            throw new \RuntimeException('Stripe webhook timestamp is outside the allowed tolerance.');
        }

        $signedPayload = $timestamp . '.' . $payload;
        $expectedSignature = hash_hmac('sha256', $signedPayload, $this->stripeWebhookSecret);

        foreach ($signatures as $candidate) {
            if (hash_equals($expectedSignature, $candidate)) {
                return;
            }
        }

        throw new \RuntimeException('Stripe webhook signature verification failed.');
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
