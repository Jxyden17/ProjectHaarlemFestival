<?php
use App\Models\Page\Section;
$section = $section ?? null;

if (!$section instanceof Section) {
    return;
}
?>
<section class="home-map-section">
    <div class="container map-box">
        <div class="map-wrapper">
            <iframe id="festival-map-iframe" name="festival-map-frame" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2435.3066034461735!2d4.626060977202893!3d52.3829919720256!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c5ef1386b1b391%3A0x18af44a874eeb1a0!2sZijlsingel%202%2C%202013%20DN%20Haarlem!5e0!3m2!1sen!2snl!4v1775343079699!5m2!1sen!2snl" frameborder="0" style="border:0; width:100%; height:100%;" allowfullscreen="" loading="lazy"></iframe>
        </div>

        <div class="map-filters">
            <label class="map-filters-label"><?= htmlspecialchars($section->title ?? 'Filter by Category') ?></label>
            <div class="map-filters-list">
                <?php foreach ($section->getItemsByCategorie('map_location') as $item): ?>
                    <?php $href = $item->content; ?>
                    <a class="map-filter-link" href="<?= htmlspecialchars($href) ?>" target="festival-map-frame"><?= htmlspecialchars($item->title) ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<script>
    // Script to toggle active class on clicked filter
    (function(){
        const links = Array.from(document.querySelectorAll('.map-filter-link'));
        if (!links.length) return;

        function setActive(clicked) {
            links.forEach(l => l.classList.toggle('active', l === clicked));
        }

        setActive(links[0]);

        links.forEach(link => {
            link.addEventListener('click', function(){
                setActive(link);
            });
        });
    })();
</script>