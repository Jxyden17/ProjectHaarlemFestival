<?php

namespace App\Service;

class PaymentResultViewService
{
    public function buildViewData(array $paymentResult): array
    {
        $status = (string) ($paymentResult['status'] ?? 'unknown');
        $isPaid = (bool) ($paymentResult['isPaid'] ?? false);

        $title = 'Payment Status';
        $message = 'Your payment is currently marked as ' . $status . '.';
        $eyebrow = 'Payment update';

        if ($isPaid) {
            $title = 'Payment Successful';
            $message = 'Your payment has been confirmed successfully.';
            $eyebrow = 'Order confirmed';
        } elseif ($status === 'cancelled') {
            $title = 'Payment Cancelled';
            $message = 'You left the payment page before completing the payment.';
        } elseif ($status === 'processing' || $status === 'pending') {
            $title = 'Payment Pending';
            $message = 'Your payment is still being processed. Please check again shortly.';
            $eyebrow = 'Payment in progress';
        }

        return [
            'title' => $title,
            'message' => $message,
            'eyebrow' => $eyebrow,
        ];
    }
}
