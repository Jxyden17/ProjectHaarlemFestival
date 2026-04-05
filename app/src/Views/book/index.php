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
$sessionLabel = trim((string) ($session['label'] ?? ''));
$sessionTitle = $performerNames !== ''
    ? $performerNames
    : ($sessionLabel !== '' ? $sessionLabel : ('Session #' . (int) ($session['id'] ?? 0)));
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
$languageLabel = preg_match('/^\+?\d{1,2}$/', $sessionLabel) === 1 ? $sessionLabel : '';
$pricingType = (string) ($session['pricing_type'] ?? 'fixed');
$minimumPrice = isset($session['minimum_price']) ? (float) $session['minimum_price'] : 5.0;
$fixedPrice = isset($session['price']) ? (float) $session['price'] : 0.0;
$availableSpots = max(0, (int) (($session['available_spots'] ?? 0) - ($session['amount_sold'] ?? 0)));
$isSoldOut = $availableSpots <= 0;
$initialUnitPrice = $pricingType === 'pay_as_you_like' ? $minimumPrice : $fixedPrice;
$familyTicketSize = 4;
$familyPackageAvailable = $pricingType !== 'pay_as_you_like' && $availableSpots >= $familyTicketSize;
$initialDisplayQuantity = 1;
$initialMultiplier = 1;
$initialSubmittedQuantity = $initialDisplayQuantity * $initialMultiplier;
$initialMaxDisplayQuantity = max(1, $availableSpots);
?>

