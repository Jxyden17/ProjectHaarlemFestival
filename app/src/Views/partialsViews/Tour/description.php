<section class="discover-section">
    <div class="discover-container">
        <h1 class="discover-title">
            <?= $section->title ?>
        </h1>
        
        <div class="discover-description">
            <?= $section->subTitle ?>
            <?= $section->description ?>
        </div>

        <div class="info-grid">
            <?php foreach ($section->getItemsByCategorie('grid') as $item): ?>
                <div class="info-item">
                    <div class="info-icon"><?= $item->image ?></div>
                    <div class="info-label"><?= $item->title ?></div>
                    <div class="info-value"><?= $item->content ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="pricing-info-row">
                <div class="pricing-section">
                    <h2 class="section-title">Prices</h2>
                    <?php foreach ($section->getItemsByCategorie('price') as $item): ?>
                    <div class="price-item">
                        <div class="price-label">
                            <span class="price-name"><?= $item->title ?></span>
                            <span class="price-subtitle"><?= $item->url ?></span>
                        </div>
                        <div class="price-value"><?= $item->content ?></div>
                    </div>
                    <?php endforeach; ?>
            </div>
            <div class="info-section">
                <h2 class="section-title">Important Information</h2>
                <ul class="info-list">
                <?php foreach($section->getItemsByCategorie('info') as $item): ?>
                    <li><?= $item->title ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>
