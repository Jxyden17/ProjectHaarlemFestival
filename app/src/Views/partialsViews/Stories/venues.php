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

<section class="venues-section">
    <div class="venues-container">
        <h2 class="venues-title"><?= $renderInlineRichText($section->title ?? null) ?></h2>
        <div class="venues-subtitle"><?= $renderBlockRichText($section->subTitle ?? null) ?></div>

        <div class="venues-grid">
            <?php foreach ($section->items as $item): ?>
                <div class="venue-card">
                    <div class="venue-header">
                        <span class="venue-pin" aria-hidden="true"></span>
                        <div class="venue-heading-copy">
                            <h3><?= $renderInlineRichText($item->title ?? null) ?></h3>
                            <?php if (trim((string)($item->subTitle ?? '')) !== ''): ?>
                                <div class="venue-address"><?= $renderBlockRichText($item->subTitle ?? null) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="venue-description">
                        <div><?= $renderBlockRichText($item->content ?? null) ?></div>
                    </div>

                    <?php if (trim((string)($item->category ?? '')) !== ''): ?>
                        <div class="venue-tags">
                            <?php 
                            $tags = array_filter(array_map('trim', explode(',', (string)($item->category ?? ''))));
                            foreach ($tags as $tag): 
                            ?>
                                <span class="tag"><?= htmlspecialchars($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
