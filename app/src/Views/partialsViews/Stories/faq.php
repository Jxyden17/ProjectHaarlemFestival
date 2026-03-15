<?php
use App\Models\Page\Section;

$section = $section ?? null;

if (!$section instanceof Section) {
    return;
}
?>

<section class="faq-section">
    <div class="faq-container">
        <h2 class="faq-title"><?= htmlspecialchars($section->title ?? '') ?></h2>
        <p class="faq-description"><?= htmlspecialchars($section->subTitle ?? '') ?></p>

        <div class="faq-list">
            <?php foreach ($section->items as $item): ?>
                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFAQ(this)">
                        <span class="faq-q"><?= htmlspecialchars($item->title) ?></span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p><?= htmlspecialchars($item->content) ?></p>
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
