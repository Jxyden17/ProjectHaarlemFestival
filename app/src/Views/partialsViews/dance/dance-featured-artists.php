<?php
use App\Models\Page\Section;
use App\Models\Page\SectionItem;
use App\Models\PerformerModel;

$danceArtistsSection = $danceArtistsSection ?? null;
$dancePerformers = is_array($dancePerformers ?? null) ? $dancePerformers : [];

if (!$danceArtistsSection instanceof Section) {
    return;
}

$artistsTitle = $danceArtistsSection->title;
$artistImageRows = array_values(array_filter(
    $danceArtistsSection->getItemsByCategorie('artist'),
    static fn($item) => $item instanceof SectionItem
));
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
            <?php foreach ($dancePerformers as $index => $performer): ?>
                <?php
                if (!$performer instanceof PerformerModel) {
                    continue;
                }

                $name = trim($performer->performerName);
                if ($name === '') {
                    continue;
                }
                $genre = trim((string)($performer->description ?? ''));
                if ($genre === '') {
                    $genre = 'DJ';
                }
                $imageRow = $artistImageRows[$index] ?? null;
                $image = $imageRow instanceof SectionItem ? trim((string)($imageRow->image ?? '')) : '';
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
