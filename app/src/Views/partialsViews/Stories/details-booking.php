<?php
$bookingItems = $section->items ?? [];
$bookingSessionId = isset($bookingSessionId) ? (int) $bookingSessionId : 0;
$bookingPricingType = (string) ($bookingPricingType ?? 'fixed');
$bookingMinimumPrice = isset($bookingMinimumPrice) ? (float) $bookingMinimumPrice : 5.0;
$bookingDateTime = null;
$bookingLocation = null;
$bookingTags = [];
$bookingPriceLabel = null;
$bookingPrice = null;
$bookingButton = null;

foreach ($bookingItems as $item) {
    $category = $item->category ?? '';
    if ($category === 'datetime') {
        $bookingDateTime = $item;
    } elseif ($category === 'location') {
        $bookingLocation = $item;
    } elseif ($category === 'tag') {
        $bookingTags[] = $item;
    } elseif ($category === 'price_label') {
        $bookingPriceLabel = $item;
    } elseif ($category === 'price') {
        $bookingPrice = $item;
    } elseif ($category === 'button') {
        $bookingButton = $item;
    }
}
?>

<?php if (!empty($bookingItems)): ?>
    <section class="story-section">
        <h2 class="story-section-title"><?= htmlspecialchars((string) ($section->title ?? 'Book Your Experience')) ?></h2>
        <article class="story-booking-card">
            <?php if ($bookingDateTime): ?>
                <div class="story-booking-datetime"><?= htmlspecialchars((string) $bookingDateTime->title) ?></div>
            <?php endif; ?>

            <?php if ($bookingLocation): ?>
                <div class="story-booking-location"><?= htmlspecialchars((string) $bookingLocation->title) ?></div>
            <?php endif; ?>

            <?php if (!empty($bookingTags)): ?>
                <div class="story-tag-list story-tag-list--booking">
                    <?php foreach ($bookingTags as $tag): ?>
                        <span class="story-tag story-tag--muted"><?= htmlspecialchars((string) $tag->title) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($bookingPriceLabel): ?>
                <div class="story-booking-price-label"><?= htmlspecialchars((string) $bookingPriceLabel->title) ?></div>
            <?php endif; ?>

            <?php if ($bookingPrice): ?>
                <div class="story-booking-price"><?= htmlspecialchars((string) $bookingPrice->title) ?></div>
            <?php endif; ?>

            <?php if ($bookingButton && $bookingSessionId > 0): ?>
                <form method="POST" action="/cart/add">
                    <input type="hidden" name="session_id" value="<?= $bookingSessionId ?>">
                    <input type="hidden" name="quantity" value="1">

                    <?php if ($bookingPricingType === 'pay_as_you_like'): ?>
                        <div class="story-booking-price-label">Choose your price</div>
                        <p class="mb-3">
                            Minimum suggested amount: &euro;<?= number_format($bookingMinimumPrice, 2) ?>
                        </p>
                        <input
                            type="number"
                            name="custom_price"
                            min="<?= htmlspecialchars(number_format($bookingMinimumPrice, 2, '.', '')) ?>"
                            step="0.01"
                            value="<?= htmlspecialchars(number_format($bookingMinimumPrice, 2, '.', '')) ?>"
                            class="form-control mb-3"
                            required
                        >
                    <?php endif; ?>

                    <button type="submit" class="story-booking-button">
                        <?= htmlspecialchars((string) ($bookingButton->title ?? 'Add to cart')) ?>
                    </button>
                </form>
            <?php endif; ?>
        </article>
    </section>
<?php endif; ?>
