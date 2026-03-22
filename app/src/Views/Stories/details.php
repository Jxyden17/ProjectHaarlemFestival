<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars((string) ($pageTitle ?? 'Story Details')) ?></title>
    <link href="/css/Stories/details.css" rel="stylesheet">
</head>
<body>
    <main class="story-detail-page">
        <div class="story-detail-shell">
            <a class="story-back-link" href="/stories">
                &larr; Back to Stories
            </a>

            <?php if ($hero): ?>
                <?php $section = $hero; include __DIR__ . '/../partialsViews/Stories/details-hero.php'; ?>
            <?php endif; ?>

            <?php if ($gallery): ?>
                <?php $section = $gallery; include __DIR__ . '/../partialsViews/Stories/details-gallery.php'; ?>
            <?php endif; ?>

            <?php if ($about): ?>
                <?php $section = $about; include __DIR__ . '/../partialsViews/Stories/details-about.php'; ?>
            <?php endif; ?>

            <?php if ($featured): ?>
                <?php $section = $featured; include __DIR__ . '/../partialsViews/Stories/details-featured.php'; ?>
            <?php endif; ?>

            <?php if ($booking): ?>
                <?php $section = $booking; include __DIR__ . '/../partialsViews/Stories/details-booking.php'; ?>
            <?php endif; ?>
        </div>
    </main>
    <script src="/js/stories-detail-audio.js"></script>
</body>
</html>
