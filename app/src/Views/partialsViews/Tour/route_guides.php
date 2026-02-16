<section class="bottom-content-section">
    <div class="route-guides-row">
        
        <div class="route-container">
            <h2 class="section-title-alt"><?= htmlspecialchars($section->title) ?></h2>
            <p class="section-subtitle"><?= htmlspecialchars($section->subTitle) ?></p>
            <div class="map-wrapper">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d38964.15988079062!2d4.601593642506887!3d52.38383699756545!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c5ef6c60e1e9fb%3A0x8ae15680b8a17e39!2sHaarlem!5e0!3m2!1spl!2snl!4v1771002968860!5m2!1spl!2snl" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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