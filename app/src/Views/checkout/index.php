<div class="container py-5">
    <section class="mb-5">
        <a href="/cart" class="btn btn-outline-light btn-sm mb-3">Back to Personal Program</a>
        <h1 class="display-4 mb-2">Checkout</h1>
        <p class="lead mb-0">Review your final order before confirming it.</p>
    </section>

    <div class="row g-4">
        <div class="col-lg-8">
            <section class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 p-lg-5">
                    <h2 class="h2 mb-4">Order Summary</h2>

                    <?php foreach ($groups as $group): ?>
                        <section class="mb-4">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                <div>
                                    <h3 class="h5 mb-1"><?= htmlspecialchars((string) ($group['title'] ?? 'Unknown Date')) ?></h3>
                                    <div class="small text-muted">
                                        <?= count($group['items'] ?? []) ?> ticket<?= count($group['items'] ?? []) === 1 ? '' : 's' ?>
                                    </div>
                                </div>

                                <div class="fw-bold">
                                    &euro;<?= number_format((float) ($group['total'] ?? 0), 2) ?>
                                </div>
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
                                        default => $eventName !== '' ? $eventName : 'Event',
                                    };

                                    $itemTitle = trim((string) ($item['performer_names'] ?? ''));
                                    if ($itemTitle === '') {
                                        $itemTitle = 'Session #' . (int) ($item['session_id'] ?? 0);
                                    }

                                    $timeLabel = substr((string) ($item['start_time'] ?? ''), 0, 5);
                                    $languageLabel = trim((string) ($item['label'] ?? ''));
                                    ?>
                                    <div class="border rounded p-3">
                                        <div class="d-flex justify-content-between align-items-start gap-3">
                                            <div>
                                                <div class="mb-2">
                                                    <span class="badge rounded-pill text-bg-secondary">
                                                        <?= htmlspecialchars($eventLabel) ?>
                                                    </span>
                                                </div>
                                                <div class="fw-semibold"><?= htmlspecialchars($itemTitle) ?></div>
                                                <div class="small text-muted">
                                                    <?= htmlspecialchars((string) ($item['venue_name'] ?? 'Unknown venue')) ?>
                                                </div>
                                                <div class="small text-muted">
                                                    <?= htmlspecialchars($timeLabel) ?>
                                                    <?php if ($languageLabel !== ''): ?>
                                                        · <?= htmlspecialchars($languageLabel) ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="text-end">
                                                <div class="small text-muted">Tickets</div>
                                                <div class="fw-semibold"><?= (int) ($item['quantity'] ?? 0) ?></div>
                                                <div class="small text-muted mt-2">Line Total</div>
                                                <div class="fw-bold">&euro;<?= number_format((float) ($item['line_total'] ?? 0), 2) ?></div>
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
            <section class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h3 mb-4">Final Total</h2>

                    <div class="d-flex justify-content-between mb-3">
                        <span>Subtotal</span>
                        <strong>&euro;<?= number_format((float) $subtotal, 2) ?></strong>
                    </div>

                    <p class="text-muted mb-4">
                        This confirms your order and continues to the Mollie iDEAL payment page.
                    </p>

                    <form method="POST" action="/checkout/confirm">
                        <button type="submit" class="btn btn-primary w-100">
                            Continue to iDEAL
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>
