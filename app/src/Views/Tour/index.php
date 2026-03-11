<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle ?? 'Tour Event') ?></title>
    <link href="/css/Tour/index.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/partialViews/schedule.css">
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

    <?php if ($scheduleData): ?>
        <?php $section = $scheduleData; include __DIR__ . '/../partialsViews/schedule.php'; ?>
    <?php endif; ?>

    <?php if ($guide): ?>
        <?php $section = $guide; include __DIR__ . '/../partialsViews/Tour/route_guides.php'; ?>
    <?php endif; ?> 
</body>
</html>