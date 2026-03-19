<<<<<<< HEAD
<?php
$heroImage = $hero?->getFirstItemImage('image') ?? '';
$heroTags = $hero?->getItemsByCategorie('tag') ?? [];
$aboutItems = $about?->getItemsByCategorie('paragraph') ?? [];
$galleryItems = $gallery?->getItemsByCategorie('gallery') ?? [];
$featuredItems = $featured?->items ?? [];
$bookingTags = $booking?->getItemsByCategorie('tag') ?? [];
$bookingPriceItems = array_values($booking?->getItemsByCategorie('price') ?? []);
$bookingPriceLabelItems = array_values($booking?->getItemsByCategorie('price_label') ?? []);
$bookingDateItems = array_values($booking?->getItemsByCategorie('datetime') ?? []);
$bookingLocationItems = array_values($booking?->getItemsByCategorie('location') ?? []);
$bookingButtonItems = array_values($booking?->getItemsByCategorie('button') ?? []);
$bookingPrice = $bookingPriceItems[0] ?? null;
$bookingPriceLabel = $bookingPriceLabelItems[0] ?? null;
$bookingDate = $bookingDateItems[0] ?? null;
$bookingLocation = $bookingLocationItems[0] ?? null;
$bookingButton = $bookingButtonItems[0] ?? null;



$renderInlineRichText = static function (?string $html, string $fallback = ''): string {
    $value = trim((string)($html ?? ''));
    if ($value === '') {
        return htmlspecialchars($fallback);
    }

    $value = preg_replace('/^\s*<p>(.*)<\/p>\s*$/is', '$1', $value) ?? $value;
    return strip_tags($value, '<strong><em><u><a><br>');
};

$renderBlockRichText = static function (?string $html): string {
    $value = trim((string)($html ?? ''));
    return $value === '' ? '' : $value;
};

$resolveStoryUrl = static function (?string $url): string {
    $value = trim((string)($url ?? ''));
    if ($value === '') {
        return '';
    }

    if (
        preg_match('#^(?:https?:)?//#i', $value) === 1
        || str_starts_with($value, '/')
        || str_starts_with($value, '#')
        || str_starts_with($value, 'mailto:')
        || str_starts_with($value, 'tel:')
    ) {
        return $value;
    }

    return '/stories/' . ltrim($value, '/');
};
?>
=======
>>>>>>> e546708a4f41b4d19a79d29e644b39d8287b434c
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<<<<<<< HEAD
    <title><?= htmlspecialchars((string)($pageTitle ?? 'Stories')) ?></title>
    <?php $storiesCssVersion = @filemtime(__DIR__ . '/../../../public/css/Stories/index.css') ?: time(); ?>
    <link href="/css/Stories/index.css?v=<?= (int)$storiesCssVersion ?>" rel="stylesheet">
