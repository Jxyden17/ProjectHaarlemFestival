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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars((string)($pageTitle ?? 'Stories')) ?></title>
    <?php $storiesCssVersion = @filemtime(__DIR__ . '/../../../public/css/Stories/index.css') ?: time(); ?>
    <link href="/css/Stories/index.css?v=<?= (int)$storiesCssVersion ?>" rel="stylesheet">
    <style>
        .stories-detail-shell { max-width: 1120px; margin: 0 auto; padding: 48px 24px 80px; color: #fff; }
        .stories-detail-hero { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 32px; align-items: stretch; margin-bottom: 48px; }
        .stories-detail-panel { background: #1a2b47; border: 1px solid #2d4563; border-radius: 16px; padding: 32px; }
        .stories-detail-cover { min-height: 420px; border-radius: 16px; background: center/cover no-repeat #0f1a2e; }
        .stories-detail-tags { display: flex; flex-wrap: wrap; gap: 10px; margin: 16px 0 24px; }
        .stories-detail-tag { padding: 8px 14px; border-radius: 999px; background: rgba(106, 93, 175, 0.28); color: #d8d0ff; font-weight: 600; font-size: 14px; }
        .stories-detail-grid { display: grid; grid-template-columns: 1fr 360px; gap: 32px; margin-bottom: 48px; }
        .stories-detail-gallery { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .stories-detail-gallery img { width: 100%; height: 200px; object-fit: cover; border-radius: 12px; }
        .stories-feature-list { display: grid; gap: 12px; }
        .stories-feature-row { display: flex; justify-content: space-between; gap: 16px; padding: 14px 18px; border: 1px solid #2d4563; border-radius: 12px; background: #0f1a2e; }
        .stories-booking-list { display: grid; gap: 12px; margin: 20px 0; }
        .stories-booking-cta { display: inline-block; margin-top: 8px; padding: 12px 18px; border-radius: 10px; background: #c79636; color: #0f2035; text-decoration: none; font-weight: 700; }
        @media (max-width: 900px) {
            .stories-detail-hero, .stories-detail-grid { grid-template-columns: 1fr; }
            .stories-detail-gallery { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="stories-detail-shell">
        <a href="/stories" class="view-profile-btn" style="margin-bottom: 24px;">Back to Stories</a>

        <section class="stories-detail-hero">
            <div class="stories-detail-panel">
                <p class="hero-badge-text"><?= $renderInlineRichText($hero?->subTitle ?? null, 'Story detail') ?></p>
                <h1 style="font-size: 48px; line-height: 1.1; margin: 8px 0 18px;"><?= $renderInlineRichText($hero?->title ?? null, (string)($pageTitle ?? 'Stories')) ?></h1>
                <div style="color: #b0b8c1; font-size: 16px; line-height: 1.8;"><?= $renderBlockRichText($hero?->description ?? null) ?></div>
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
                <h2 style="margin-top:0;"><?= htmlspecialchars((string)($about?->title ?? 'About')) ?></h2>
                <?php foreach ($aboutItems as $item): ?>
                    <div style="color:#b0b8c1; line-height:1.8;"><?= $renderBlockRichText($item->content ?? null) ?></div>
                <?php endforeach; ?>
            </div>

            <aside class="stories-detail-panel">
                <h2 style="margin-top:0;"><?= $renderInlineRichText($booking?->title ?? null, 'Booking') ?></h2>
                <div style="color:#b0b8c1;"><?= $renderBlockRichText($booking?->description ?? null) ?></div>
                <div class="stories-booking-list">
                    <?php if ($bookingDate): ?><div><strong>Date</strong><br><?= htmlspecialchars((string)($bookingDate->title ?? '')) ?></div><?php endif; ?>
                    <?php if ($bookingLocation): ?><div><strong>Location</strong><br><?= htmlspecialchars((string)($bookingLocation->title ?? '')) ?></div><?php endif; ?>
                    <?php if ($bookingPriceLabel): ?><div><strong><?= htmlspecialchars((string)($bookingPriceLabel->title ?? 'Price')) ?></strong></div><?php endif; ?>
                    <?php if ($bookingPrice): ?><div style="font-size:28px;font-weight:700;"><?= htmlspecialchars((string)($bookingPrice->title ?? '')) ?></div><?php endif; ?>
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
            <section class="stories-detail-panel" style="margin-bottom:48px;">
                <h2 style="margin-top:0;"><?= htmlspecialchars((string)($gallery?->title ?? 'Gallery')) ?></h2>
                <div class="stories-detail-gallery">
                    <?php foreach ($galleryItems as $item): ?>
                        <img src="<?= htmlspecialchars((string)($item->image ?? '')) ?>" alt="<?= htmlspecialchars((string)($item->title ?? 'Gallery image')) ?>">
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($featuredItems !== []): ?>
            <section class="stories-detail-panel">
                <h2 style="margin-top:0;"><?= htmlspecialchars((string)($featured?->title ?? 'Featured')) ?></h2>
                <div class="stories-feature-list">
                    <?php foreach ($featuredItems as $item): ?>
                        <div class="stories-feature-row">
                            <span><?= htmlspecialchars((string)($item->title ?? 'Feature')) ?></span>
                            <?php if (trim((string)($item->url ?? '')) !== ''): ?>
                                <a href="<?= htmlspecialchars((string)($item->url ?? '')) ?>" class="view-profile-btn">Open</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</body>
</html>
