<?php
$order = is_array($order ?? null) ? $order : [];
$payment = is_array($payment ?? null) ? $payment : [];
$orderId = (int) ($order['id'] ?? 0);
$totalAmount = (float) ($order['total_amount'] ?? 0);
$paymentStatus = (string) ($payment['status'] ?? 'open');
?>

<div class="container py-5">
    <section class="mb-5">
        <a href="/checkout" class="btn btn-outline-light btn-sm mb-3">Back to Checkout</a>
        <h1 class="display-4 mb-2">Mock Payment</h1>
        <p class="lead mb-0">Use this local payment simulator for your exam demo.</p>
    </section>

    <div class="row g-4">
        <div class="col-lg-7">
            <section class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 p-lg-5">
                    <h2 class="h3 mb-4">Payment Summary</h2>

                    <div class="d-flex justify-content-between mb-3">
                        <span>Order</span>
                        <strong>#<?= $orderId ?></strong>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span>Method</span>
                        <strong><?= htmlspecialchars((string) ($payment['method'] ?? 'mock')) ?></strong>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span>Current Status</span>
                        <strong><?= htmlspecialchars(ucfirst($paymentStatus)) ?></strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Total</span>
                        <strong>&euro;<?= number_format($totalAmount, 2) ?></strong>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-lg-5">
            <section class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h3 mb-4">Simulate Result</h2>
                    <p class="text-muted mb-4">
                        Choose the outcome you want to demonstrate. This updates the local order and payment status without calling Mollie.
                    </p>

                    <div class="d-grid gap-3">
                        <form method="POST" action="/payment/mock/<?= $orderId ?>">
                            <input type="hidden" name="status" value="paid">
                            <button type="submit" class="btn btn-success w-100">Mark as Paid</button>
                        </form>

                        <form method="POST" action="/payment/mock/<?= $orderId ?>">
                            <input type="hidden" name="status" value="failed">
                            <button type="submit" class="btn btn-danger w-100">Mark as Failed</button>
                        </form>

                        <form method="POST" action="/payment/mock/<?= $orderId ?>">
                            <input type="hidden" name="status" value="canceled">
                            <button type="submit" class="btn btn-outline-secondary w-100">Mark as Canceled</button>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
