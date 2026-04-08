<?php

namespace App\Controllers;

use App\Service\PaymentResultViewService;
use App\Service\Interfaces\IPaymentService;

class PaymentController extends BaseController
{
    public function __construct(
        private IPaymentService $paymentService,
        private PaymentResultViewService $paymentResultViewService
    )
    {
    }

    public function return(): void
    {
        $orderId = (int) ($_GET['order_id'] ?? 0);
        $sessionId = trim((string) ($_GET['session_id'] ?? ''));
        $isCancelled = isset($_GET['cancelled']) && (string) $_GET['cancelled'] === '1';

        if ($orderId <= 0) {
            $this->renderPaymentError('Missing order identifier.');
            return;
        }

        if ($isCancelled) {
            try {
                $result = $this->paymentService->handleCancellation($orderId);
            } catch (\Throwable $e) {
                $this->renderPaymentError($e->getMessage());
                return;
            }

            $this->renderPaymentResult($result);
            return;
        }

        try {
            $result = $this->paymentService->handleReturn($orderId, $sessionId);
        } catch (\Throwable $e) {
            error_log('Payment return failed for order #' . $orderId . ': ' . $e->getMessage());
            $this->renderPaymentError($e->getMessage());
            return;
        }

        $this->renderPaymentResult($result);
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
            error_log('Payment webhook failed: ' . $e->getMessage());
            http_response_code(400);
            echo 'ERROR';
        }
        exit;
    }

    private function renderPaymentError(string $message): void
    {
        http_response_code(400);
        $this->render('shared/error', [
            'errorTitle' => 'Payment unavailable',
            'errorMessage' => $message,
        ]);
    }

    private function renderPaymentResult(array $result): void
    {
        $viewData = $this->paymentResultViewService->buildViewData($result);

        $this->render('payment/result', [
            'title' => (string) ($viewData['title'] ?? 'Payment Result'),
            'paymentResult' => $result,
            'paymentView' => $viewData,
        ]);
    }
}
