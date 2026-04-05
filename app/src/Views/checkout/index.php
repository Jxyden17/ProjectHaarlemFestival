<?php
$groups = is_array($groups ?? null) ? $groups : [];
$subtotal = (float) ($subtotal ?? 0);
?>

<div class="container-fluid px-0 checkout-page">
    <section class="py-4 py-lg-5 cart-hero">
        <div class="container">
            <a href="/cart" class="btn btn-sm mb-3 checkout-back-btn">
                Back to Shopping Cart
            </a>
            <h1 class="display-3 fw-bold mb-2 text-white">Checkout</h1>
            <p class="fs-4 mb-0 text-white cart-page-subtitle">Review your final order before confirming it.</p>
        </div>
    </section>

    <div class="container py-5">
        <div class="row g-4 align-items-start">
            <div class="col-lg-8">
                <section class="card border-0 shadow-sm h-100 checkout-card">
                    <div class="card-body p-4 p-lg-5">
                        <h2 class="display-5 mb-4 checkout-card-title">Order Summary</h2>

                        <?php foreach ($groups as $group): ?>
                            <section class="mb-5">
                                <div class="d-flex justify-content-between align-items-start gap-3 border-bottom pb-3 mb-3 checkout-group-head">
                                    <div>
                                        <h3 class="h2 mb-1 checkout-group-title"><?= htmlspecialchars((string) ($group['title'] ?? 'Unknown Date')) ?></h3>
                                        <div class="small checkout-group-meta">
                                            <?= count($group['items'] ?? []) ?> ticket<?= count($group['items'] ?? []) === 1 ? '' : 's' ?>
                                        </div>
                                    </div>

                                    <div class="h3 mb-0 checkout-accent">&euro;<?= number_format((float) ($group['total'] ?? 0), 2) ?></div>
                                </div>

                                <div class="d-grid gap-3">
                                    <?php foreach (($group['items'] ?? []) as $item): ?>
                                        <?php
                                        $eventName = (string) ($item['event_name'] ?? '');
                                        $eventLabel = match (strtolower(str_replace(' ', '', $eventName))) {
                                            'tellingstory' => 'Stories',
                                            'astrollthroughhistory' => 'Tour',
                                            'dance' => 'Dance',
                                            'jazz' => 'Jazz',
                                            'yummy' => 'Yummy',
                                            default => $eventName !== '' ? $eventName : 'Event',
                                        };

                                        $itemTitle = trim((string) ($item['performer_names'] ?? ''));
                                        if ($itemTitle === '') {
                                            $sessionLabel = trim((string) ($item['label'] ?? ''));
                                            $itemTitle = $sessionLabel !== '' ? $sessionLabel : ('Session #' . (int) ($item['session_id'] ?? 0));
                                        }

                                        $eventSlug = strtolower($eventLabel);
                                        $eventSlug = preg_replace('/[^a-z0-9]+/', '-', $eventSlug) ?: 'default';
                                        $timeLabel = substr((string) ($item['start_time'] ?? ''), 0, 5);
                                        ?>
                                        <div class="border rounded-3 p-3 p-lg-4 checkout-item-card">
                                            <div class="d-flex justify-content-between align-items-start gap-3">
                                                <div>
                                                    <div class="mb-3">
                                                        <span class="badge rounded-pill px-3 py-2 cart-event-pill cart-event-pill--<?= htmlspecialchars($eventSlug) ?>">
                                                            <?= htmlspecialchars($eventLabel) ?>
                                                        </span>
                                                    </div>

                                                    <div class="h3 mb-1 checkout-item-title"><?= htmlspecialchars($itemTitle) ?></div>
                                                    <div class="fs-5 checkout-item-venue"><?= htmlspecialchars((string) ($item['venue_name'] ?? 'Unknown venue')) ?></div>
                                                    <div class="small mt-2 checkout-item-time"><?= htmlspecialchars($timeLabel) ?></div>
                                                </div>

                                                <div class="text-end checkout-item-summary">
                                                    <div class="checkout-item-label">Tickets</div>
                                                    <div class="h4 mb-3 checkout-item-value"><?= (int) ($item['quantity'] ?? 0) ?></div>
                                                    <div class="checkout-item-label">Line Total</div>
                                                    <div class="h3 mb-0 checkout-accent">&euro;<?= number_format((float) ($item['line_total'] ?? 0), 2) ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>

            <div class="col-lg-4">
                <section class="card border-0 shadow-sm checkout-card">
                    <div class="card-body p-4">
                        <h2 class="display-5 mb-4 checkout-card-title">Final Total</h2>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fs-4 checkout-total-label">Subtotal</span>
                            <strong class="fs-3 checkout-accent">&euro;<?= number_format($subtotal, 2) ?></strong>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <span class="badge rounded-pill px-3 py-2 checkout-method checkout-method--ideal">iDEAL</span>
                            <span class="badge rounded-pill px-3 py-2 checkout-method">Visa</span>
                            <span class="badge rounded-pill px-3 py-2 checkout-method">Mastercard</span>
                        </div>

                        <p class="small mb-4 checkout-copy">
                            You will continue to a secure payment page to complete your order with iDEAL or card.
                        </p>

                        <form method="POST" action="/checkout/confirm">
                            <button type="submit" class="btn w-100 py-3 fs-5 checkout-submit-btn">
                                Continue to payment
                            </button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
