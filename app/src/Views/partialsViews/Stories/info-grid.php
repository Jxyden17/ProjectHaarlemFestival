<?php
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
<section class="section-wrapper">
    <h2 class="main-grid-title"><?= $renderInlineRichText($section->title ?? null) ?></h2>
    
    <div class="three-column-grid">
        <?php foreach ($section->items as $item): ?>
            <div class="grid-item-card">
                <div class="image-box">
                    <img src="<?= htmlspecialchars((string)($item->image ?? '')) ?>" alt="<?= htmlspecialchars((string)($item->title ?? '')) ?>">
                </div>
                
                <div class="text-box">
                    <h3 class="item-title"><?= $renderInlineRichText($item->title ?? null) ?></h3>
                    <div class="item-content">
                        <?= $renderBlockRichText($item->content ?? null) ?>
                    </div>
                    <?php if (trim((string)($item->url ?? '')) !== ''): ?>
                        <a href="<?= htmlspecialchars((string)($item->url ?? '')) ?>" class="view-profile-btn">View Profile</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
