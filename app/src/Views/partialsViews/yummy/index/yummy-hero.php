<section class="yummy-page">
    <div class="yummy-hero">
        <div class="yummy-container">
            <?php foreach ($section->items as $item): ?>
                <?php if (!empty($item->image)): ?>
                    <img class="yummy-banner"
                        src="<?= htmlspecialchars($item->image) ?>" alt="">
                    </img>
                <?php endif; ?>
            <?php endforeach; ?>

            <h1 class="yummy-title"><?= htmlspecialchars($section->title) ?></h1>

            <p class="yummy-subtitle"><?= htmlspecialchars($section->description) ?></p>
        </div>
    </div>
</section>