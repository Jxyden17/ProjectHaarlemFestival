<?php
use App\Models\Page\Section;

$section = $section ?? null;

if (!$section instanceof Section) {
    return;
}
?>

<section class="explore-section">
    <div class="explore-container">
        <h2 class="explore-title"><?= htmlspecialchars($section->title ?? '') ?></h2>
        <p class="explore-description"><?= htmlspecialchars($section->subTitle ?? '') ?></p>

        <div class="explore-grid">
            <?php foreach ($section->items as $item): ?>
                <div class="explore-card">
                    <?php if (trim((string)($item->image ?? '')) !== ''): ?>
                        <div class="explore-image">
                            <img src="<?= htmlspecialchars((string)($item->image ?? '')) ?>" alt="<?= htmlspecialchars((string)($item->title ?? '')) ?>">
                        </div>
                    <?php endif; ?>
                    <div class="explore-content">
                        <h3><?= htmlspecialchars((string)($item->title ?? '')) ?></h3>
                        <?php if (trim((string)($item->subTitle ?? '')) !== ''): ?>
                            <p class="explore-subtitle"><?= htmlspecialchars((string)($item->subTitle ?? '')) ?></p>
                        <?php endif; ?>
                        <p class="explore-text"><?= htmlspecialchars((string)($item->content ?? '')) ?></p>
                        <?php if (trim((string)($item->url ?? '')) !== ''): ?>
                            <a href="<?= htmlspecialchars((string)($item->url ?? '')) ?>" class="explore-btn">Explore</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($section->description): ?>
            <div class="explore-footer">
                <p><?= htmlspecialchars($section->description) ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>
