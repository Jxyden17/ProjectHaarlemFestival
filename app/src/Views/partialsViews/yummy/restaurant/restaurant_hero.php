<section class="restaurant-hero">

    <div class="hero-image">

        <?php foreach ($section->items as $item): ?>
            <?php if (!empty($item->image)): ?>
                <img src="<?= htmlspecialchars($item->image) ?>" alt="">
            <?php endif; ?>
        <?php endforeach; ?>

    </div>

    <div class="hero-info">
        <h1><?= htmlspecialchars($section->title) ?></h1>

        <?php foreach ($section->items as $item): ?>

            <p class="restaurant-stars">
                <?= htmlspecialchars($item->icon) ?>
            </p>

            <p class="restaurant-content">
                <?= htmlspecialchars($item->content) ?>
            </p>

        <?php endforeach; ?>
    </div>

</section>