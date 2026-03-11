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

        <p class="hero-description">
            <?= htmlspecialchars($section->description) ?>
        </p>

    </div>

</section>