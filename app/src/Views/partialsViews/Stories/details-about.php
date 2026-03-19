<?php $aboutItems = $section->items ?? []; ?>

<?php if (!empty($aboutItems)): ?>
    <section class="story-section story-section--text">
        <h2 class="story-section-title"><?= htmlspecialchars((string) ($section->title ?? 'About')) ?></h2>
        <div class="story-copy">
            <?php foreach ($aboutItems as $item): ?>
                <?php if (!empty($item->content)): ?>
                    <p><?= htmlspecialchars((string) $item->content) ?></p>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
