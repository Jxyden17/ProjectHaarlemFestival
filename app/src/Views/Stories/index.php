<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Haarlem Festival</title>
    <link href="/css/Stories/index.css" rel="stylesheet">
</head>
<body>

    <?php if ($hero): ?>
        <?php $section = $hero; include __DIR__ . '/../partialsViews/Stories/hero.php'; ?>
    <?php endif; ?>

    <?php if ($callout): ?>
        <?php $section = $callout; include __DIR__ . '/../partialsViews/Stories/callout.php'; ?>
    <?php endif; ?>

    <?php if ($grid): ?>
        <?php $section = $grid; include __DIR__ . '/../partialsViews/Stories/info-grid.php'; ?>
    <?php endif; ?>

    <?php if ($venues): ?>
        <?php $section = $venues; include __DIR__ . '/../partialsViews/Stories/venues.php'; ?>
    <?php endif; ?>

    <section class="stories-schedule-wrapper">
     <?php $section = $schedule; include __DIR__ . '/../partialsViews/schedule.php'; ?>
    </section>

    <?php if ($explore): ?>
        <?php $section = $explore; include __DIR__ . '/../partialsViews/Stories/explore.php'; ?>
    <?php endif; ?>

    <?php if ($faq): ?>
        <?php $section = $faq; include __DIR__ . '/../partialsViews/Stories/faq.php'; ?>
    <?php endif; ?> 

</body>
</html>
