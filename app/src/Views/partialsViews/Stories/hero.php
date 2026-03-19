<?php
use App\Models\Page\Section;

$section = $section ?? null;
if (!$section instanceof Section) {
    return;
}

$items = is_array($section->items) ? $section->items : [];
$calendarIcon = '/img/storiesIMG/Icon-for-the-calendar-hero.png';
$pointerIcon = '/img/storiesIMG/Icon-for-the-hero-pointer.png';
$viewProgramIcon = '/img/storiesIMG/Icon-for-the-view-program.png';

$renderInlineRichText = static function (?string $html, string $fallback = ''): string {
    $value = trim((string)($html ?? ''));
    if ($value === '') {
        return htmlspecialchars($fallback);
    }

    $value = preg_replace('/^\s*<p>(.*)<\/p>\s*$/is', '$1', $value) ?? $value;
    return strip_tags($value, '<strong><em><u><a><br>');
};
?>

<section class="stories-hero-section">
    <div class="stories-hero-images">
        <?php foreach ($items as $item): ?>
            <div class="stories-hero-img-item">
                <img src="<?= htmlspecialchars((string)($item->image ?? '')) ?>" alt="<?= htmlspecialchars((string)($item->title ?? '')) ?>">
            </div>
        <?php endforeach; ?>
    </div>

    <div class="stories-hero-overlay"></div>

    <div class="stories-hero-inner">
        <div class="stories-hero-text">
            <div class="hero-badge">
                <span class="hero-badge-icon">
                    <img src="<?= htmlspecialchars($calendarIcon) ?>" alt="" aria-hidden="true">
                </span>
                <span class="hero-badge-text">This Weekend - July 24-27, 2025</span>
            </div>

            <h1 class="stories-banner-title"><?= $renderInlineRichText($section->title ?? null, 'Stories in Haarlem') ?></h1>

            <div class="hero-info-cards">
                <div class="hero-info-card">
                    <span class="info-card-icon">
                        <img src="<?= htmlspecialchars($calendarIcon) ?>" alt="" aria-hidden="true">
                    </span>
                    <div class="info-card-content">
                        <h3>Total Events</h3>
                        <p>15 Shows</p>
                    </div>
                </div>

                <div class="hero-info-card">
                    <span class="info-card-icon">
                        <img src="<?= htmlspecialchars($pointerIcon) ?>" alt="" aria-hidden="true">
                    </span>
                    <div class="info-card-content">
                        <h3>Venues</h3>
                        <p>6 Locations</p>
                    </div>
                </div>
            </div>

            <div class="hero-actions">
                <a href="#schedule" class="view-program-btn">
                    View program
                    <span class="view-program-btn-icon">
                        <img src="<?= htmlspecialchars($viewProgramIcon) ?>" alt="" aria-hidden="true">
                    </span>
                </a>
            </div>
        </div>
    </div>
</section>
