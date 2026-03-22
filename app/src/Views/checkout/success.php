<div class="container py-5">
    <section class="card border-0 shadow-sm">
        <div class="card-body p-5 text-center">
            <h1 class="display-5 mb-3">Order Confirmed</h1>
            <p class="lead mb-3">
                Your order has been created successfully.
            </p>
            <p class="text-muted mb-4">
                Order reference: #<?= (int) ($orderId ?? 0) ?>
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="/" class="btn btn-primary">Back to Events</a>
                <a href="/cart" class="btn btn-outline-secondary">Open New Cart</a>
            </div>
        </div>
    </section>
</div>
