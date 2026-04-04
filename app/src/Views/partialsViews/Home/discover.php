<?php
use App\Models\Page\Section;
$section = $section ?? null;

if (!$section instanceof Section) {
    return;
}
?>
<section class="discover-events">
    <div class="container discover-box">
        <h2 class="section-title"><?= htmlspecialchars($section->title ?? 'Discover Events') ?></h2>
        
        <div class="events-grid">
            <?php foreach($section->getItemsByCategorie('event_card') as $item): ?>
                <div class="event-card">
                    <a class="card-link" href="<?= htmlspecialchars($item->url) ?>" aria-label="<?= htmlspecialchars($item->title) ?>"></a>
                    <div class="card-image">
                        <img src="<?= htmlspecialchars($item->image) ?>" alt="<?= htmlspecialchars($item->title) ?>">
                    </div>
                    <div class="card-content">
                        <h3 class="card-title title-color-<?= $index ?>"><?= htmlspecialchars($item->title) ?></h3>
                        <p class="card-text"><?= htmlspecialchars($item->content) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>