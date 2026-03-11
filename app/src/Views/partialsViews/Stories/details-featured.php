<?php $featuredItems = $section->items ?? []; ?>

<?php if (!empty($featuredItems)): ?>
    <section class="story-section">
        <h2 class="story-section-title"><?= htmlspecialchars((string) ($section->title ?? 'Featured Stories')) ?></h2>
        <div class="story-featured-list">
            <?php foreach ($featuredItems as $item): ?>
                <article class="story-feature-card">
                    <div class="story-feature-copy">
                        <h3><?= htmlspecialchars((string) ($item->title ?? '')) ?></h3>
                        <p><?= htmlspecialchars((string) ($item->content ?? '')) ?></p>
                        <div class="story-waveform" aria-hidden="true">
                            <?php for ($i = 0; $i < 48; $i++): ?>
                                <span></span>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <a class="story-feature-button" href="<?= htmlspecialchars((string) ($item->url ?? '#')) ?>">
                        <?= htmlspecialchars((string) (($item->category ?? '') !== '' ? $item->category : 'Listen')) ?>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
