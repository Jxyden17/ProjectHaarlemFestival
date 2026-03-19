<?php
$bookingItems = $section->items ?? [];
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

            <?php if ($bookingButton): ?>
                <a class="story-booking-button" href="<?= htmlspecialchars((string) ($bookingButton->url ?? '#')) ?>">
                    <?= htmlspecialchars((string) ($bookingButton->title ?? 'Book Now')) ?>
                </a>
            <?php endif; ?>
        </article>
    </section>
<?php endif; ?>
