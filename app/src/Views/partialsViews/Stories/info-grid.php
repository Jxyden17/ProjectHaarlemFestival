<section class="section-wrapper">
    <h2 class="main-grid-title"><?= htmlspecialchars((string)($section->title ?? '')) ?></h2>
    
    <div class="three-column-grid">
        <?php foreach ($section->items as $item): ?>
            <div class="grid-item-card">
                <div class="image-box">
                    <img src="<?= htmlspecialchars((string)($item->image ?? '')) ?>" alt="<?= htmlspecialchars((string)($item->title ?? '')) ?>">
                </div>
                
                <div class="text-box">
                    <h3 class="item-title"><?= htmlspecialchars((string)($item->title ?? '')) ?></h3>
                    <div class="item-content">
                        <?= htmlspecialchars((string)($item->content ?? '')) ?>
                    </div>
                    <?php if (trim((string)($item->url ?? '')) !== ''): ?>
                        <a href="<?= htmlspecialchars((string)($item->url ?? '')) ?>" class="view-profile-btn">View Profile</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
