<?php
$artistsTitle = trim((string)($danceIndexViewModel->artistsTitle ?? ''));
$artistCards = is_array($danceIndexViewModel->artistCards ?? null) ? $danceIndexViewModel->artistCards : [];
?>

<section class="dance-artists" id="dance-featured-artists">
    <div class="dance-artists-inner">
        <h2 class="dance-artists-title">
            <span class="dance-artists-title-icon">
                <i data-lucide="music-4" aria-hidden="true"></i>
            </span>
            <?= htmlspecialchars($artistsTitle) ?>
        </h2>

        <div class="dance-artists-grid">
            <?php foreach ($artistCards as $artistCard): ?>
                <?php
                $name = trim((string)($artistCard['name'] ?? ''));
                $genre = trim((string)($artistCard['genre'] ?? ''));
                $image = trim((string)($artistCard['image'] ?? ''));
                $detailUrl = trim((string)($artistCard['detailUrl'] ?? ''));
                if ($name === '' || $image === '') {
                    continue;
                }
                ?>
                <?php if ($detailUrl !== ''): ?>
                    <a class="dance-artist-card-link" href="<?= htmlspecialchars($detailUrl) ?>" aria-label="View details for <?= htmlspecialchars($name) ?>">
                        <article class="dance-artist-card">
                            <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($name) ?>">
                            <div class="dance-artist-card-content">
                                <span class="dance-artist-card-genre"><?= htmlspecialchars($genre) ?></span>
                                <h3 class="dance-artist-card-name"><?= htmlspecialchars($name) ?></h3>
                            </div>
                        </article>
                    </a>
                <?php else: ?>
                    <article class="dance-artist-card">
                        <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($name) ?>">
                        <div class="dance-artist-card-content">
                            <span class="dance-artist-card-genre"><?= htmlspecialchars($genre) ?></span>
                            <h3 class="dance-artist-card-name"><?= htmlspecialchars($name) ?></h3>
                        </div>
                    </article>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
