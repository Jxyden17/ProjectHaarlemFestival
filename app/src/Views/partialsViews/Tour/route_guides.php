<section class="bottom-content-section">
    <div class="route-guides-row">
        
        <div class="route-container">
            <h2 class="section-title-alt"><?= htmlspecialchars($section->title) ?></h2>
            <p class="section-subtitle"><?= htmlspecialchars($section->subTitle) ?></p>
            <div class="map-wrapper">
                <!-- <div id="map"src="https://maps.googleapis.com/maps/api/js?key=<?php echo getenv('GOOGLE_MAPS_API_KEY'); ?>&callback=initMap">></div> -->
            </div>
        </div>

        <div class="guides-container">
            <h2 class="section-title-alt">Meet Your Guide</h2>
            <p class="guide-intro"><?= htmlspecialchars($section->description) ?>
            </p>
            <div class="guides-grid">
                <?php foreach($section->getItemsByCategorie('guide') as $item): ?>
                <div class="guide-card">
                    <img src="<?= htmlspecialchars($item->image) ?>" alt="<?= htmlspecialchars($item->title) ?>" class="guide-img">
                    <h4><?= htmlspecialchars($item->title) ?></h4>
                    <p class="guide-role"><?= htmlspecialchars($item->subTitle) ?></p>
                    <p class="guide-desc"><?= htmlspecialchars($item->content) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>