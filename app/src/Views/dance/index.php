<?php
use App\Models\ViewModels\Dance\DanceIndexViewModel;
use App\Models\Page\Page;

$danceIndexViewModel = $danceIndexViewModel ?? null;

if (!$danceIndexViewModel instanceof DanceIndexViewModel) {
    return;
}

// Keep partials unchanged for now by mapping page VM fields to existing names.
$scheduleData = $danceIndexViewModel->schedule;
$danceBannerStats = $danceIndexViewModel->bannerStats;
$venues = $danceIndexViewModel->venues;
$danceHomePage = $danceIndexViewModel->homeContent;

if (!$danceHomePage instanceof Page) {
    return;
}

$danceBannerSection = $danceHomePage->getSection('dance_banner');
$danceArtistsSection = $danceHomePage->getSection('dance_artists');
$danceInfoSection = $danceHomePage->getSection('dance_info');
$dancePassesSection = $danceHomePage->getSection('dance_passes');
$danceCapacitySection = $danceHomePage->getSection('dance_capacity');
$danceSpecialSection = $danceHomePage->getSection('dance_special_session');
?>

<link href="/css/Dance/dance-index.css" rel="stylesheet">

<?php require __DIR__ . '/../partialsViews/dance/dance-banner.php'; ?>
<?php require __DIR__ . '/../partialsViews/dance/dance-featured-artists.php'; ?>
<?php require __DIR__ . '/../partialsViews/schedule.php'; ?>
<?php require __DIR__ . '/../partialsViews/dance/dance-event-info.php'; ?>
