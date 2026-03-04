<section class="contact-details-section">
    <div class="contact-container">
        <div class="map-wrapper">
            <iframe src="<?= htmlspecialchars((string)($section->description ?? '')) ?>" width="100%" height="450" style="border:0;"></iframe>
        </div>

        <div class="info-wrapper-box">
            <div class="entrance-image">
                <?php $img = $section->getFirstItemImage('opening_hours'); ?>
                <?php if ($img): ?>
                    <img src="<?= htmlspecialchars((string)$img) ?>" alt="Entrance">
                <?php endif; ?>
            </div>
            
            <div class="details-flex">
                <div class="contact-col">
                    <h2 class="column-title"><?= htmlspecialchars((string)($section->subTitle ?? '')) ?></h2>
                    <?php foreach($section->getItemsByCategorie('info') as $item): ?>
                        <p><strong><?= htmlspecialchars((string)$item->title) ?>:</strong> <?= htmlspecialchars((string)($item->content ?? '')) ?></p>
                    <?php endforeach; ?>
                </div>
                
                <div class="hours-col">
                    <h2 class="column-title"><?= htmlspecialchars((string)$section->title) ?></h2>
                    <?php foreach ($section->getItemsByCategorie('opening_hours') as $item): ?>
                        <?php if ($item->title !== 'extra'): ?>
                        <div class="hour-row">
                            <span class="day"><?= htmlspecialchars((string)$item->title) ?></span>
                            <span class="time"><?= htmlspecialchars((string)($item->content ?? '')) ?></span>
                        </div>
                        <?php else: ?>
                            <p class="extra-info"><?= htmlspecialchars((string)($item->content ?? '')) ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
