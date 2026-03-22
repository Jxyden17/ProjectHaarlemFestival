<?php
$session = is_array($session ?? null) ? $session : [];

$eventNameRaw = (string) ($session['event_name'] ?? '');
$eventLabel = match (strtolower(str_replace(' ', '', $eventNameRaw))) {
    'tellingstory' => 'Stories',
    'astrollthroughhistory' => 'Tour',
    'dance' => 'Dance',
    'jazz' => 'Jazz',
    'yummy' => 'Yummy',
    default => $eventNameRaw !== '' ? $eventNameRaw : 'Event',
};

$performerNames = trim((string) ($session['performer_names'] ?? ''));
$sessionTitle = $performerNames !== '' ? $performerNames : ('Session #' . (int) ($session['id'] ?? 0));
$venueName = (string) ($session['venue_name'] ?? 'Unknown venue');
$dateLabel = '';

if (trim((string) ($session['date'] ?? '')) !== '') {
    try {
        $dateLabel = (new DateTime((string) $session['date']))->format('l (d.m.Y)');
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
$initialUnitPrice = $pricingType === 'pay_as_you_like' ? $minimumPrice : $fixedPrice;
?>

<div class="container-fluid px-0">
    <section class="py-4 py-lg-5" style="background: linear-gradient(rgba(7, 16, 38, 0.72), rgba(7, 16, 38, 0.92)), url('/img/home/home-banner.png') center/cover no-repeat;">
        <div class="container">
            <h1 class="display-3 fw-bold mb-2" style="color: #d6a436;">Book tickets</h1>
            <p class="fs-3 mb-0 text-white">You can first add tickets to the list then buy it all in one go</p>
        </div>
    </section>

    <div class="container py-5">
        <div class="mx-auto" style="max-width: 640px;">
            <div class="text-center mb-4">
                <a href="/cart" class="btn btn-outline-light btn-sm mb-3">Back to Personal Program</a>
                <h2 class="h1 text-white mb-0">
                    <?= $pricingType === 'pay_as_you_like' ? 'Pay-as-you-Like' : 'Book tickets' ?>
                </h2>
            </div>

            <form method="POST" action="/cart/add" class="d-grid gap-3">
                <input type="hidden" name="session_id" value="<?= (int) ($session['id'] ?? 0) ?>">

                <div>
                    <label class="form-label small text-white">Select Event</label>
                    <div class="rounded-3 px-3 py-3" style="border: 2px solid #d6a436; background-color: #132746; color: #f4f1e8;">
                        <span><?= htmlspecialchars($eventLabel) ?></span>
                    </div>
                </div>

                <?php if ($languageLabel !== ''): ?>
                    <div>
                        <label class="form-label small text-white">Select Languages</label>
                        <div class="rounded-3 px-3 py-3" style="border: 2px solid #d6a436; background-color: #132746; color: #f4f1e8;">
                            <span><?= htmlspecialchars($languageLabel) ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <div>
                    <label class="form-label small text-white">Select Act</label>
                    <div class="rounded-3 px-3 py-3" style="border: 2px solid #d6a436; background-color: #132746; color: #f4f1e8;">
                        <span><?= htmlspecialchars($sessionTitle) ?></span>
                    </div>
                </div>

                <div>
                    <label class="form-label small text-white">Select Day</label>
                    <div class="rounded-3 px-3 py-3" style="border: 2px solid #d6a436; background-color: #132746; color: #f4f1e8;">
                        <span><?= htmlspecialchars($dateLabel !== '' ? $dateLabel : 'Unknown date') ?></span>
                    </div>
                </div>

                <div>
                    <label class="form-label small text-white">Select Time</label>
                    <div class="rounded-3 px-3 py-3" style="border: 2px solid #d6a436; background-color: #132746; color: #f4f1e8;">
                        <span><?= htmlspecialchars($timeLabel !== '' ? $timeLabel : 'Unknown time') ?></span>
                    </div>
                </div>

                <div class="rounded-3 p-3" style="border: 2px solid #d6a436; background-color: #132746;">
                    <?php if ($pricingType === 'pay_as_you_like'): ?>
                        <label for="custom_price" class="form-label text-white mb-1">Choose your price per ticket</label>
                        <div class="small mb-3" style="color: rgba(244, 241, 232, 0.78);">
                            Minimum suggested amount: &euro;<?= number_format($minimumPrice, 2) ?>
                        </div>

                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <input
                                    id="custom_price"
                                    type="number"
                                    name="custom_price"
                                    min="<?= htmlspecialchars(number_format($minimumPrice, 2, '.', '')) ?>"
                                    step="0.01"
                                    value="<?= htmlspecialchars(number_format($minimumPrice, 2, '.', '')) ?>"
                                    class="form-control"
                                    style="background-color: #f4f1e8;"
                                    <?= $isSoldOut ? 'disabled' : '' ?>
                                    required
                                >
                            </div>

                            <div class="col-md-6">
                                <label for="quantity" class="form-label text-white mb-1">Ticket Amount</label>
                                <div class="d-flex align-items-center justify-content-between rounded-3 px-2 py-2" style="border: 1px solid #d6a436; min-height: 56px;">
                                    <button type="button" class="btn btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center" data-qty-action="decrease" style="width: 2rem; height: 2rem; border: 1px solid #d6a436; color: #d6a436; background: transparent;" <?= $isSoldOut ? 'disabled' : '' ?>>−</button>
                                    <input
                                        id="quantity"
                                        type="number"
                                        name="quantity"
                                        min="1"
                                        max="<?= max(1, $availableSpots) ?>"
                                        value="1"
                                        class="form-control border-0 text-center bg-transparent text-white"
                                        style="box-shadow: none; max-width: 90px;"
                                        <?= $isSoldOut ? 'disabled' : '' ?>
                                        required
                                    >
                                    <button type="button" class="btn btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center" data-qty-action="increase" style="width: 2rem; height: 2rem; border: 1px solid #d6a436; color: #d6a436; background: transparent;" <?= $isSoldOut ? 'disabled' : '' ?>>+</button>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div>
                                <div class="text-white fw-semibold">Ticket Amount</div>
                                <div class="small" style="color: rgba(244, 241, 232, 0.78);">&euro;<?= number_format($fixedPrice, 2) ?> per person</div>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center" data-qty-action="decrease" style="width: 2rem; height: 2rem; border: 1px solid #d6a436; color: #d6a436; background: transparent;" <?= $isSoldOut ? 'disabled' : '' ?>>−</button>
                                <input
                                    id="quantity"
                                    type="number"
                                    name="quantity"
                                    min="1"
                                    max="<?= max(1, $availableSpots) ?>"
                                    value="1"
                                    class="form-control border-0 text-center bg-transparent text-white"
                                    style="box-shadow: none; max-width: 90px;"
                                    <?= $isSoldOut ? 'disabled' : '' ?>
                                    required
                                >
                                <button type="button" class="btn btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center" data-qty-action="increase" style="width: 2rem; height: 2rem; border: 1px solid #d6a436; color: #d6a436; background: transparent;" <?= $isSoldOut ? 'disabled' : '' ?>>+</button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-between align-items-center border-top pt-3" style="border-color: rgba(214, 164, 54, 0.6) !important;">
                    <span class="fs-4 text-white">Total</span>
                    <strong id="booking-total" class="fs-1" style="color: #d6a436;">&euro;<?= number_format($initialUnitPrice, 2) ?></strong>
                </div>

                <div class="rounded-3 px-3 py-3 d-flex align-items-center gap-2" style="background-color: #f4f1e8; color: #11233d;">
                    <input class="form-check-input mt-0" type="checkbox" value="1" id="booking-confirm" name="booking_confirm" required <?= $isSoldOut ? 'disabled' : '' ?>>
                    <label class="form-check-label small" for="booking-confirm">
                        I confirm: all participants are 12+ years old and no strollers.
                    </label>
                </div>

                <?php if ($isSoldOut): ?>
                    <div class="alert alert-warning mb-0">
                        This session is currently sold out.
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn w-100 py-3" style="background-color: #d6a436; color: #11233d; font-weight: 700;" <?= $isSoldOut ? 'disabled' : '' ?>>
                    Add to personal program
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    (() => {
        const quantityInput = document.getElementById('quantity');
        const customPriceInput = document.getElementById('custom_price');
        const totalEl = document.getElementById('booking-total');
        const decreaseBtn = document.querySelector('[data-qty-action="decrease"]');
        const increaseBtn = document.querySelector('[data-qty-action="increase"]');
        const maxQuantity = Number(quantityInput?.max || 1);
        const minQuantity = Number(quantityInput?.min || 1);
        const defaultUnitPrice = Number(<?= json_encode($initialUnitPrice) ?>);

        const updateTotal = () => {
            if (!quantityInput || !totalEl) {
                return;
            }

            const quantity = Math.max(minQuantity, Math.min(maxQuantity, Number(quantityInput.value || minQuantity)));
            quantityInput.value = String(quantity);

            let unitPrice = defaultUnitPrice;
            if (customPriceInput) {
                unitPrice = Number(customPriceInput.value || defaultUnitPrice);
            }

            const total = quantity * unitPrice;
            totalEl.textContent = new Intl.NumberFormat('nl-NL', {
                style: 'currency',
                currency: 'EUR'
            }).format(total);
        };

        if (decreaseBtn && quantityInput) {
            decreaseBtn.addEventListener('click', () => {
                const nextValue = Math.max(minQuantity, Number(quantityInput.value || minQuantity) - 1);
                quantityInput.value = String(nextValue);
                updateTotal();
            });
        }

        if (increaseBtn && quantityInput) {
            increaseBtn.addEventListener('click', () => {
                const nextValue = Math.min(maxQuantity, Number(quantityInput.value || minQuantity) + 1);
                quantityInput.value = String(nextValue);
                updateTotal();
            });
        }

        quantityInput?.addEventListener('input', updateTotal);
        customPriceInput?.addEventListener('input', updateTotal);
        updateTotal();
    })();
</script>
