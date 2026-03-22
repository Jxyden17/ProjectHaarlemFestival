<?php

namespace App\Controllers;

use App\Service\Interfaces\IPaymentService;

class PaymentController extends BaseController
{
    private IPaymentService $paymentService;

    public function __construct(IPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function return(): void
    {
        $orderId = (int) ($_GET['order_id'] ?? 0);

        if ($orderId <= 0) {
            http_response_code(400);
            $this->render('shared/error', [
                'errorTitle' => 'Payment unavailable',
                'errorMessage' => 'Missing order identifier.',
            ]);
            return;
        }

        try {
            $result = $this->paymentService->handleReturn($orderId);
        } catch (\Throwable $e) {
            http_response_code(400);
            $this->render('shared/error', [
                'errorTitle' => 'Payment unavailable',
                'errorMessage' => $e->getMessage(),
            ]);
            return;
        }

        $this->render('payment/result', [
            'title' => 'Payment Result',
            'paymentResult' => $result,
        ]);
    }

    public function webhook(): void
    {
        $paymentId = trim((string) ($_POST['id'] ?? ''));

        try {
            $this->paymentService->handleWebhook($paymentId);
            http_response_code(200);
            echo 'OK';
        } catch (\Throwable $e) {
            http_response_code(400);
            echo 'ERROR';
        }
        exit;
    }

    public function mock(array $vars): void
    {
        $orderId = (int) ($vars['orderId'] ?? 0);

        try {
            $paymentData = $this->paymentService->getMockPaymentData($orderId);
        } catch (\Throwable $e) {
            http_response_code(400);
            $this->render('shared/error', [
                'errorTitle' => 'Mock payment unavailable',
                'errorMessage' => $e->getMessage(),
            ]);
            return;
        }

        $this->render('payment/mock', [
            'title' => 'Mock Payment',
            'order' => $paymentData['order'],
            'payment' => $paymentData['payment'],
        ]);
    }

    public function mockComplete(array $vars): void
    {
        $orderId = (int) ($vars['orderId'] ?? 0);
        $status = strtolower(trim((string) ($_POST['status'] ?? '')));

        try {
            $result = $this->paymentService->completeMockPayment($orderId, $status);
        } catch (\Throwable $e) {
            http_response_code(400);
            $this->render('shared/error', [
                'errorTitle' => 'Mock payment unavailable',
                'errorMessage' => $e->getMessage(),
            ]);
            return;
        }

        $this->render('payment/result', [
            'title' => 'Payment Result',
            'paymentResult' => $result,
        ]);
    }
}
