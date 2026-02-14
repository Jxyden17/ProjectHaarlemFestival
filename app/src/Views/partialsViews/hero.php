<link rel="stylesheet" href="/css/partialViews/hero.css">
<section class="hero-section">
    <div class="hero-images">
        <?php foreach ($section->items as $item): ?>
            <div class="hero-img-item">
                <img src="<?= htmlspecialchars($item->image) ?>" alt="<?= htmlspecialchars($item->title); ?>">
            </div>
            <?php endforeach; ?>
    </div>

    <div class="hero-overlay"></div>

    <div class="hero-text">
        <h1><?= htmlspecialchars($section->title) ?></h1>
        <p><?= htmlspecialchars($section->items[0]->content ?? '') ?></p>
    </div>
</section>