<?php
use App\Models\ViewModels\Dance\DanceBannerStatsViewModel;

$danceBannerStats = $danceBannerStats ?? null;
$totalEvents = $danceBannerStats instanceof DanceBannerStatsViewModel ? $danceBannerStats->totalEvents : 0;
$totalLocations = $danceBannerStats instanceof DanceBannerStatsViewModel ? $danceBannerStats->totalLocations : 0;
?>

<section class="dance-banner">
    <div class="dance-banner-inner">
        <div class="dance-banner-badge">
            <i data-lucide="calendar-days" aria-hidden="true"></i>
            <span>This Weekend - July 24-26, 2026</span>
        </div>

        <h1 class="dance-banner-title">Dance Events In Haarlem</h1>
        <p class="dance-banner-description">
            Discover the best dance music events happening this weekend. From progressive house to trance,
            experience world-class DJs in Haarlem's top venues.
        </p>

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
