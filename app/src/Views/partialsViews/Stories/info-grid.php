<section class="section-wrapper">
    <h2 class="main-grid-title"><?= htmlspecialchars($section->title) ?></h2>
    
    <div class="three-column-grid">
        <?php foreach ($section->items as $item): ?>
            <div class="grid-item-card">
                <div class="image-box">
                    <img src="<?= htmlspecialchars($item->image) ?>" alt="<?= htmlspecialchars($item->title) ?>">
                </div>
                
                <div class="text-box">
                    <h3 class="item-title"><?= htmlspecialchars($item->title) ?></h3>
                    <div class="item-content">
                        <?= htmlspecialchars($item->content) ?>
                    </div>
                    <?php if ($item->url): ?>
                    <?php
                    $profileUrl = $item->url;
                        if ($item->title === 'Mister Anansi') {
                            $profileUrl = '/stories/details?slug=mister-anansi';
                        } elseif ($item->title === 'Omdenken Podcast') {
                            $profileUrl = '/stories/details?slug=omdenken-podcast';
                        } elseif ($item->title === 'Corrie ten Boom') {
                            $profileUrl = '/stories/details?slug=corrie-ten-boom';
                        }
                        ?>
                    <a href="<?= htmlspecialchars($profileUrl) ?>" class="view-profile-btn">View Profile</a>
                <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
