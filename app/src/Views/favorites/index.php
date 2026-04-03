<?php
$favorites = is_array($favorites ?? null) ? $favorites : [];
?>

<div class="container-fluid px-0 cart-page favorites-page">
    <section class="py-5 cart-hero">
        <div class="container">
            <a href="/" class="btn btn-sm mb-3 cart-back-btn">
                Back to Home
            </a>
            <h1 class="display-3 fw-bold mb-2 cart-page-title">Favorites</h1>
            <p class="fs-3 mb-0 text-white cart-page-subtitle">Keep the sessions you want to come back to in one place.</p>
        </div>
    </section>

    <div class="container py-5">
        <?php if ($favorites === []): ?>
            <section class="mx-auto cart-empty-shell">
                <div class="card border-0 shadow-sm cart-empty-card">
                    <div class="card-body text-center py-5 px-4 px-lg-5">
                        <span class="cart-empty-eyebrow">Favorites</span>
                        <h2 class="display-5 mb-3 cart-empty-title">Your favorites list is empty</h2>
                        <p class="cart-empty-copy mb-4">Save sessions you like and come back later when you are ready to book them.</p>
                        <a href="/#schedule" class="btn px-4 py-2 cart-checkout-btn">Explore Schedule</a>
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
                                    <i data-lucide="heart"></i>
                                </span>
                                <h2 class="h2 mb-0 text-white">Your Favorites</h2>
                            </div>
                            <p class="mb-0 cart-heading-copy">Sessions you saved for later are listed here.</p>
                        </div>
                    </div>

                    <div class="d-none d-lg-grid px-3 py-2 mb-2 cart-table-head">
                        <div>Event</div>
                        <div>Time</div>
                        <div>Location</div>
                        <div>Name</div>
                        <div>Status</div>
                        <div>Price</div>
                        <div class="text-end">Action</div>
                    </div>

                    <?php foreach ($favorites as $item): ?>
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
                            $itemTitle = 'Session #' . (int) ($item['session_id'] ?? 0);
                        }

                        $venueName = (string) ($item['venue_name'] ?? 'Unknown venue');
                        $timeLabel = substr((string) ($item['start_time'] ?? ''), 0, 5);
                        $languageLabel = trim((string) ($item['label'] ?? ''));
                        $remainingSpots = max(0, (int) (($item['available_spots'] ?? 0) - ($item['amount_sold'] ?? 0)));
                        $pricingType = (string) ($item['pricing_type'] ?? 'fixed');
                        $priceValue = $pricingType === 'pay_as_you_like'
                            ? (float) ($item['minimum_price'] ?? 0)
                            : (float) ($item['price'] ?? 0);
                        ?>
                        <div class="px-3 py-3 border-top cart-row-shell">
                            <div class="d-grid align-items-center gap-3 cart-row-grid">
                                <div>
                                    <span class="badge rounded-pill px-3 py-2 cart-event-pill cart-event-pill--<?= htmlspecialchars($eventSlug) ?>">
                                        <?= htmlspecialchars($eventLabel) ?>
                                    </span>
                                </div>

                                <div class="small text-white cart-time-cell">
                                    <div class="fw-semibold"><?= htmlspecialchars($timeLabel !== '' ? $timeLabel : 'TBA') ?></div>
                                    <div class="cart-time-note">Starts</div>
                                </div>

                                <div class="small text-white">
                                    <div class="fw-semibold"><?= htmlspecialchars($venueName) ?></div>
                                    <?php if ($languageLabel !== ''): ?>
                                        <div class="cart-secondary-copy"><?= htmlspecialchars($languageLabel) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="small text-white">
                                    <div class="fw-semibold"><?= htmlspecialchars($itemTitle) ?></div>
                                </div>

                                <div class="small text-white">
                                    <div class="fw-semibold"><?= $remainingSpots > 0 ? 'Available' : 'Sold out' ?></div>
                                    <div class="cart-secondary-copy"><?= $remainingSpots ?> spots left</div>
                                </div>

                                <div class="small fw-semibold cart-price-cell">
                                    <?php if ($pricingType === 'pay_as_you_like'): ?>
                                        <div>Pay as you like</div>
                                        <div class="cart-secondary-copy">From &euro;<?= number_format($priceValue, 2) ?></div>
                                    <?php else: ?>
                                        <div>&euro;<?= number_format($priceValue, 2) ?></div>
                                        <div class="cart-secondary-copy">Per ticket</div>
                                    <?php endif; ?>
                                </div>

                                <div class="text-end d-flex flex-wrap justify-content-end gap-2">
                                    <a href="/book/<?= (int) ($item['session_id'] ?? 0) ?>" class="btn btn-sm px-3 cart-checkout-btn">
                                        Book
                                    </a>
                                    <form method="POST" action="/favorites/remove" class="m-0">
                                        <input type="hidden" name="session_id" value="<?= (int) ($item['session_id'] ?? 0) ?>">
                                        <input type="hidden" name="redirect_to" value="/favorites">
                                        <button type="submit" class="btn btn-sm px-3 cart-delete-btn">Remove</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</div>
