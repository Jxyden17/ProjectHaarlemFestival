<section class="yummy-page">
    <div class="yummy-hero">
        <div class="yummy-container">
            <?php foreach ($section->items as $item): ?>
                <?php if (!empty($item->image)): ?>
                    <img class="yummy-banner"
                        src="<?= $item->image ?>" alt="">
                <?php endif; ?>
            <?php endforeach; ?>

            <h1 class="yummy-title"><?= $section->title ?></h1>

            <div class="yummy-subtitle">
                <?= $section->description ?>
            </div>
        </div>
    </div>
</section>