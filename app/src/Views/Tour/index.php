<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Haarlem Festival</title>
    <link href="/css/History/index.css" rel="stylesheet">
</head>
<body>

    <?php if ($hero): ?>
        <?php $section = $hero; include __DIR__ . '/../partialsViews/hero.php'; ?>
    <?php endif; ?>

    <?php if ($stops): ?>
        <?php $section = $stops; include __DIR__ . '/../partialsViews/Tour/stops-grid.php'; ?>
    <?php endif; ?>

    <?php if ($discover): ?>
        <?php $section = $discover; include __DIR__ . '/../partialsViews/Tour/description.php'; ?>
    <?php endif; ?>

    <!--<?php if ($schedule): ?>
        <?php $section = $schedule; include __DIR__ . '/../partialsViews/schedule.php'; ?>
    <?php endif; ?> -->

    <?php if ($guide): ?>
        <?php $section = $guide; include __DIR__ . '/../partialsViews/Tour/route_guides.php'; ?>
    <?php endif; ?> 
</body>
</html>