</head>
<body>
    <div class="stories-detail-shell">
        <a href="/stories" class="view-profile-btn stories-detail-back-link">Back to Stories</a>

        <section class="stories-detail-hero">
            <div class="stories-detail-panel">
                <p class="hero-badge-text"><?= $renderInlineRichText($hero?->subTitle ?? null, 'Story detail') ?></p>
                <h1 class="stories-detail-panel-title-main"><?= $renderInlineRichText($hero?->title ?? null, (string)($pageTitle ?? 'Stories')) ?></h1>
                <div class="stories-detail-panel-copy"><?= $renderBlockRichText($hero?->description ?? null) ?></div>
                <div class="stories-detail-tags">
                    <?php foreach ($heroTags as $item): ?>
                        <span class="stories-detail-tag"><?= htmlspecialchars((string)($item->title ?? '')) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="stories-detail-cover" style="background-image:url('<?= htmlspecialchars((string)$heroImage) ?>');"></div>
        </section>

        <section class="stories-detail-grid">
            <div class="stories-detail-panel">
                <h2 class="stories-detail-panel-title"><?= htmlspecialchars((string)($about?->title ?? 'About')) ?></h2>
                <?php foreach ($aboutItems as $item): ?>
                    <div class="stories-detail-panel-copy"><?= $renderBlockRichText($item->content ?? null) ?></div>
                <?php endforeach; ?>
            </div>

            <aside class="stories-detail-panel">
                <h2 class="stories-detail-panel-title"><?= $renderInlineRichText($booking?->title ?? null, 'Booking') ?></h2>
                <div class="stories-detail-panel-copy"><?= $renderBlockRichText($booking?->description ?? null) ?></div>
                <div class="stories-booking-list">
                    <?php if ($bookingDate): ?><div><strong>Date</strong><br><?= htmlspecialchars((string)($bookingDate->title ?? '')) ?></div><?php endif; ?>
                    <?php if ($bookingLocation): ?><div><strong>Location</strong><br><?= htmlspecialchars((string)($bookingLocation->title ?? '')) ?></div><?php endif; ?>
                    <?php if ($bookingPriceLabel): ?><div><strong><?= htmlspecialchars((string)($bookingPriceLabel->title ?? 'Price')) ?></strong></div><?php endif; ?>
                    <?php if ($bookingPrice): ?><div class="stories-booking-price"><?= htmlspecialchars((string)($bookingPrice->title ?? '')) ?></div><?php endif; ?>
                </div>
                <div class="stories-detail-tags">
                    <?php foreach ($bookingTags as $item): ?>
                        <span class="stories-detail-tag"><?= htmlspecialchars((string)($item->title ?? '')) ?></span>
                    <?php endforeach; ?>
                </div>
                <?php if ($bookingButton): ?>
                    <a class="stories-booking-cta" href="<?= htmlspecialchars((string)($bookingButton->url ?? '#')) ?>">
                        <?= htmlspecialchars((string)($bookingButton->title ?? 'Book now')) ?>
                    </a>
                <?php endif; ?>
            </aside>
        </section>

        <?php if ($galleryItems !== []): ?>
            <section class="stories-detail-panel stories-detail-gallery-panel">
                <h2 class="stories-detail-panel-title"><?= htmlspecialchars((string)($gallery?->title ?? 'Gallery')) ?></h2>
                <div class="stories-detail-gallery">
                    <?php foreach ($galleryItems as $item): ?>
                        <img src="<?= htmlspecialchars((string)($item->image ?? '')) ?>" alt="<?= htmlspecialchars((string)($item->title ?? 'Gallery image')) ?>">
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($featuredItems !== []): ?>
            <section class="stories-detail-panel">
                <h2 class="stories-detail-panel-title"><?= htmlspecialchars((string)($featured?->title ?? 'Featured')) ?></h2>
                <div class="stories-feature-list">
                    <?php foreach ($featuredItems as $item): ?>
                        <div class="stories-feature-row">
                            <span><?= htmlspecialchars((string)($item->title ?? 'Feature')) ?></span>
                            <?php $itemUrl = $resolveStoryUrl($item->url ?? ''); ?>
                            <?php if ($itemUrl !== ''): ?>
                                <a href="<?= htmlspecialchars($itemUrl) ?>" class="view-profile-btn">Open</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
=======
    <title><?= htmlspecialchars((string) ($pageTitle ?? 'Story Details')) ?></title>
    <link href="/css/Stories/details.css" rel="stylesheet">
</head>
<body>
    <main class="story-detail-page">
        <div class="story-detail-shell">
            <a class="story-back-link" href="/stories">
                &larr; Back to Stories
            </a>

            <?php if ($hero): ?>
                <?php $section = $hero; include __DIR__ . '/../partialsViews/Stories/details-hero.php'; ?>
            <?php endif; ?>

            <?php if ($gallery): ?>
                <?php $section = $gallery; include __DIR__ . '/../partialsViews/Stories/details-gallery.php'; ?>
            <?php endif; ?>

            <?php if ($about): ?>
                <?php $section = $about; include __DIR__ . '/../partialsViews/Stories/details-about.php'; ?>
            <?php endif; ?>

            <?php if ($featured): ?>
                <?php $section = $featured; include __DIR__ . '/../partialsViews/Stories/details-featured.php'; ?>
            <?php endif; ?>

            <?php if ($booking): ?>
                <?php $section = $booking; include __DIR__ . '/../partialsViews/Stories/details-booking.php'; ?>
            <?php endif; ?>
        </div>
    </main>
    <script src="/js/stories-detail-audio.js"></script>
>>>>>>> e546708a4f41b4d19a79d29e644b39d8287b434c
</body>
</html>
