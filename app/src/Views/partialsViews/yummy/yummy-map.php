<?php if ($yummyIndexViewModel->map): ?>
    <?php $section = $yummyIndexViewModel->map; ?>
    <?php foreach ($section->items as $item): ?>
        <section class="yummy-map-section">
            <div class="yummy-container">
                <div class="yummy-map-card">
                    <div class="map-header">
                        <h2><?= htmlspecialchars($section->title) ?></h2>
                        <span class="map-tag"><?= htmlspecialchars($item->title) ?></span>
                    </div>
                    <div class="map-container">
                        <iframe src="<?= htmlspecialchars($item->content) ?>" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                    <p class="map-description"><?= htmlspecialchars($item->subTitle) ?></p>
                </div>
            </div>
        </section>
    <?php endforeach; ?>
<?php endif; ?>