<?php
use App\Models\Page\Section;
use App\Models\Page\SectionItem;

$danceArtistsSection = $danceArtistsSection ?? null;

if (!$danceArtistsSection instanceof Section) {
    return;
}

$artistsTitle = $danceArtistsSection->title;
$artists = $danceArtistsSection->getItemsByCategorie('artist');
?>

<section class="dance-artists">
    <div class="dance-artists-inner">
        <h2 class="dance-artists-title">
            <span class="dance-artists-title-icon">
                <i data-lucide="music-4" aria-hidden="true"></i>
            </span>
            <?= htmlspecialchars($artistsTitle) ?>
        </h2>

        <div class="dance-artists-grid">
            <?php foreach ($artists as $artist): ?>
                <?php
                if (!$artist instanceof SectionItem) {
                    continue;
                }

                $name = trim($artist->title);
                if ($name === '') {
                    continue;
                }
                $genre = (string)($artist->content ?? '');
                $image = trim((string)($artist->image ?? ''));
                if ($image === '') {
                    continue;
                }
                ?>
                <article class="dance-artist-card">
                    <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($name) ?>">
                    <div class="dance-artist-card-content">
                        <span class="dance-artist-card-genre"><?= htmlspecialchars($genre) ?></span>
                        <h3 class="dance-artist-card-name"><?= htmlspecialchars($name) ?></h3>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
