<?php
$featuredItem = null;

foreach ($section->items as $item) {
    if (!empty($item->image)) {
        $featuredItem = $item;
        break;
    }
}

if ($featuredItem === null && !empty($section->items)) {
    $featuredItem = $section->items[0];
}

$calloutImage = $featuredItem?->image ?? null;
$calloutAlt = $featuredItem?->title ?: ($section->title ?? 'Stories callout');
?>

<section class="stories-callout">
    <div class="callout-inner">
        <div class="callout-feature">
            <?php if ($calloutImage): ?>
                <img
                    class="callout-feature-image"
                    src="<?= htmlspecialchars($calloutImage) ?>"
                    alt="<?= htmlspecialchars($calloutAlt) ?>"
                >
            <?php endif; ?>

            <div class="callout-feature-overlay"></div>

            <div class="callout-copy">
                <h3 class="callout-title"><?= htmlspecialchars($section->title ?? '') ?></h3>
                <?php if (!empty($section->subTitle)): ?>
                    <p class="callout-subtitle"><?= htmlspecialchars($section->subTitle) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($section->description)): ?>
            <p class="callout-description"><?= nl2br(htmlspecialchars($section->description)) ?></p>
        <?php endif; ?>
    </div>
</section>
