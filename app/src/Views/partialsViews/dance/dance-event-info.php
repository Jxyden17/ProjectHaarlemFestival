<?php
use App\Models\Event\VenueModel;

$venues = $venues ?? [];
$importantInformationTitle = trim((string)($danceIndexViewModel->importantInfoTitle ?? ''));
$importantInformationHtml = (string)($danceIndexViewModel->importantInfoHtml ?? '');
$passesTitle = trim((string)($danceIndexViewModel->passesTitle ?? ''));
$passes = is_array($danceIndexViewModel->passes ?? null) ? $danceIndexViewModel->passes : [];
$capacityTitle = trim((string)($danceIndexViewModel->capacityTitle ?? ''));
$capacityHtml = (string)($danceIndexViewModel->capacityHtml ?? '');
$specialTitle = trim((string)($danceIndexViewModel->specialTitle ?? ''));
$specialHtml = (string)($danceIndexViewModel->specialHtml ?? '');
?>

<?php
$sectionClass = is_string($danceEventInfoClass ?? null) ? trim((string)$danceEventInfoClass) : '';
?>

<section class="dance-event-info<?= $sectionClass === '' ? '' : ' ' . htmlspecialchars($sectionClass) ?>">
    <div class="dance-event-info-inner">
        <?php
        $importantInfoTitle = $importantInformationTitle;
        $importantInfoHtml = $importantInformationHtml;
        require __DIR__ . '/dance-important-info.php';
        ?>

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
                    $label = trim((string)($pass['label'] ?? ''));
                    $price = trim((string)($pass['price'] ?? ''));
                    $bookUrl = trim((string)($pass['bookUrl'] ?? ''));
                    if ($label === '' || $price === '') {
                        continue;
                    }
                    $rowClass = !empty($pass['highlight']) ? 'dance-pass-row dance-pass-row-highlight' : 'dance-pass-row';
                    ?>
                    <?php if ($bookUrl !== ''): ?>
                        <a class="<?= htmlspecialchars($rowClass) ?> dance-pass-row-link" href="<?= htmlspecialchars($bookUrl) ?>">
                            <span><?= htmlspecialchars($label) ?></span>
                            <strong><?= htmlspecialchars($price) ?></strong>
                        </a>
                    <?php else: ?>
                        <div class="<?= htmlspecialchars($rowClass) ?>">
                            <span><?= htmlspecialchars($label) ?></span>
                            <strong><?= htmlspecialchars($price) ?></strong>
                        </div>
                    <?php endif; ?>
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
