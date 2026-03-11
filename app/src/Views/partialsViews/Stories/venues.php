<?php
use App\Models\Page\Section;

$section = $section ?? null;

if (!$section instanceof Section) {
    return;
}
?>

<section class="venues-section">
    <div class="venues-container">
        <h2 class="venues-title"><?= htmlspecialchars($section->title ?? '') ?></h2>
        <p class="venues-subtitle"><?= htmlspecialchars($section->subTitle ?? '') ?></p>

        <div class="venues-grid">
            <?php foreach ($section->items as $item): ?>
                <div class="venue-card">
                    <div class="venue-header">
                        <h3><?= htmlspecialchars($item->title) ?></h3>
                    </div>

                    <div class="venue-description">
                        <p><?= htmlspecialchars($item->content) ?></p>
                    </div>

                    <?php if ($item->category): ?>
                        <div class="venue-tags">
                            <?php 
                            $tags = array_filter(array_map('trim', explode(',', $item->category)));
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
