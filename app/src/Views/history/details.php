<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Haarlem Festival</title>
    <link href="/css/History/index.css" rel="stylesheet">
</head>
<body>
    <div class="subpage-container">
    <?php include __DIR__ . '/../partials/_subpage_hero.php'; ?>

    <div class="content-wrapper">
        <?php foreach ($sections as $section): ?>
            <?php include __DIR__ . '/../partials/_subpage_grid.php'; ?>
        <?php endforeach; ?>

        <?php include __DIR__ . '/../partials/_subpage_info_footer.php'; ?>
    </div>
</div>
</body>
</html>