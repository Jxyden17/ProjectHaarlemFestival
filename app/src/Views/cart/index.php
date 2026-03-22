<?php
$items = is_array($items ?? null) ? $items : [];
$groups = is_array($groups ?? null) ? $groups : [];
$subtotal = (float) ($subtotal ?? 0);
?>

<div class="container-fluid px-0">
    <section class="py-5" style="background: linear-gradient(rgba(7, 16, 38, 0.7), rgba(7, 16, 38, 0.88)), url('/img/home/home-banner.png') center/cover no-repeat;">
        <div class="container">
            <a href="/" class="btn btn-sm mb-3" style="background-color: #d6a436; color: #11233d; border-radius: 999px; padding-inline: 1rem; font-weight: 600;">
                Back to Home
            </a>
            <h1 class="display-3 fw-bold mb-2" style="color: #d6a436;">Personal Program</h1>
            <p class="fs-3 mb-0 text-white">All tickets that you are interested in are here</p>
        </div>
    </section>

    <div class="container py-5">
        <?php if ($items === []): ?>
            <section class="mx-auto" style="max-width: 980px;">
                <div class="card border-0 shadow-sm" style="border-radius: 1.5rem;">
                    <div class="card-body text-center py-5 px-4">
                        <h2 class="display-6 mb-3">Your personal program is empty</h2>
                        <p class="lead text-muted mb-4">You have not added any tickets yet.</p>
                        <a href="/" class="btn btn-primary px-4">View Events</a>
                    </div>
                </div>
            </section>
        <?php else: ?>
            <section class="mx-auto" style="max-width: 1180px;">
                <div class="rounded-5 p-3 p-lg-4 shadow-sm" style="background-color: #132746; border: 2px solid #d6a436;">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 2rem; height: 2rem; background-color: rgba(214, 164, 54, 0.15); color: #d6a436; border: 1px solid #d6a436;">
                                    <span aria-hidden="true">□</span>
                                </span>
                                <h2 class="h2 mb-0 text-white">Your Personal Schedule</h2>
                            </div>
                            <p class="mb-0" style="color: rgba(255, 255, 255, 0.7);">Review all selected tickets before continuing to payment.</p>
                        </div>

                        <div class="text-lg-end">
                            <div class="small text-uppercase" style="color: rgba(255, 255, 255, 0.6); letter-spacing: 0.08em;">Total</div>
                            <div class="fs-3 fw-bold" style="color: #d6a436;">&euro;<?= number_format($subtotal, 2) ?></div>
                        </div>
                    </div>

                    <div class="d-none d-lg-grid px-3 py-2 mb-2" style="grid-template-columns: 140px 120px 1.4fr 1.5fr 150px 170px 100px; color: rgba(255, 255, 255, 0.75); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em;">
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
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 px-3 py-3" style="background: linear-gradient(90deg, rgba(167, 126, 34, 0.92), rgba(214, 164, 54, 0.98)); color: #10213c;">
                                <div>
                                    <h3 class="h5 mb-1"><?= htmlspecialchars((string) ($group['title'] ?? 'Unknown Date')) ?></h3>
                                    <div class="small"><?= count($group['items'] ?? []) ?> events scheduled</div>
                                </div>

                                <div class="fw-semibold">Total: &euro;<?= number_format((float) ($group['total'] ?? 0), 2) ?></div>
                            </div>

                            <div style="background-color: #132746;">
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

                                    $itemTitle = trim((string) ($item['performer_names'] ?? ''));
                                    if ($itemTitle === '') {
                                        $itemTitle = 'Session #' . (int) ($item['session_id'] ?? 0);
                                    }

                                    $venueName = (string) ($item['venue_name'] ?? 'Unknown venue');
                                    $timeLabel = substr((string) ($item['start_time'] ?? ''), 0, 5);
                                    $languageLabel = trim((string) ($item['label'] ?? ''));
                                    $priceValue = (float) ($item['unit_price'] ?? 0);
                                    $remainingSpots = max(0, (int) ($item['remaining_spots'] ?? 0));
                                    $currentQuantity = (int) ($item['quantity'] ?? 0);
                                    $canIncrease = $currentQuantity < $remainingSpots;
                                    ?>
                                    <div class="px-3 py-3 border-top" style="border-color: rgba(255, 255, 255, 0.08) !important;">
                                        <div class="d-grid align-items-center gap-3" style="grid-template-columns: 140px 120px 1.4fr 1.5fr 150px 170px 100px;">
                                            <div>
                                                <span class="badge rounded-pill px-3 py-2" style="background-color: <?= htmlspecialchars($eventColors['bg']) ?>; color: <?= htmlspecialchars($eventColors['text']) ?>;">
                                                    <?= htmlspecialchars($eventLabel) ?>
                                                </span>
                                            </div>

                                            <div class="small text-white">
                                                <div class="fw-semibold"><?= htmlspecialchars($timeLabel) ?></div>
                                                <?php if ($languageLabel !== ''): ?>
                                                    <div style="color: rgba(255, 255, 255, 0.65);"><?= htmlspecialchars($languageLabel) ?></div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="small text-white">
                                                <?= htmlspecialchars($venueName) ?>
                                            </div>

                                            <div class="small text-white">
                                                <div class="fw-semibold"><?= htmlspecialchars($itemTitle) ?></div>
                                            </div>

                                            <div>
                                                <div class="d-inline-flex align-items-center gap-2 rounded-pill px-2 py-1" style="background-color: rgba(236, 216, 255, 0.12);">
                                                    <form method="POST" action="/cart/update" class="m-0">
                                                        <input type="hidden" name="cart_item_id" value="<?= (int) $item['id'] ?>">
                                                        <input type="hidden" name="quantity" value="<?= max(0, $currentQuantity - 1) ?>">
                                                        <button type="submit" class="btn btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width: 2rem; height: 2rem; border: 1px solid #d6a436; color: #d6a436; background: transparent;">−</button>
                                                    </form>

                                                    <span class="badge rounded-pill px-3 py-2" style="background-color: #f3dcff; color: #5d3c88; min-width: 2.5rem;">
                                                        <?= $currentQuantity ?>
                                                    </span>

                                                    <form method="POST" action="/cart/update" class="m-0">
                                                        <input type="hidden" name="cart_item_id" value="<?= (int) $item['id'] ?>">
                                                        <input type="hidden" name="quantity" value="<?= $currentQuantity + 1 ?>">
                                                        <button type="submit" class="btn btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width: 2rem; height: 2rem; border: 1px solid #d6a436; color: #d6a436; background: transparent; <?= $canIncrease ? '' : 'opacity:0.45;' ?>" <?= $canIncrease ? '' : 'disabled' ?>>+</button>
                                                    </form>
                                                </div>
                                            </div>

                                            <div class="small fw-semibold" style="color: #d6a436;">
                                                <?php if ($priceValue <= 0): ?>
                                                    <div>Pay as you like</div>
                                                <?php else: ?>
                                                    <div>&euro;<?= number_format($priceValue, 2) ?></div>
                                                <?php endif; ?>
                                                <div style="color: rgba(255, 255, 255, 0.65);">&euro;<?= number_format((float) ($item['line_total'] ?? 0), 2) ?> total</div>
                                            </div>

                                            <div class="text-end">
                                                <form method="POST" action="/cart/remove" class="m-0">
                                                    <input type="hidden" name="cart_item_id" value="<?= (int) $item['id'] ?>">
                                                    <button type="submit" class="btn btn-sm px-3" style="background-color: #d6a436; color: #11233d; border-radius: 0.55rem;">Delete</button>
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
                                <div class="rounded-3 px-3 py-3 d-flex align-items-center gap-2" style="background-color: #f4f1e8; color: #11233d;">
                                    <input class="form-check-input mt-0" type="checkbox" value="" id="program-confirm" disabled checked>
                                    <label class="form-check-label small" for="program-confirm">
                                        I confirm: all participants are 12+ years old and no strollers.
                                    </label>
                                </div>
                            </div>

                            <div class="col-lg-6 text-lg-end">
                                <div class="mb-3">
                                    <div class="small text-uppercase" style="color: rgba(255, 255, 255, 0.6); letter-spacing: 0.08em;">Grand Total</div>
                                    <div class="display-6 fw-bold mb-0" style="color: #d6a436;">&euro;<?= number_format($subtotal, 2) ?></div>
                                </div>

                                <a href="/checkout" class="btn px-5 py-2" style="background-color: #f0b84b; color: #11233d; font-weight: 700; min-width: 240px;">
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
