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

$resolveStoryUrl = static function (?string $url): string {
    $value = trim((string)($url ?? ''));
    if ($value === '') {
        return '';
    }

    if (
        preg_match('#^(?:https?:)?//#i', $value) === 1
        || str_starts_with($value, '/')
        || str_starts_with($value, '#')
        || str_starts_with($value, 'mailto:')
        || str_starts_with($value, 'tel:')
    ) {
        return $value;
    }

    return '/stories/' . ltrim($value, '/');
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
<<<<<<< HEAD
                    <?php $itemUrl = $resolveStoryUrl($item->url ?? ''); ?>
                    <?php if ($itemUrl !== ''): ?>
                        <a href="<?= htmlspecialchars($itemUrl) ?>" class="view-profile-btn">View Profile</a>
                    <?php endif; ?>
=======
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
>>>>>>> e546708a4f41b4d19a79d29e644b39d8287b434c
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
