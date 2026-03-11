<section class="contact-details-section">
    <div class="contact-container">
        <div class="map-wrapper">
            <iframe src="<?= $section->description ?>" width="100%" height="450" style="border:0;"></iframe>
        </div>

        <div class="info-wrapper-box">
            <div class="entrance-image">
                <?php $img = $section->getFirstItemImage('opening_hours'); ?>
                <?php if ($img): ?>
                    <img src="<?= $img ?>" alt="Entrance">
                <?php endif; ?>
            </div>
            
            <div class="details-flex">
                <div class="contact-col">
                    <h2 class="column-title"><?= $section->subTitle ?></h2>
                    <?php foreach($section->getItemsByCategorie('info') as $item): ?>
                        <p><strong><?= $item->title ?>:</strong> <?= $item->content ?></p>
                    <?php endforeach; ?>
                </div>
                
                <div class="hours-col">
                    <h2 class="column-title"><?= $section->title ?></h2>
                    <?php foreach ($section->getItemsByCategorie('opening_hours') as $item): ?>
                        <?php if ($item->title !== 'extra'): ?>
                        <div class="hour-row">
                            <span class="day"><?= $item->title ?></span>
                            <span class="time"><?= $item->content ?></span>
                        </div>
                        <?php else: ?>
                            <p class="extra-info"><?= $item->content ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
