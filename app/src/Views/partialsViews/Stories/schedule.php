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

<section class="schedule-section">
    <div class="schedule-container">
        <h2 class="schedule-title"><?= $renderInlineRichText($section->title ?? null) ?></h2>
        <div class="schedule-description"><?= $renderBlockRichText($section->subTitle ?? null) ?></div>

        <div class="schedule-list">
            <?php foreach ($section->items as $item): ?>
                <div class="schedule-item">
                    <div class="schedule-details">
                        <h4 class="event-title"><?= $renderInlineRichText($item->title ?? null) ?></h4>
                        <div class="event-content"><?= $renderBlockRichText($item->content ?? null) ?></div>
                        <?php if (trim((string)($item->image ?? '')) !== ''): ?>
                            <img src="<?= htmlspecialchars((string)($item->image ?? '')) ?>" alt="<?= htmlspecialchars((string)($item->title ?? '')) ?>" class="schedule-thumb">
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