<div class="container-fluid px-0 book-page">
    <section class="py-4 py-lg-5 cart-hero">
        <div class="container">
            <a href="/cart" class="btn btn-sm mb-3 cart-back-btn">
                Back to Shopping Cart
            </a>
            <h1 class="display-3 fw-bold mb-2 cart-page-title">Book tickets</h1>
            <p class="fs-3 mb-0 text-white cart-page-subtitle">You can first add tickets to the list then buy it all in one go</p>
        </div>
    </section>

    <div class="container py-5">
        <div class="mx-auto book-shell">
            <div class="text-center mb-4">
                <h2 class="h1 text-white mb-0">
                    <?= $pricingType === 'pay_as_you_like' ? 'Pay-as-you-Like' : 'Book tickets' ?>
                </h2>
            </div>

            <form method="POST" action="/cart/add" class="d-grid gap-3">
                <input type="hidden" name="session_id" value="<?= (int) ($session['id'] ?? 0) ?>">
                <input type="hidden" name="quantity" id="quantity" value="<?= $initialSubmittedQuantity ?>">

                <div>
                    <label class="form-label small text-white">Your event</label>
                    <div class="rounded-3 px-3 py-3 book-info-box">
                        <span><?= htmlspecialchars($eventLabel) ?></span>
                    </div>
                </div>

                <?php if ($languageLabel !== ''): ?>
                    <div>
                        <label class="form-label small text-white">Available language</label>
                        <div class="rounded-3 px-3 py-3 book-info-box">
                            <span><?= htmlspecialchars($languageLabel) ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <div>
                    <label class="form-label small text-white">Your act</label>
                    <div class="rounded-3 px-3 py-3 book-info-box">
                        <span><?= htmlspecialchars($sessionTitle) ?></span>
                    </div>
                </div>

                <div>
                    <label class="form-label small text-white">Your day</label>
                    <div class="rounded-3 px-3 py-3 book-info-box">
                        <span><?= htmlspecialchars($dateLabel !== '' ? $dateLabel : 'Unknown date') ?></span>
                    </div>
                </div>

                <div>
                    <label class="form-label small text-white">Your time</label>
                    <div class="rounded-3 px-3 py-3 book-info-box">
                        <span><?= htmlspecialchars($timeLabel !== '' ? $timeLabel : 'Unknown time') ?></span>
                    </div>
                </div>

                <div class="rounded-3 p-3 book-ticket-box">
                    <?php if ($pricingType === 'pay_as_you_like'): ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="book-pay-card h-100">
                                    <label for="custom_price" class="form-label text-white mb-1">Your ticket price</label>
                                    <div class="small mb-2 book-muted-copy">
                                        Minimum suggested amount: &euro;<?= number_format($minimumPrice, 2) ?>
                                    </div>
                                    <input
                                        id="custom_price"
                                        type="number"
                                        name="custom_price"
                                        min="<?= htmlspecialchars(number_format($minimumPrice, 2, '.', '')) ?>"
                                        step="0.01"
                                        value="<?= htmlspecialchars(number_format($minimumPrice, 2, '.', '')) ?>"
                                        class="form-control book-price-input"
                                        <?= $isSoldOut ? 'disabled' : '' ?>
                                        required
                                    >
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="book-pay-card h-100">
                                    <label for="booking_quantity_display" class="form-label text-white mb-1">Your ticket amount</label>
                                    <div class="small mb-2 book-muted-copy">Choose how many tickets you want to add</div>
                                    <div class="d-flex align-items-center justify-content-between rounded-3 px-2 py-2 book-qty-shell">
                                        <button type="button" class="btn btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center book-qty-btn" data-qty-action="decrease" <?= $isSoldOut ? 'disabled' : '' ?>>−</button>
                                        <input
                                            id="booking_quantity_display"
                                            type="number"
                                            min="1"
                                            max="<?= max(1, $availableSpots) ?>"
                                            value="1"
                                            class="form-control border-0 text-center bg-transparent text-white book-qty-input"
                                            <?= $isSoldOut ? 'disabled' : '' ?>
                                            required
                                        >
                                        <button type="button" class="btn btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center book-qty-btn" data-qty-action="increase" <?= $isSoldOut ? 'disabled' : '' ?>>+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="d-grid gap-3">
                            <div>
                                <div class="text-white fw-semibold mb-2">Your ticket type</div>
                                <div class="book-ticket-type-grid">
                                    <label class="book-ticket-option">
                                        <input
                                            type="radio"
                                            name="ticket_mode"
                                            value="standard"
                                            class="book-ticket-option-input"
                                            checked
                                            <?= $isSoldOut ? 'disabled' : '' ?>
                                        >
                                        <span class="book-ticket-option-card">
                                            <span class="book-ticket-option-title">Standard ticket</span>
                                            <span class="book-ticket-option-copy">&euro;<?= number_format($fixedPrice, 2) ?> per person</span>
                                        </span>
                                    </label>

                                    <label class="book-ticket-option <?= $familyPackageAvailable ? '' : 'book-ticket-option--disabled' ?>">
                                        <input
                                            type="radio"
                                            name="ticket_mode"
                                            value="family"
                                            class="book-ticket-option-input"
                                            <?= (!$familyPackageAvailable || $isSoldOut) ? 'disabled' : '' ?>
                                        >
                                        <span class="book-ticket-option-card">
                                            <span class="book-ticket-option-title">Family package</span>
                                            <span class="book-ticket-option-copy">
                                                4 people, &euro;<?= number_format($fixedPrice, 2) ?> per person
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div>
                                <div class="text-white fw-semibold">Your ticket amount</div>
                                <div class="small book-muted-copy" id="booking-amount-copy">&euro;<?= number_format($fixedPrice, 2) ?> per person</div>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center book-qty-btn" data-qty-action="decrease" <?= $isSoldOut ? 'disabled' : '' ?>>−</button>
                                <input
                                    id="booking_quantity_display"
                                    type="number"
                                    min="1"
                                    max="<?= $initialMaxDisplayQuantity ?>"
                                    value="1"
                                    class="form-control border-0 text-center bg-transparent text-white book-qty-input"
                                    <?= $isSoldOut ? 'disabled' : '' ?>
                                    required
                                >
                                <button type="button" class="btn btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center book-qty-btn" data-qty-action="increase" <?= $isSoldOut ? 'disabled' : '' ?>>+</button>
                            </div>
                        </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-between align-items-center border-top pt-3 book-total-row">
                    <span class="fs-4 text-white">Total</span>
                    <strong id="booking-total" class="fs-1 book-total-value">&euro;<?= number_format($initialUnitPrice, 2) ?></strong>
                </div>

                <div class="rounded-3 px-3 py-3 d-flex align-items-center gap-2 book-confirm-box">
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

                <button type="submit" class="btn w-100 py-3 book-submit-btn" <?= $isSoldOut ? 'disabled' : '' ?>>
                    Add to shopping cart
                </button>
            </form>

            <?php if ($pricingType === 'pay_as_you_like'): ?>
                <div class="book-note-card mt-4">
                    <div class="book-note-title">Important Information</div>
                    <ul class="book-note-list mb-0">
                        <li>Pay-as-you-like lets you decide what feels fair for your ticket.</li>
                        <li>The minimum amount keeps the session accessible while still supporting the artist.</li>
                        <li>You choose your amount per ticket before adding it to your shopping cart.</li>
                    </ul>
                </div>
            <?php else: ?>
                <div class="book-note-card mt-4">
                    <div class="book-note-title">Important Information</div>
                    <ul class="book-note-list mb-0">
                        <li>The family package covers 4 people for the same session and time slot.</li>
                        <li>Choose 1 package for 4 people, 2 packages for 8 people, and so on.</li>
                        <li>Family packages use the same availability as regular tickets, so they depend on enough spots being open.</li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    (() => {
        const quantityInput = document.getElementById('quantity');
        const quantityDisplayInput = document.getElementById('booking_quantity_display');
        const customPriceInput = document.getElementById('custom_price');
        const totalEl = document.getElementById('booking-total');
        const decreaseBtn = document.querySelector('[data-qty-action="decrease"]');
        const increaseBtn = document.querySelector('[data-qty-action="increase"]');
        const defaultUnitPrice = Number(<?= json_encode($initialUnitPrice) ?>);
        const pricingType = <?= json_encode($pricingType) ?>;
        const availableSpots = Number(<?= json_encode($availableSpots) ?>);
        const familyTicketSize = Number(<?= json_encode($familyTicketSize) ?>);
        const amountCopy = document.getElementById('booking-amount-copy');
        const ticketModeInputs = document.querySelectorAll('input[name="ticket_mode"]');

        const getCurrentMode = () => {
            const selectedMode = Array.from(ticketModeInputs).find((input) => input.checked);
            return selectedMode ? selectedMode.value : 'standard';
        };

        const getMultiplier = () => {
            if (pricingType !== 'fixed') {
                return 1;
            }

            return getCurrentMode() === 'family' ? familyTicketSize : 1;
        };

        const getMaxDisplayQuantity = () => {
            const multiplier = getMultiplier();
            return Math.max(1, Math.floor(availableSpots / multiplier));
        };

        const updateTotal = () => {
            if (!quantityInput || !quantityDisplayInput || !totalEl) {
                return;
            }

            const minQuantity = Number(quantityDisplayInput.min || 1);
            const maxQuantity = getMaxDisplayQuantity();
            const displayQuantity = Math.max(minQuantity, Math.min(maxQuantity, Number(quantityDisplayInput.value || minQuantity)));
            const multiplier = getMultiplier();
            const submittedQuantity = displayQuantity * multiplier;

            quantityDisplayInput.max = String(maxQuantity);
            quantityDisplayInput.value = String(displayQuantity);
            quantityInput.value = String(submittedQuantity);

            let unitPrice = defaultUnitPrice;
            if (customPriceInput) {
                unitPrice = Number(customPriceInput.value || defaultUnitPrice);
            }

            if (amountCopy && pricingType === 'fixed') {
                amountCopy.textContent = getCurrentMode() === 'family'
                    ? `${new Intl.NumberFormat('nl-NL', { style: 'currency', currency: 'EUR' }).format(unitPrice * familyTicketSize)} per family package (4 people)`
                    : `${new Intl.NumberFormat('nl-NL', { style: 'currency', currency: 'EUR' }).format(unitPrice)} per person`;
            }

            const total = submittedQuantity * unitPrice;
            totalEl.textContent = new Intl.NumberFormat('nl-NL', {
                style: 'currency',
                currency: 'EUR'
            }).format(total);
        };

        if (decreaseBtn && quantityDisplayInput) {
            decreaseBtn.addEventListener('click', () => {
                const minQuantity = Number(quantityDisplayInput.min || 1);
                const nextValue = Math.max(minQuantity, Number(quantityDisplayInput.value || minQuantity) - 1);
                quantityDisplayInput.value = String(nextValue);
                updateTotal();
            });
        }

        if (increaseBtn && quantityDisplayInput) {
            increaseBtn.addEventListener('click', () => {
                const minQuantity = Number(quantityDisplayInput.min || 1);
                const maxQuantity = getMaxDisplayQuantity();
                const nextValue = Math.min(maxQuantity, Number(quantityDisplayInput.value || minQuantity) + 1);
                quantityDisplayInput.value = String(nextValue);
                updateTotal();
            });
        }

        quantityDisplayInput?.addEventListener('input', updateTotal);
        customPriceInput?.addEventListener('input', updateTotal);
        ticketModeInputs.forEach((input) => input.addEventListener('change', updateTotal));
        updateTotal();
    })();
</script>
