<?php
$heroItems = $section->items ?? [];
$heroImage = $section->getFirstItemImage('image');
$heroTags = $section->getItemsByCategorie('tag');
?>

<section class="story-hero">
    <?php if (!empty($heroImage)): ?>
        <div class="story-hero-image-wrap">
            <img
                class="story-hero-image"
                src="<?= htmlspecialchars((string) $heroImage) ?>"
                alt="<?= htmlspecialchars((string) ($pageTitle ?? 'Story')) ?>"
            >
        </div>
    <?php endif; ?>

    <div class="story-hero-copy">
        <h1 class="story-title"><?= htmlspecialchars((string) ($pageTitle ?? '')) ?></h1>
        <?php if (!empty($heroTags)): ?>
            <div class="story-tag-list">
                <?php foreach ($heroTags as $tag): ?>
                    <span class="story-tag"><?= htmlspecialchars((string) $tag->title) ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
