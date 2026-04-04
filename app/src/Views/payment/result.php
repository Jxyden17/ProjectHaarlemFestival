<?php
$paymentResult = is_array($paymentResult ?? null) ? $paymentResult : [];
$status = (string) ($paymentResult['status'] ?? 'unknown');
$orderId = (int) ($paymentResult['orderId'] ?? 0);
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
    $eyebrow = 'Payment update';
} elseif ($status === 'processing' || $status === 'pending') {
    $title = 'Payment Pending';
    $message = 'Your payment is still being processed. Please check again shortly.';
    $eyebrow = 'Payment in progress';
}
?>

<div class="container-fluid px-0 checkout-page payment-result-page">
    <div class="container py-5 py-lg-6">
        <section class="card border-0 shadow-sm payment-result-card">
            <div class="card-body p-4 p-lg-5">
                <div class="payment-result-shell">
                    <span class="payment-result-eyebrow"><?= htmlspecialchars($eyebrow) ?></span>
                    <h1 class="display-4 mb-3 payment-result-title"><?= htmlspecialchars($title) ?></h1>
                    <p class="payment-result-order mb-3">Order reference: #<?= $orderId ?></p>
                    <p class="payment-result-copy mb-4"><?= htmlspecialchars($message) ?></p>

                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="/" class="btn px-4 py-2 cart-checkout-btn">Back to Events</a>
                        <a href="/cart" class="btn px-4 py-2 payment-result-secondary-btn">Open Shopping Cart</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
