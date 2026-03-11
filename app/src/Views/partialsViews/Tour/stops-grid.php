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
        <?php
        $detailPageIdsByPosition = [
            0 => 2,
            1 => 7,
            2 => 8,
            3 => 9,
            4 => 10,
            5 => 11,
            6 => 12,
            7 => 13,
            8 => 14,
        ];
        $detailPageId = (int)($detailPageIdsByPosition[(int)$item->position] ?? (int)$item->position);
        ?>
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
                <a href="/tour/details?id=<?= $detailPageId ?>" class="btn-primary">Read More</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
