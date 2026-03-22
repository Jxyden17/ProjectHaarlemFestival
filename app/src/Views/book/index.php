<?php
$session = is_array($session ?? null) ? $session : [];

$eventNameRaw = (string) ($session['event_name'] ?? '');
$eventLabel = match (strtolower(str_replace(' ', '', $eventNameRaw))) {
    'tellingstory' => 'Stories',
    'astrollthroughhistory' => 'Tour',
    'dance' => 'Dance',
    'jazz' => 'Jazz',
    default => $eventNameRaw !== '' ? $eventNameRaw : 'Event',
};

$performerNames = trim((string) ($session['performer_names'] ?? ''));
$sessionTitle = $performerNames !== '' ? $performerNames : ('Session #' . (int) ($session['id'] ?? 0));
$venueName = (string) ($session['venue_name'] ?? 'Unknown venue');
$dateLabel = '';

if (trim((string) ($session['date'] ?? '')) !== '') {
    try {
        $dateLabel = (new DateTime((string) $session['date']))->format('l, F j, Y');
    } catch (Throwable $e) {
        $dateLabel = (string) $session['date'];
    }
}

$timeLabel = substr((string) ($session['start_time'] ?? ''), 0, 5);
$languageLabel = trim((string) ($session['label'] ?? ''));
$pricingType = (string) ($session['pricing_type'] ?? 'fixed');
$minimumPrice = isset($session['minimum_price']) ? (float) $session['minimum_price'] : 5.0;
$fixedPrice = isset($session['price']) ? (float) $session['price'] : 0.0;
$availableSpots = max(0, (int) (($session['available_spots'] ?? 0) - ($session['amount_sold'] ?? 0)));
$isSoldOut = $availableSpots <= 0;
?>

<div class="container py-5">
    <section class="mb-5">
        <a href="/cart" class="btn btn-outline-light btn-sm mb-3">Back to Personal Program</a>
        <h1 class="display-4 mb-2">Book Tickets</h1>
        <p class="lead mb-0">Choose your ticket details and add this session to your personal program.</p>
    </section>

    <div class="row g-4">
        <div class="col-lg-7">
            <section class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 p-lg-5">
                    <div class="mb-4">
                        <span class="badge rounded-pill text-bg-secondary mb-3">
                            <?= htmlspecialchars($eventLabel) ?>
                        </span>
                        <h2 class="h1 mb-2"><?= htmlspecialchars($sessionTitle) ?></h2>
                        <p class="text-muted mb-0"><?= htmlspecialchars($venueName) ?></p>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="small text-muted">Date</div>
                            <div class="fw-semibold"><?= htmlspecialchars($dateLabel !== '' ? $dateLabel : 'Unknown date') ?></div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Time</div>
                            <div class="fw-semibold"><?= htmlspecialchars($timeLabel !== '' ? $timeLabel : 'Unknown time') ?></div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Language</div>
                            <div class="fw-semibold"><?= htmlspecialchars($languageLabel !== '' ? $languageLabel : 'N/A') ?></div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-muted">Availability</div>
                            <div class="fw-semibold"><?= $availableSpots ?> spot<?= $availableSpots === 1 ? '' : 's' ?> left</div>
                        </div>
                    </div>

                    <form method="POST" action="/cart/add" class="d-grid gap-3">
                        <input type="hidden" name="session_id" value="<?= (int) ($session['id'] ?? 0) ?>">

                        <div>
                            <label for="quantity" class="form-label">Tickets</label>
                            <input
                                id="quantity"
                                type="number"
                                name="quantity"
                                min="1"
                                max="<?= max(1, $availableSpots) ?>"
                                value="1"
                                class="form-control"
                                <?= $isSoldOut ? 'disabled' : '' ?>
                                required
                            >
                        </div>

                        <?php if ($pricingType === 'pay_as_you_like'): ?>
                            <div>
                                <label for="custom_price" class="form-label">Choose your price</label>
                                <div class="small text-muted mb-2">
                                    Minimum amount: &euro;<?= number_format($minimumPrice, 2) ?>
                                </div>
                                <input
                                    id="custom_price"
                                    type="number"
                                    name="custom_price"
                                    min="<?= htmlspecialchars(number_format($minimumPrice, 2, '.', '')) ?>"
                                    step="0.01"
                                    value="<?= htmlspecialchars(number_format($minimumPrice, 2, '.', '')) ?>"
                                    class="form-control"
                                    <?= $isSoldOut ? 'disabled' : '' ?>
                                    required
                                >
                            </div>
                        <?php endif; ?>

                        <?php if ($isSoldOut): ?>
                            <div class="alert alert-warning mb-0">
                                This session is currently sold out.
                            </div>
                        <?php else: ?>
                            <button type="submit" class="btn btn-primary btn-lg">
                                Add to Personal Program
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </section>
        </div>

        <div class="col-lg-5">
            <section class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h3 mb-4">Booking Summary</h2>

                    <div class="d-flex justify-content-between mb-3">
                        <span>Event</span>
                        <strong><?= htmlspecialchars($eventLabel) ?></strong>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span>Session</span>
                        <strong class="text-end"><?= htmlspecialchars($sessionTitle) ?></strong>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span>Venue</span>
                        <strong class="text-end"><?= htmlspecialchars($venueName) ?></strong>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span>Pricing</span>
                        <strong>
                            <?php if ($pricingType === 'pay_as_you_like'): ?>
                                Pay as you like
                            <?php else: ?>
                                &euro;<?= number_format($fixedPrice, 2) ?>
                            <?php endif; ?>
                        </strong>
                    </div>

                    <?php if ($pricingType === 'pay_as_you_like'): ?>
                        <p class="text-muted mb-0">
                            You choose the amount, but it must be at least &euro;<?= number_format($minimumPrice, 2) ?>.
                        </p>
                    <?php else: ?>
                        <p class="text-muted mb-0">
                            This session uses a fixed ticket price. You can adjust the quantity before adding it to your program.
                        </p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</div>
