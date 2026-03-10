<?php
// Custom hero for stories pages. Reuse generic data but can add story-specific classes
use App\Models\Page\Section;

$section = $section ?? null;
if (!$section instanceof Section) {
    return;
}

$items = is_array($section->items) ? $section->items : [];
$firstItem = $items[0] ?? null;
$secondItem = $items[1] ?? null;
?>

<section class="stories-hero-section">
    <div class="stories-hero-images">
        <?php foreach ($items as $item): ?>
            <div class="stories-hero-img-item">
                <img src="<?= htmlspecialchars($item->image) ?>" alt="<?= htmlspecialchars($item->title); ?>">
            </div>
        <?php endforeach; ?>
    </div>

    <div class="stories-hero-overlay"></div>

    <div class="stories-hero-text">
        <div class="hero-badge">
            <span class="hero-badge-icon">📅</span>
            <span class="hero-badge-text">This Weekend • July 24-27, 2025</span>
        </div>

        <h1><?= htmlspecialchars($section->title) ?></h1>
        <p><?= htmlspecialchars($firstItem->content ?? '') ?></p>
        <p><?= htmlspecialchars($secondItem->content ?? '') ?></p>

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
