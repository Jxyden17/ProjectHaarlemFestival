<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> <?=htmlentities($section->title) ?></title>
    <link href="/css/Home/index.css" rel="stylesheet">
</head>
<body>

    <?php if ($hero): ?>
        <?php $section = $hero; include __DIR__ . '/../partialsViews/Home/hero.php'; ?>
    <?php endif; ?>

    <?php if ($about): ?>
        <?php $section = $about; include __DIR__ . '/../partialsViews/Home/about.php'; ?>
    <?php endif; ?>

    <?php if ($discover): ?>
        <?php $section = $discover; include __DIR__ . '/../partialsViews/Home/discover.php'; ?>
    <?php endif; ?>

    <?php if ($scheduleData): ?>
        <?php $section = $scheduleData; include __DIR__ . '/../partialsViews/schedule.php'; ?>
    <?php endif; ?> 

    <?php if ($map): ?>
        <?php $section = $map; include __DIR__ . '/../partialsViews/Home/map.php'; ?>
    <?php endif; ?> 

    <?php if ($faq): ?>
        <?php $section = $faq; include __DIR__ . '/../partialsViews/Home/faq.php'; ?>
    <?php endif; ?>
</body>
</html>