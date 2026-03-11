<link rel="stylesheet" href="/css/partialViews/hero.css">
<section class="hero-section">
    <div class="hero-images">
        <?php foreach($section->getItemsByCategorie('hero') as $item): ?>
            <div class="hero-img-item">
                <img src="<?= htmlspecialchars($item->image) ?>" alt="<?= htmlspecialchars($item->title); ?>">
            </div>
    </div>

    <div class="hero-overlay"></div>

    <div class="hero-text">
        <h1><?= htmlspecialchars($item->title) ?></h1>
        <p><?= htmlspecialchars($item->content ?? '') ?></p>
        <p><?= htmlspecialchars($item->subTitle ?? '') ?></p>
        <a href="" class="btn btn-primary">Explore Events</a>
    </div>
    <?php endforeach; ?>
</section>
