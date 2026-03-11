<?php
use App\Models\ViewModels\Dance\DanceIndexViewModel;

$danceIndexViewModel = $danceIndexViewModel ?? null;

if (!$danceIndexViewModel instanceof DanceIndexViewModel) {
    return;
}

$scheduleData = $danceIndexViewModel->schedule;
$venues = $danceIndexViewModel->venues;

$danceIndexScheduleClass = 'schedule--dance-index';
$danceIndexScheduleTitleIcon = 'calendar-days';
$danceIndexScheduleHasIcons = true;
$danceEventInfoSectionClass = 'dance-event-info--dance-index';
?>

<link href="/css/Dance/dance-index.css" rel="stylesheet">

<?php require __DIR__ . '/../partialsViews/dance/dance-banner.php'; ?>
<?php require __DIR__ . '/../partialsViews/dance/dance-featured-artists.php'; ?>
<?php
$scheduleSectionClass = $danceIndexScheduleClass;
$scheduleTitleIcon = $danceIndexScheduleTitleIcon;
$scheduleHasIcons = $danceIndexScheduleHasIcons;
require __DIR__ . '/../partialsViews/schedule.php';
?>
<?php
$danceEventInfoClass = $danceEventInfoSectionClass;
require __DIR__ . '/../partialsViews/dance/dance-event-info.php';
?>
