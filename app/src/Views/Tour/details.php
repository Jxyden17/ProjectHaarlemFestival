<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($section->title)?></title>
    <link href="/css/History/index.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/partialViews/infoGrid.css">
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
</body>
</html>