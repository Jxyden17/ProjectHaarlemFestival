<?php
use App\Models\Page\Section;

$section = $section ?? null;

if (!$section instanceof Section) {
    return;
}
?>
<section class="stops-section">
    <h2 class="title"><?= $section->title ?></h2>
    <div class="grid">
        <?php foreach($section->getItemsByCategorie('stop') as $item): ?>
        <div class="card">
            <div class="image">
                <img src="<?= $item->image ?>" alt="<?= $item->title ?>">
                <span class="letter"><?= $item->icon ?></span>
            </div>
            <div class="info">
                <span class="duration"><?= $item->duration ?></span>
                <h3><?= $item->title ?></h3>
                <p><?= $item->subTitle ?></p>
                <p><?= $item->content ?></p>
                <a href="/tour/details?id=<?= $item->url ?>" class="Read-More">Read More</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>