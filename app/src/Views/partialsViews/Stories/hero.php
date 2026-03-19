<?php
use App\Models\Page\Section;

$section = $section ?? null;
if (!$section instanceof Section) {
    return;
}

$items = is_array($section->items) ? $section->items : [];
$firstItem = $items[0] ?? null;
$secondItem = $items[1] ?? null;
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

<section class="stories-hero-section">
    <div class="stories-banner-inner">
        <div class="stories-banner-badge">
            <span class="stories-banner-badge-icon">🗓</span>
            <span>This Weekend • July 24-27, 2025</span>
        </div>

        <h1 class="stories-banner-title"><?= $renderInlineRichText($section->title ?? null, 'Stories in Haarlem') ?></h1>

        <?php if (trim((string)($firstItem->content ?? '')) !== ''): ?>
            <div class="stories-banner-description"><?= $renderBlockRichText($firstItem->content ?? null) ?></div>
        <?php endif; ?>
        <?php if (trim((string)($secondItem->content ?? '')) !== ''): ?>
            <div class="stories-banner-subcopy"><?= $renderBlockRichText($secondItem->content ?? null) ?></div>
        <?php endif; ?>

        <div class="stories-banner-stats">
            <div class="stories-stat-card">
                <div class="stories-stat-card-icon">
                    <span>🗓</span>
                </div>
                <div>
                    <div class="stories-stat-card-label">Total Events</div>
                    <div class="stories-stat-card-value">
                        15 Shows
                    </div>
                </div>
            </div>

            <div class="stories-stat-card">
                <div class="stories-stat-card-icon stories-stat-card-icon-venue">
                    <span>📍</span>
                </div>
                <div>
                    <div class="stories-stat-card-label">Venues</div>
                    <div class="stories-stat-card-value">
                        6 Locations
                    </div>
                </div>
            </div>
        </div>

        <div class="stories-banner-actions">
            <a href="#schedule" class="stories-banner-cta">
                <span>View program</span>
                <span class="stories-banner-cta-icon">↓</span>
            </a>
        </div>
    </div>
</div>
</section>
