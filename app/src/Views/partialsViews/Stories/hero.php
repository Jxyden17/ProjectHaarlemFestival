<?php
use App\Models\Page\Section;

$section = $section ?? null;
if (!$section instanceof Section) {
    return;
}

$items = is_array($section->items) ? $section->items : [];
$firstItem = $items[0] ?? null;
$secondItem = $items[1] ?? null;
$heroImages = array_values(array_filter($items, static fn ($item) => trim((string)($item->image ?? '')) !== ''));
?>

<section class="stories-hero-section">
    <?php if ($heroImages !== []): ?>
        <div class="stories-hero-images">
            <?php foreach ($heroImages as $item): ?>
                <div class="stories-hero-img-item">
                    <img src="<?= htmlspecialchars((string)($item->image ?? '')) ?>" alt="<?= htmlspecialchars((string)($item->title ?? 'Stories image')) ?>">
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="stories-hero-overlay"></div>

    <div class="stories-hero-text">
        <div class="hero-badge">
            <span class="hero-badge-icon">📅</span>
            <span class="hero-badge-text">This Weekend • July 24-27, 2025</span>
        </div>

        <h1><?= htmlspecialchars((string)($section->title ?? 'Stories in Haarlem')) ?></h1>
        <?php if (trim((string)($firstItem->content ?? '')) !== ''): ?>
            <p><?= htmlspecialchars((string)($firstItem->content ?? '')) ?></p>
        <?php endif; ?>
        <?php if (trim((string)($secondItem->content ?? '')) !== ''): ?>
            <p><?= htmlspecialchars((string)($secondItem->content ?? '')) ?></p>
        <?php endif; ?>

        <div class="hero-info-cards">
            <div class="hero-info-card">
                <span class="info-card-icon">📅</span>
                <div class="info-card-content">
                    <h3>Total Events</h3>
                    <p>15 Shows</p>
                </div>
            </div>
            <div class="hero-info-card">
                <span class="info-card-icon">📍</span>
                <div class="info-card-content">
                    <h3>Venues</h3>
                    <p>6 Locations</p>
                </div>
            </div>
        </div>

        <div class="hero-actions">
            <a href="#schedule" class="view-program-btn">
                <span class="view-program-btn-icon">🎭</span>
                View program
            </a>
        </div>
    </div>
</section>
