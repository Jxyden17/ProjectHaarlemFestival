<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle ?? 'Story Details') ?></title>
    <link href="/css/Stories/details.css" rel="stylesheet">
</head>
<body>
    <?php if (!empty($story)): ?>
        <main class="story-detail-page">
            <div class="story-detail-shell">
                <a class="story-back-link" href="<?= htmlspecialchars($story['backUrl']) ?>">
                    &larr; <?= htmlspecialchars($story['backLabel']) ?>
                </a>

                <section class="story-hero">
                    <div class="story-hero-image-wrap">
                        <img
                            class="story-hero-image"
                            src="<?= htmlspecialchars($story['heroImage']) ?>"
                            alt="<?= htmlspecialchars($story['title']) ?>"
                        >
                    </div>

                    <div class="story-hero-copy">
                        <h1 class="story-title"><?= htmlspecialchars($story['title']) ?></h1>
                        <?php if (!empty($story['tags'])): ?>
                            <div class="story-tag-list">
                                <?php foreach ($story['tags'] as $tag): ?>
                                    <span class="story-tag"><?= htmlspecialchars($tag) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

                <?php if (!empty($story['galleryImages'])): ?>
                    <section class="story-section">
                        <h2 class="story-section-title"><?= htmlspecialchars($story['galleryTitle']) ?></h2>
                        <div class="story-gallery-grid">
                            <?php foreach ($story['galleryImages'] as $image): ?>
                                <div class="story-gallery-card">
                                    <img class="story-gallery-image" src="<?= htmlspecialchars($image) ?>" alt="">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if (!empty($story['aboutParagraphs'])): ?>
                    <section class="story-section story-section--text">
                        <h2 class="story-section-title"><?= htmlspecialchars($story['aboutTitle']) ?></h2>
                        <div class="story-copy">
                            <?php foreach ($story['aboutParagraphs'] as $paragraph): ?>
                                <p><?= htmlspecialchars($paragraph) ?></p>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if (!empty($story['featuredItems'])): ?>
                    <section class="story-section">
                        <h2 class="story-section-title"><?= htmlspecialchars($story['featuredTitle']) ?></h2>
                        <div class="story-featured-list">
                            <?php foreach ($story['featuredItems'] as $item): ?>
                                <article class="story-feature-card">
                                    <div class="story-feature-copy">
                                        <h3><?= htmlspecialchars($item['title']) ?></h3>
                                        <p><?= htmlspecialchars($item['subtitle']) ?></p>
                                        <div class="story-waveform" aria-hidden="true">
                                            <?php for ($i = 0; $i < 48; $i++): ?>
                                                <span></span>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <a class="story-feature-button" href="<?= htmlspecialchars($item['buttonUrl'] ?? '#') ?>">
                                        <?= htmlspecialchars($item['buttonLabel']) ?>
                                    </a>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if (!empty($story['booking'])): ?>
                    <section class="story-section">
                        <h2 class="story-section-title"><?= htmlspecialchars($story['bookingTitle']) ?></h2>
                        <article class="story-booking-card">
                            <div class="story-booking-datetime"><?= htmlspecialchars($story['booking']['datetime']) ?></div>
                            <div class="story-booking-location"><?= htmlspecialchars($story['booking']['location']) ?></div>

                            <?php if (!empty($story['booking']['tags'])): ?>
                                <div class="story-tag-list story-tag-list--booking">
                                    <?php foreach ($story['booking']['tags'] as $tag): ?>
                                        <span class="story-tag story-tag--muted"><?= htmlspecialchars($tag) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="story-booking-price-label"><?= htmlspecialchars($story['booking']['priceLabel']) ?></div>
                            <div class="story-booking-price"><?= htmlspecialchars($story['booking']['price']) ?></div>

                            <a class="story-booking-button" href="<?= htmlspecialchars($story['booking']['buttonUrl']) ?>">
                                <?= htmlspecialchars($story['booking']['buttonLabel']) ?>
                            </a>
                        </article>
                    </section>
                <?php endif; ?>
            </div>
        </main>
    <?php endif; ?>
</body>
</html>
