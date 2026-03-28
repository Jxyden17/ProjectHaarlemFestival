<?php
use App\Models\ViewModels\Dance\DanceDetailViewModel;

$danceDetailViewModel = $danceDetailViewModel ?? null;
if (!$danceDetailViewModel instanceof DanceDetailViewModel) {
    return;
}

$leftHeroImage = $danceDetailViewModel->heroImages['left'] ?? ['image' => '', 'alt' => ''];
$mainHeroImage = $danceDetailViewModel->heroImages['center'] ?? ['image' => '', 'alt' => ''];
$rightHeroImage = $danceDetailViewModel->heroImages['right'] ?? ['image' => '', 'alt' => ''];
?>

<link href="/css/Dance/dance-detail.css" rel="stylesheet">

<section class="dance-detail-banner">
    <div class="dance-detail-banner-media">
        <div class="dance-detail-panel dance-detail-panel-left">
            <?php if (($leftHeroImage['image'] ?? '') !== ''): ?>
                <img src="<?= htmlspecialchars((string)$leftHeroImage['image']) ?>" alt="<?= htmlspecialchars((string)($leftHeroImage['alt'] ?? '')) ?>" loading="lazy">
            <?php endif; ?>
        </div>

        <div class="dance-detail-panel dance-detail-panel-main">
            <?php if (($mainHeroImage['image'] ?? '') !== ''): ?>
                <img src="<?= htmlspecialchars((string)$mainHeroImage['image']) ?>" alt="<?= htmlspecialchars((string)($mainHeroImage['alt'] ?? '')) ?>">
            <?php endif; ?>
        </div>

        <div class="dance-detail-panel dance-detail-panel-right">
            <?php if (($rightHeroImage['image'] ?? '') !== ''): ?>
                <img src="<?= htmlspecialchars((string)$rightHeroImage['image']) ?>" alt="<?= htmlspecialchars((string)($rightHeroImage['alt'] ?? '')) ?>" loading="lazy">
            <?php endif; ?>
        </div>
    </div>

    <div class="dance-detail-banner-content">
        <a class="dance-detail-back-link" href="/dance#dance-featured-artists">
            <i data-lucide="arrow-left" aria-hidden="true"></i>
            <span>Back to Home</span>
        </a>

        <span class="dance-detail-badge"><?= htmlspecialchars($danceDetailViewModel->badge) ?></span>
        <h1 class="dance-detail-title"><?= htmlspecialchars($danceDetailViewModel->performerName) ?></h1>
        <p class="dance-detail-subtitle"><?= htmlspecialchars($danceDetailViewModel->subtitle) ?></p>
    </div>
</section>

