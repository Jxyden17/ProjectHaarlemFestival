<?php
use App\Models\ViewModels\Dance\DanceIndexViewModel;

$danceIndexViewModel = $danceIndexViewModel ?? null;

if (!$danceIndexViewModel instanceof DanceIndexViewModel) {
    return;
}

// Keep partials unchanged for now by mapping page VM fields to existing names.
$scheduleData = $danceIndexViewModel->schedule;
$danceBannerStats = $danceIndexViewModel->bannerStats;
$venues = $danceIndexViewModel->venues;
?>

<link href="/css/Dance/Dance-Index.css" rel="stylesheet">

<?php require __DIR__ . '/../partialsViews/dance/dance-banner.php'; ?>
<?php require __DIR__ . '/../partialsViews/dance/dance-featured-artists.php'; ?>
<?php require __DIR__ . '/../partialsViews/schedule.php'; ?>
<?php require __DIR__ . '/../partialsViews/dance/dance-event-info.php'; ?>
