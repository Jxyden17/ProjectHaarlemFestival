<?php
// Custom hero for stories pages. Reuse generic data but can add story-specific classes
use App\Models\Page\Section;

$section = $section ?? null;
if (!$section instanceof Section) {
    return;
}

$items = is_array($section->items) ? $section->items : [];
$firstItem = $items[0] ?? null;
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
        <h1><?= htmlspecialchars($section->title) ?></h1>
        <p><?= htmlspecialchars($firstItem->content ?? '') ?></p>
    </div>
</section>
