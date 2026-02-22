
<?php
$performers=$jazzViewModel->jazzPerformers;
$scheduleData = $jazzViewModel->schedule;

require __DIR__ . '/../partialsViews/schedule.php';
require __DIR__ . '/../partialsViews/jazz/jazz-featured-artists.php'; ?>