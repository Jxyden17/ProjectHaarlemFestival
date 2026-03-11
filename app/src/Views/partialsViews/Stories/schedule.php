<?php
use App\Models\Page\Section;

$section = $section ?? null;

if (!$section instanceof Section) {
    return;
}
?>

<section class="schedule-section">
    <div class="schedule-container">
        <h2 class="schedule-title"><?= htmlspecialchars($section->title ?? '') ?></h2>
        <p class="schedule-description"><?= htmlspecialchars($section->subTitle ?? '') ?></p>

        <div class="schedule-list">
            <?php foreach ($section->items as $item): ?>
                <div class="schedule-item">
                    <div class="schedule-details">
                        <h4 class="event-title"><?= htmlspecialchars($item->title) ?></h4>
                        <p class="event-content"><?= htmlspecialchars($item->content) ?></p>
                        <?php if ($item->image): ?>
                            <img src="<?= htmlspecialchars($item->image) ?>" alt="<?= htmlspecialchars($item->title) ?>" class="schedule-thumb">
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
