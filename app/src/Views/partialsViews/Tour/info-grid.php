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
                        <?= $item->content ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>