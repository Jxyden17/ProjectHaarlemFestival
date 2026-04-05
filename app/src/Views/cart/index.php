<?php
$items = is_array($items ?? null) ? $items : [];
$groups = is_array($groups ?? null) ? $groups : [];
$subtotal = (float) ($subtotal ?? 0);
?>

<div class="container-fluid px-0 cart-page">
    <section class="py-5 cart-hero">
        <div class="container">
            <a href="/" class="btn btn-sm mb-3 cart-back-btn">
                Back to Home
            </a>
            <h1 class="display-3 fw-bold mb-2 cart-page-title">Shopping Cart</h1>
            <p class="fs-3 mb-0 text-white cart-page-subtitle">All selected tickets are collected here before checkout.</p>
        </div>
    </section>

    <div class="container py-5">
        <?php if ($items === []): ?>
            <section class="mx-auto cart-empty-shell">
                <div class="card border-0 shadow-sm cart-empty-card">
                    <div class="card-body text-center py-5 px-4 px-lg-5">
                        <span class="cart-empty-eyebrow">Shopping Cart</span>
                        <h2 class="display-5 mb-3 cart-empty-title">Your shopping cart is empty</h2>
                        <p class="cart-empty-copy mb-4">You have not added any tickets yet. Explore the festival and come back once you are ready to book.</p>
                        <a href="/" class="btn px-4 py-2 cart-checkout-btn">View Events</a>
                    </div>
                </div>
            </section>
        <?php else: ?>
            <section class="mx-auto cart-shell">
                <div class="rounded-5 p-3 p-lg-4 shadow-sm cart-card">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle cart-heading-icon">
                                    <span aria-hidden="true">□</span>
                                </span>
                                <h2 class="h2 mb-0 text-white">Your Shopping Cart</h2>
                            </div>
                            <p class="mb-0 cart-heading-copy">Review all selected tickets before continuing to payment.</p>
                        </div>
                    </div>

                    <div class="d-none d-lg-grid px-3 py-2 mb-2 cart-table-head">
                        <div>Event</div>
                        <div>Time</div>
                        <div>Location</div>
                        <div>Name</div>
                        <div>Tickets</div>
                        <div>Price for ticket</div>
                        <div class="text-end">Action</div>
                    </div>

                    <?php foreach ($groups as $group): ?>
                        <section class="mb-4">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 px-3 py-3 rounded-4 cart-group-head">
                                <div>
                                    <h3 class="h5 mb-1 cart-group-title"><?= htmlspecialchars((string) ($group['title'] ?? 'Unknown Date')) ?></h3>
                                    <div class="small cart-group-meta"><?= count($group['items'] ?? []) ?> events selected</div>
                                </div>

                                <div class="fw-semibold cart-group-total">&euro;<?= number_format((float) ($group['total'] ?? 0), 2) ?></div>
                            </div>

                            <div class="cart-group-body">
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

                                    $eventColors = match (strtolower($eventLabel)) {
                                        'stories' => ['bg' => '#e8d8ff', 'text' => '#5d3c88'],
                                        'tour' => ['bg' => '#f5e4bf', 'text' => '#8a5b12'],
                                        'dance' => ['bg' => '#9ee9ff', 'text' => '#125778'],
                                        'jazz' => ['bg' => '#f7d15d', 'text' => '#624400'],
                                        'yummy' => ['bg' => '#ff8f8f', 'text' => '#7a1616'],
                                        default => ['bg' => '#d8e4ff', 'text' => '#24446e'],
                                    };
                                    $eventSlug = strtolower($eventLabel);
                                    $eventSlug = preg_replace('/[^a-z0-9]+/', '-', $eventSlug) ?: 'default';

                                    $itemTitle = trim((string) ($item['performer_names'] ?? ''));
                                    if ($itemTitle === '') {
                                        $sessionLabel = trim((string) ($item['label'] ?? ''));
                                        $itemTitle = $sessionLabel !== '' ? $sessionLabel : ('Session #' . (int) ($item['session_id'] ?? 0));
                                    }

                                    $venueName = (string) ($item['venue_name'] ?? 'Unknown venue');
                                    $timeLabel = substr((string) ($item['start_time'] ?? ''), 0, 5);
                                    $languageLabel = trim((string) ($item['label'] ?? ''));
                                    $isAgeLabel = preg_match('/^\+?\d{1,2}$/', $languageLabel) === 1;
                                    $detailBadge = $isAgeLabel ? $languageLabel : '';
                                    $secondaryLabel = !$isAgeLabel ? $languageLabel : '';
                                    $priceValue = (float) ($item['unit_price'] ?? 0);
                                    $remainingSpots = max(0, (int) ($item['remaining_spots'] ?? 0));
                                    $currentQuantity = (int) ($item['quantity'] ?? 0);
                                    $canIncrease = $currentQuantity < $remainingSpots;
                                    ?>
                                    <div class="px-3 py-3 border-top cart-row-shell">
                                        <div class="d-grid align-items-center gap-3 cart-row-grid">
                                            <div>
                                                <span class="badge rounded-pill px-3 py-2 cart-event-pill cart-event-pill--<?= htmlspecialchars($eventSlug) ?>">
                                                    <?= htmlspecialchars($eventLabel) ?>
                                                </span>
                                            </div>

                                            <div class="small text-white cart-time-cell">
                                                <div class="fw-semibold"><?= htmlspecialchars($timeLabel) ?></div>
                                                <div class="cart-time-note">Starts</div>
                                            </div>

                                            <div class="small text-white">
                                                <div class="fw-semibold"><?= htmlspecialchars($venueName) ?></div>
                                                <?php if ($secondaryLabel !== ''): ?>
                                                    <div class="cart-secondary-copy"><?= htmlspecialchars($secondaryLabel) ?></div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="small text-white">
                                                <div class="fw-semibold"><?= htmlspecialchars($itemTitle) ?></div>
                                                <?php if ($detailBadge !== ''): ?>
                                                    <div class="mt-2">
                                                        <span class="badge rounded-pill px-2 py-1 cart-detail-pill">
                                                            <?= htmlspecialchars($detailBadge) ?>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <div>
                                                <div class="d-inline-flex align-items-center gap-2 rounded-pill px-2 py-1 cart-qty-shell">
                                                    <form method="POST" action="/cart/update" class="m-0">
                                                        <input type="hidden" name="cart_item_id" value="<?= (int) $item['id'] ?>">
                                                        <input type="hidden" name="quantity" value="<?= max(0, $currentQuantity - 1) ?>">
                                                        <button type="submit" class="btn btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center cart-qty-btn">−</button>
                                                    </form>

                                                    <span class="badge rounded-pill px-3 py-2 cart-qty-value">
                                                        <?= $currentQuantity ?>
                                                    </span>

                                                    <form method="POST" action="/cart/update" class="m-0">
                                                        <input type="hidden" name="cart_item_id" value="<?= (int) $item['id'] ?>">
                                                        <input type="hidden" name="quantity" value="<?= $currentQuantity + 1 ?>">
                                                        <button type="submit" class="btn btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center cart-qty-btn<?= $canIncrease ? '' : ' cart-qty-btn--disabled' ?>" <?= $canIncrease ? '' : 'disabled' ?>>+</button>
                                                    </form>
                                                </div>
                                            </div>

                                            <div class="small fw-semibold cart-price-cell">
                                                <?php if ($priceValue <= 0): ?>
                                                    <div>Pay as you like</div>
                                                <?php else: ?>
                                                    <div>&euro;<?= number_format($priceValue, 2) ?></div>
                                                <?php endif; ?>
                                                <div class="cart-secondary-copy">Line total: &euro;<?= number_format((float) ($item['line_total'] ?? 0), 2) ?></div>
                                            </div>

                                            <div class="text-end">
                                                <form method="POST" action="/cart/remove" class="m-0">
                                                    <input type="hidden" name="cart_item_id" value="<?= (int) $item['id'] ?>">
                                                    <button type="submit" class="btn btn-sm px-3 cart-delete-btn">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endforeach; ?>

                    <div class="pt-3">
                        <div class="row align-items-center g-4">
                            <div class="col-lg-6">
                                <div class="rounded-3 px-3 py-3 d-flex align-items-center gap-2 cart-confirm-box">
                                    <input class="form-check-input mt-0" type="checkbox" value="" id="program-confirm" disabled checked>
                                    <label class="form-check-label small" for="program-confirm">
                                        I confirm: all participants are 12+ years old and no strollers.
                                    </label>
                                </div>
                            </div>

                            <div class="col-lg-6 text-lg-end">
                                <div class="mb-3">
                                    <div class="small text-uppercase cart-total-label">Total</div>
                                    <div class="display-6 fw-bold mb-0 cart-total-value">&euro;<?= number_format($subtotal, 2) ?></div>
                                </div>

                                <a href="/checkout" class="btn px-5 py-2 cart-checkout-btn">
                                    Buy Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>
</div>
