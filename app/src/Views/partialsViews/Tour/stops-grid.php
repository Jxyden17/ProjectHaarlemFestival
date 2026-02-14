<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/css/partialViews/Tour/stopGrid.css">
</head>
<section class="stops-section">
    <h2 class="title"><?= htmlspecialchars($section->title) ?? '' ?></h2>
    <div class="grid">
        <?php foreach($section->getItemsByCategorie('stop') as $item): ?>
        <div class="card">
            <div class="image">
                <img src="<?= htmlspecialchars($item->image) ?>" alt="<?= htmlspecialchars($item->title) ?>">
                <span class="letter"><?= htmlspecialchars($item->icon) ?></span>
            </div>
            <div class="info">
                <span class="duration"><?= htmlspecialchars($item->duration) ?></span>
                <h3><?= htmlspecialchars($item->title) ?></h3>
                <p><?= htmlspecialchars($item->subTitle) ?></p>
                <p><?= htmlspecialchars($item->content) ?></p>
                <a href="/tour/details?id=<?= $item->position ?>" class="btn-primary">Read More</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
