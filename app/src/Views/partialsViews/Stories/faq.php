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

<section class="faq-section">
    <div class="faq-container">
        <h2 class="faq-title"><?= $renderInlineRichText($section->title ?? null) ?></h2>
        <div class="faq-description"><?= $renderBlockRichText($section->subTitle ?? null) ?></div>

        <div class="faq-list">
            <?php foreach ($section->items as $item): ?>
                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFAQ(this)">
                        <span class="faq-q"><?= $renderInlineRichText($item->title ?? null) ?></span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <div><?= $renderBlockRichText($item->content ?? null) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
function toggleFAQ(button) {
    const answer = button.nextElementSibling;
    const icon = button.querySelector('.faq-icon');
    
    if (answer.style.display === 'block') {
        answer.style.display = 'none';
        icon.textContent = '+';
    } else {
        answer.style.display = 'block';
        icon.textContent = '−';
    }
}
</script>
