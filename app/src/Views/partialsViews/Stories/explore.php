<?php
use App\Models\Page\Section;

$section = $section ?? null;

if (!$section instanceof Section) {
    return;
}

$renderInlineRichText = static function (?string $html, string $fallback = ''): string {
    $value = trim((string)($html ?? ''));
    if ($value === '') {
        return htmlspecialchars($fallback);
    }

    $value = preg_replace('/^\s*<p>(.*)<\/p>\s*$/is', '$1', $value) ?? $value;
    return strip_tags($value, '<strong><em><u><a><br>');
};

$renderBlockRichText = static function (?string $html): string {
    $value = trim((string)($html ?? ''));
    return $value === '' ? '' : $value;
};
?>

<section class="explore-section">
    <div class="explore-container">
        <h2 class="explore-title"><?= $renderInlineRichText($section->title ?? null) ?></h2>
        <div class="explore-description"><?= $renderBlockRichText($section->subTitle ?? null) ?></div>

        <div class="explore-grid">
            <?php foreach ($section->items as $item): ?>
                <div class="explore-card">
                    <?php if (trim((string)($item->image ?? '')) !== ''): ?>
                        <div class="explore-image">
                            <img src="<?= htmlspecialchars((string)($item->image ?? '')) ?>" alt="<?= htmlspecialchars((string)($item->title ?? '')) ?>">
                        </div>
                    <?php endif; ?>
                    <div class="explore-content">
                        <h3><?= $renderInlineRichText($item->title ?? null) ?></h3>
                        <?php if (trim((string)($item->subTitle ?? '')) !== ''): ?>
                            <div class="explore-subtitle"><?= $renderBlockRichText($item->subTitle ?? null) ?></div>
                        <?php endif; ?>
                        <div class="explore-text"><?= $renderBlockRichText($item->content ?? null) ?></div>
                        <?php $itemUrl = $resolveStoryUrl($item->url ?? ''); ?>
                        <?php if ($itemUrl !== ''): ?>
                            <a href="<?= htmlspecialchars($itemUrl) ?>" class="explore-btn">Explore</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($section->description): ?>
            <div class="explore-footer">
                <div><?= $renderBlockRichText($section->description ?? null) ?></div>
            </div>
        <?php endif; ?>
    </div>
</section>
