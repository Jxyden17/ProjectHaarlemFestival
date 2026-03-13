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
                <?php
                $itemTitle = trim((string) ($item->title ?? ''));
                $itemUrl = trim((string) ($item->url ?? ''));

                if ($itemTitle === 'Yummy!') {
                    $itemUrl = '/yummy';
                } elseif ($itemTitle === 'Haarlem Jazz') {
                    $itemUrl = '/jazz';
                }
                ?>
                <div class="explore-card">
                    <?php if ($item->image): ?>
                        <div class="explore-image">
                            <img src="<?= htmlspecialchars($item->image) ?>" alt="<?= htmlspecialchars($item->title) ?>">
                        </div>
                    <?php endif; ?>
                    <div class="explore-content">
                        <h3><?= htmlspecialchars($item->title) ?></h3>
                        <?php if ($item->subTitle): ?>
                            <p class="explore-subtitle"><?= htmlspecialchars($item->subTitle) ?></p>
                        <?php endif; ?>
                        <p class="explore-text"><?= htmlspecialchars($item->content) ?></p>
                        <?php if ($itemUrl !== ''): ?>
                            <a href="<?= htmlspecialchars($itemUrl) ?>" class="explore-btn">Explore</a>
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
