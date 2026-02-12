<section class="discover-section">
    <div class="discover-container">
        <h1 class="discover-title">
            <?= htmlspecialchars($section->title ?? '') ?>
        </h1>
        
        <div class="discover-description">
            <?= htmlspecialchars($section->subTitle ?? '') ?>
            <?= htmlspecialchars($section->description ?? '') ?>
        </div>

        <div class="info-grid">
            <?php foreach ($section->getItemsByCategorie('grid') as $item): ?>
                <div class="info-item">
                    <div class="info-icon"><?= htmlspecialchars($item->image) ?></div>
                    <div class="info-label"><?= htmlspecialchars($item->title) ?></div>
                    <div class="info-value"><?= htmlspecialchars($item->content) ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="pricing-info-row">
                <div class="pricing-section">
                    <h2 class="section-title">Prices</h2>
                    <?php foreach ($section->getItemsByCategorie('price') as $item): ?>
                    <div class="price-item">
                        <div class="price-label">
                            <span class="price-name"><?= htmlspecialchars($item->title) ?? '' ?></span>
                            <span class="price-subtitle"><?= htmlspecialchars($item->url) ?? '' ?></span>
                        </div>
                        <div class="price-value"><?= htmlspecialchars($item->content) ?? '' ?></div>
                    </div>
                    <?php endforeach; ?>
            </div>
            <div class="info-section">
                <h2 class="section-title">Important Information</h2>
                <ul class="info-list">
                <?php foreach($section->getItemsByCategorie('info') as $item): ?>
                    <li><?= htmlspecialchars($item->title) ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>