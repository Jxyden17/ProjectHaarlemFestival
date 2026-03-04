<?php
use App\Models\Page\Section;

$section = $section ?? null;

if (!$section instanceof Section) {
    return;
}

$items = is_array($section->items) ? $section->items : [];
$firstItem = $items[0] ?? null;
?>

<section class="hero-section">
    <div class="hero-images">
        <?php foreach ($items as $item): ?>
            <div class="hero-img-item">
                <img src="<?= htmlspecialchars($item->image) ?>" alt="<?= htmlspecialchars($item->title); ?>">
            </div>
            <?php endforeach; ?>
    </div>

    <div class="hero-overlay"></div>

    <div class="hero-text">
        <h1><?= htmlspecialchars($section->title) ?></h1>
        <p><?= htmlspecialchars($firstItem->content ?? '') ?></p>
    </div>
</section>
