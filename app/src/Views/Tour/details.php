<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($section->title)?></title>
    <link href="/css/Tour/index.css" rel="stylesheet">
</head>
<body>

    <?php if ($header): ?>
        <?php $section = $header; include __DIR__ . '/../partialsViews/hero.php'; ?>
    <?php endif; ?>

    <?php if ($history): ?>
        <?php $section = $history; include __DIR__ . '/../partialsViews/Tour/info-grid.php'; ?>
    <?php endif; ?>

    <?php if ($did_you_know): ?>
        <?php $section = $did_you_know; include __DIR__ . '/../partialsViews/Tour/info-grid.php'; ?>
    <?php endif; ?>

    <?php if ($openingTime): ?>
        <?php $section = $openingTime; include __DIR__ . '/../partialsViews/Tour/contact-info.php'; ?>
    <?php endif; ?>
</body>
</html>