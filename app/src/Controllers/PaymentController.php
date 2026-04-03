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
        $isCancelled = isset($_GET['cancelled']) && (string) $_GET['cancelled'] === '1';

        if ($orderId <= 0) {
            http_response_code(400);
            $this->render('shared/error', [
                'errorTitle' => 'Payment unavailable',
                'errorMessage' => 'Missing order identifier.',
            ]);
            return;
        }

        if ($isCancelled) {
            $this->render('payment/result', [
                'title' => 'Payment Cancelled',
                'paymentResult' => [
                    'status' => 'cancelled',
                    'orderId' => $orderId,
                    'isPaid' => false,
                ],
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
        $payload = file_get_contents('php://input');
        $signature = trim((string) ($_SERVER['HTTP_STRIPE_SIGNATURE'] ?? ''));

        try {
            $this->paymentService->handleWebhook((string) $payload, $signature);
            http_response_code(200);
            echo 'OK';
        } catch (\Throwable $e) {
            http_response_code(400);
            echo 'ERROR';
        }
        exit;
    }
}
