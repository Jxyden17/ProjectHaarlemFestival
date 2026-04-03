<?php
$paymentResult = is_array($paymentResult ?? null) ? $paymentResult : [];
$status = (string) ($paymentResult['status'] ?? 'unknown');
$orderId = (int) ($paymentResult['orderId'] ?? 0);
$isPaid = (bool) ($paymentResult['isPaid'] ?? false);
?>

<div class="container py-5">
    <section class="card border-0 shadow-sm">
        <div class="card-body p-5 text-center">
            <h1 class="display-5 mb-3">
                <?php if ($isPaid): ?>
                    Payment Successful
                <?php elseif ($status === 'cancelled'): ?>
                    Payment Cancelled
                <?php elseif ($status === 'processing' || $status === 'pending'): ?>
                    Payment Pending
                <?php else: ?>
                    Payment Status: <?= htmlspecialchars(ucfirst($status)) ?>
                <?php endif; ?>
            </h1>

            <p class="lead mb-3">
                Order reference: #<?= $orderId ?>
            </p>

            <?php if ($isPaid): ?>
                <p class="text-muted mb-4">Your payment has been confirmed successfully.</p>
            <?php elseif ($status === 'cancelled'): ?>
                <p class="text-muted mb-4">You left the payment page before completing the payment.</p>
            <?php elseif ($status === 'processing' || $status === 'pending'): ?>
                <p class="text-muted mb-4">Your payment is still being processed. Please check again shortly.</p>
            <?php else: ?>
                <p class="text-muted mb-4">Your payment is currently marked as <?= htmlspecialchars($status) ?>.</p>
            <?php endif; ?>

            <div class="d-flex justify-content-center gap-3">
                <a href="/" class="btn btn-primary">Back to Events</a>
                <a href="/cart" class="btn btn-outline-secondary">Open Shopping Cart</a>
            </div>
        </div>
    </section>
</div>
