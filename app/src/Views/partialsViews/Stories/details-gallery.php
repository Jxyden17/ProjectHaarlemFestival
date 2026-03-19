<?php $galleryItems = $section->items ?? []; ?>

<?php if (!empty($galleryItems)): ?>
    <section class="story-section">
        <h2 class="story-section-title"><?= htmlspecialchars((string) ($section->title ?? 'Gallery')) ?></h2>
        <div class="story-gallery-grid">
            <?php foreach ($galleryItems as $item): ?>
                <?php if (!empty($item->image)): ?>
                    <div class="story-gallery-card">
                        <img class="story-gallery-image" src="<?= htmlspecialchars((string) $item->image) ?>" alt="">
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
