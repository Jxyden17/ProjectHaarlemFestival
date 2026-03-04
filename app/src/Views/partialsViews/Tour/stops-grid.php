<?php
use App\Models\Page\Section;

$section = $section ?? null;

if (!$section instanceof Section) {
    return;
}
?>
<section class="stops-section">
    <h2 class="title"><?= htmlspecialchars($section->title) ?></h2>
    <div class="grid">
        <?php foreach($section->getItemsByCategorie('stop') as $item): ?>
        <div class="card">
            <div class="image">
                <img src="<?= htmlspecialchars((string)($item->image ?? '')) ?>" alt="<?= htmlspecialchars((string)$item->title) ?>">
                <span class="letter"><?= htmlspecialchars((string)($item->icon ?? '')) ?></span>
            </div>
            <div class="info">
                <span class="duration"><?= htmlspecialchars((string)($item->duration ?? '')) ?></span>
                <h3><?= htmlspecialchars((string)$item->title) ?></h3>
                <p><?= htmlspecialchars((string)($item->subTitle ?? '')) ?></p>
                <p><?= htmlspecialchars((string)($item->content ?? '')) ?></p>
                <a href="/tour/details?id=<?= $item->position ?>" class="btn-primary">Read More</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
