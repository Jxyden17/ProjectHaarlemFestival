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

    <?php if ($grid): ?>
        <?php $section = $grid; include __DIR__ . '/../partialsViews/stops-grid.php'; ?>
    <?php endif; ?>

    <!-- <?php if ($discover): ?>
        <?php $section = $discover; include __DIR__ . '/../partialsViews/description.php'; ?>
    <?php endif; ?>

    <?php if ($schedule): ?>
        <?php $section = $schedule; include __DIR__ . '/../partialsViews/schedule.php'; ?>
    <?php endif; ?>

    <?php if ($guides): ?>
        <?php $section = $guides; include __DIR__ . '/../partialsViews/route_guides.php'; ?>
    <?php endif; ?> -->
</body>
</html>