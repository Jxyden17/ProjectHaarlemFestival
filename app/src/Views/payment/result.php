<?php
$paymentResult = is_array($paymentResult ?? null) ? $paymentResult : [];
$paymentView = is_array($paymentView ?? null) ? $paymentView : [];
$status = (string) ($paymentResult['status'] ?? 'unknown');
$orderId = (int) ($paymentResult['orderId'] ?? 0);
$title = (string) ($paymentView['title'] ?? 'Payment Status');
$message = (string) ($paymentView['message'] ?? ('Your payment is currently marked as ' . $status . '.'));
$eyebrow = (string) ($paymentView['eyebrow'] ?? 'Payment update');
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
