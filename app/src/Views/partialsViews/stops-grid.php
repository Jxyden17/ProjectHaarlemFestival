<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/css/partialViews/stopGrid.css">
</head>
<section class="stops-section">
    <h2 class="title"><?= htmlspecialchars($section->title) ?? '' ?></h2>
    <div class="grid">
        <?php foreach($section->getItemsByCategorie('stop') as $item): ?>
        <div class="card">
            <div class="image">
                <img src="<?= htmlspecialchars($item->image) ?>" alt="<?= htmlspecialchars($item->title) ?>">
                <span class="letter">A</span>
            </div>
            <div class="info">
                <span class="duration"><?= htmlspecialchars($item->url) ?></span>
                <h3><?= htmlspecialchars($item->title) ?></h3>
                <p><?= htmlspecialchars($item->content) ?></p>
                <button class="read-more">Read More</button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