<section class="dance-detail-highlights">
    <div class="dance-detail-highlights-inner">
        <h2 class="dance-detail-highlights-title">
            <i data-lucide="award" aria-hidden="true"></i>
            <?= htmlspecialchars($danceDetailViewModel->highlightsTitle) ?>
        </h2>

        <div class="dance-detail-highlights-grid">
            <?php foreach ($danceDetailViewModel->highlights as $item): ?>
                <article class="dance-detail-highlight-card">
                    <span class="dance-detail-highlight-icon"><i data-lucide="<?= htmlspecialchars((string)($item['icon'] ?? 'star')) ?>" aria-hidden="true"></i></span>
                    <div>
                        <h3><?= htmlspecialchars((string)($item['title'] ?? '')) ?></h3>
                        <p><?= htmlspecialchars((string)($item['content'] ?? '')) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="dance-detail-tracks">
    <div class="dance-detail-tracks-inner">
        <h2 class="dance-detail-tracks-title">
            <i data-lucide="music" aria-hidden="true"></i>
            <?= htmlspecialchars($danceDetailViewModel->tracksTitle) ?>
        </h2>

        <div class="dance-detail-tracks-grid">
            <?php foreach ($danceDetailViewModel->tracks as $item): ?>
                <?php $audioPath = trim((string)($item['audioUrl'] ?? '')); ?>
                <article class="dance-detail-track-card">
                    <div class="dance-detail-track-cover">
                        <?php if ((string)($item['image'] ?? '') !== ''): ?>
                            <img src="<?= htmlspecialchars((string)$item['image']) ?>" alt="<?= htmlspecialchars((string)($item['title'] ?? '')) ?> cover" loading="lazy">
                        <?php endif; ?>
                        <button
                            type="button"
                            class="dance-detail-track-play"
                            data-audio-src="<?= htmlspecialchars($audioPath) ?>"
                            data-state="paused"
                            aria-label="Play preview for <?= htmlspecialchars((string)($item['title'] ?? 'track')) ?>"
                            <?= $audioPath === '' ? 'disabled' : '' ?>
                        >
                            <i data-lucide="play" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="dance-detail-track-content">
                        <h3><?= htmlspecialchars((string)($item['title'] ?? '')) ?></h3>
                        <p><?= htmlspecialchars((string)($item['subtitle'] ?? '')) ?></p>
                        <small><?= htmlspecialchars((string)($item['year'] ?? '')) ?></small>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <p class="dance-detail-tracks-note">
            <i data-lucide="music-2" aria-hidden="true"></i>
            <?= htmlspecialchars($danceDetailViewModel->tracksNote) ?>
        </p>
    </div>
</section>

<script src="/js/dance-detail-audio.js"></script>

<section class="dance-detail-schedule">
    <div class="dance-detail-schedule-inner">
        <h2 class="dance-detail-schedule-title">
            <i data-lucide="calendar-days" aria-hidden="true"></i>
            <?= htmlspecialchars($danceDetailViewModel->scheduleTitle) ?>
        </h2>

        <div class="dance-detail-schedule-table">
            <div class="dance-detail-schedule-head">
                <div>Date</div>
                <div>Time</div>
                <div>Event</div>
                <div>Location</div>
                <div>Price</div>
                <div></div>
            </div>

            <?php if (count($danceDetailViewModel->scheduleRows) === 0): ?>
                <div class="dance-detail-schedule-empty">No <?= htmlspecialchars($danceDetailViewModel->performerName === '' ? 'performer' : $danceDetailViewModel->performerName) ?> sessions found.</div>
            <?php else: ?>
                <?php foreach ($danceDetailViewModel->scheduleRows as $row): ?>
                    <?php
                    if (!$row instanceof \App\Models\ViewModels\Shared\ScheduleRowViewModel) {
                        continue;
                    }
                    ?>
                    <div class="dance-detail-schedule-row">
                        <div class="dance-detail-schedule-cell-date">
                            <span class="dance-detail-schedule-cell-icon"><i data-lucide="calendar" aria-hidden="true"></i></span>
                            <span><?= htmlspecialchars($row->date) ?></span>
                        </div>
                        <div class="dance-detail-schedule-cell-time">
                            <i data-lucide="clock-3" aria-hidden="true"></i>
                            <span><?= htmlspecialchars($row->time) ?></span>
                        </div>
                        <div><?= htmlspecialchars($row->event) ?></div>
                        <div class="dance-detail-schedule-cell-location">
                            <i data-lucide="map-pin" aria-hidden="true"></i>
                            <span><?= htmlspecialchars($row->location) ?></span>
                        </div>
                        <div class="dance-detail-schedule-price"><?= htmlspecialchars($row->price) ?></div>
                        <div>
                            <a class="dance-detail-schedule-book" href="<?= htmlspecialchars($row->bookUrl) ?>">Book Now</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php
        $importantInfoTitle = $danceDetailViewModel->importantInfoTitle;
        $importantInfoHtml = $danceDetailViewModel->importantInfoHtml;
        require __DIR__ . '/../partialsViews/dance/dance-important-info.php';
        ?>
    </div>
</section>
