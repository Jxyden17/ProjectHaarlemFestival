<?php
use App\Models\Page\Section;
use App\Models\Page\SectionItem;
use App\Models\VenueModel;

$venues = $venues ?? [];
$danceInfoSection = $danceInfoSection ?? null;
$dancePassesSection = $dancePassesSection ?? null;
$danceCapacitySection = $danceCapacitySection ?? null;
$danceSpecialSection = $danceSpecialSection ?? null;

if (!$danceInfoSection instanceof Section || !$dancePassesSection instanceof Section || !$danceCapacitySection instanceof Section || !$danceSpecialSection instanceof Section) {
    return;
}

$importantInformationTitle = $danceInfoSection->title;
$importantInformationHtml = (string)$danceInfoSection->description;
$passesTitle = $dancePassesSection->title;
$passes = $dancePassesSection->getItemsByCategorie('pass');
$capacityTitle = $danceCapacitySection->title;
$capacityHtml = (string)$danceCapacitySection->description;
$specialTitle = $danceSpecialSection->title;
$specialHtml = (string)$danceSpecialSection->description;
?>

<section class="dance-event-info">
    <div class="dance-event-info-inner">
        <div class="dance-important-card">
            <h2 class="dance-important-title">
                <span class="dance-important-icon">
                    <i data-lucide="info" aria-hidden="true"></i>
                </span>
                <?= htmlspecialchars($importantInformationTitle) ?>
            </h2>
            <div class="dance-important-list"><?= $importantInformationHtml ?></div>
        </div>

        <div class="dance-info-cards-grid">
            <article class="dance-info-card">
                <h3 class="dance-info-card-title">
                    <span class="dance-info-card-icon">
                        <i data-lucide="calendar-days" aria-hidden="true"></i>
                    </span>
                    <?= htmlspecialchars($passesTitle) ?>
                </h3>
                <?php foreach ($passes as $pass): ?>
                    <?php
                    if (!$pass instanceof SectionItem) {
                        continue;
                    }

                    $label = trim($pass->title);
                    $price = trim((string)($pass->content ?? ''));
                    if ($label === '' || $price === '') {
                        continue;
                    }
                    $rowClass = (string)($pass->url ?? '') === 'highlight' ? 'dance-pass-row dance-pass-row-highlight' : 'dance-pass-row';
                    ?>
                    <div class="<?= htmlspecialchars($rowClass) ?>">
                        <span><?= htmlspecialchars($label) ?></span>
                        <strong><?= htmlspecialchars($price) ?></strong>
                    </div>
                <?php endforeach; ?>
            </article>

            <article class="dance-info-card dance-info-card-accent">
                <h3 class="dance-info-card-title">
                    <span class="dance-info-card-icon dance-info-card-icon-warn">
                        <i data-lucide="alert-triangle" aria-hidden="true"></i>
                    </span>
                    <?= htmlspecialchars($capacityTitle) ?>
                </h3>
                <div class="dance-info-list"><?= $capacityHtml ?></div>
            </article>

            <article class="dance-info-card">
                <h3 class="dance-info-card-title">
                    <span class="dance-info-card-icon dance-info-card-icon-music">
                        <i data-lucide="music-2" aria-hidden="true"></i>
                    </span>
                    <?= htmlspecialchars($specialTitle) ?>
                </h3>
                <div class="dance-info-list"><?= $specialHtml ?></div>
            </article>
        </div>

        <div class="dance-venues-card">
            <h3 class="dance-info-card-title">
                <span class="dance-info-card-icon dance-info-card-icon-venue">
                    <i data-lucide="map-pin" aria-hidden="true"></i>
                </span>
                Venues
            </h3>

            <div class="dance-venues-grid">
                <?php foreach ($venues as $venue): ?>
                    <div>
                        <?php if ($venue instanceof VenueModel): ?>
                            <h4><?= htmlspecialchars($venue->venueName) ?></h4>
                            <p><?= htmlspecialchars($venue->address ?? 'Address unavailable') ?></p>
                        <?php else: ?>
                            <h4>Unknown venue</h4>
                            <p>Address unavailable</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
