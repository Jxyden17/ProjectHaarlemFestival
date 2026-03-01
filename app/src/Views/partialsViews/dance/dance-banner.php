<?php
use App\Models\Page\Section;
use App\Models\ViewModels\Dance\DanceBannerStatsViewModel;

$danceBannerStats = $danceBannerStats ?? null;
$totalEvents = $danceBannerStats instanceof DanceBannerStatsViewModel ? $danceBannerStats->totalEvents : 0;
$totalLocations = $danceBannerStats instanceof DanceBannerStatsViewModel ? $danceBannerStats->totalLocations : 0;
$danceBannerSection = $danceBannerSection ?? null;

if (!$danceBannerSection instanceof Section) {
    return;
}

$badge = (string)$danceBannerSection->subTitle;
$title = $danceBannerSection->title;
$description = (string)$danceBannerSection->description;
?>

<section class="dance-banner">
    <div class="dance-banner-inner">
        <div class="dance-banner-badge">
            <i data-lucide="calendar-days" aria-hidden="true"></i>
            <span><?= htmlspecialchars($badge) ?></span>
        </div>

        <h1 class="dance-banner-title"><?= htmlspecialchars($title) ?></h1>
        <div class="dance-banner-description"><?= $description ?></div>

        <div class="dance-banner-stats">
            <div class="dance-stat-card">
                <div class="dance-stat-card-icon">
                    <i data-lucide="calendar-range" aria-hidden="true"></i>
                </div>
                <div>
                    <div class="dance-stat-card-label">Total Events</div>
                    <div class="dance-stat-card-value">
                        <?= $totalEvents ?> <?= $totalEvents === 1 ? 'Show' : 'Shows' ?>
                    </div>
                </div>
            </div>

            <div class="dance-stat-card">
                <div class="dance-stat-card-icon dance-stat-card-icon-venue">
                    <i data-lucide="map-pin" aria-hidden="true"></i>
                </div>
                <div>
                    <div class="dance-stat-card-label">Venues</div>
                    <div class="dance-stat-card-value">
                        <?= $totalLocations ?> <?= $totalLocations === 1 ? 'Location' : 'Locations' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
