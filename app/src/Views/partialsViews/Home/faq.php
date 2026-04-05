<?php
use App\Models\Page\Section;
$section = $section ?? null;

if (!$section instanceof Section) {
    return;
}
?>
<section class="home-faq-section">
    <div class="container faq-box">
        <h2 class="faq-title"><?= htmlspecialchars($section->title ?? 'Frequently Asked Questions') ?></h2>

        <div class="faq-list">
            <?php foreach ($section->getItemsByCategorie('faq') as $item): ?>
                <details class="faq-item">
                    <summary class="faq-question"><?= htmlspecialchars($item->title) ?></summary>
                    <div class="faq-answer"><?= htmlspecialchars($item->content) ?></div>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>