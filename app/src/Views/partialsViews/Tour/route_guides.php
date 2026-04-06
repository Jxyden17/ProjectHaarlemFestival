<?php
use App\Models\Page\Section;
$section = $section ?? null;

if (!$section instanceof Section) {
    return;
}
?>
<section class="bottom-content-section">
    <div class="route-guides-row">   
        <div class="route-container">
            <h2 class="section-title-alt"><?= $section->title ?></h2>
            <p class="section-subtitle"><?= $section->subTitle ?></p>
            <div class="map-wrapper">
                <iframe src="https://www.google.com/maps/embed?pb=!1m70!1m12!1m3!1d9741.835084666856!2d4.627400924650594!3d52.38023320239508!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m55!3e2!4m5!1s0x47c5ef6bea0a4215%3A0x2cefd774cf4e0dab!2sThe%20St.%20Bavo%20Church%20in%20Haarlem%2C%20Grote%20Markt%2022%2C%202011%20HL%20Haarlem!3m2!1d52.381135099999995!2d4.636892899999999!4m5!1s0x47c5ef6b924ce7ed%3A0xd9721c5337b4704!2sGrote%20Markt%2C%20Haarlem!3m2!1d52.381331499999995!2d4.6363167999999995!4m5!1s0x47c5ef402ff6db3b%3A0x48cdf25945154d75!2sFrans%20Hals%20Museum%2C%20Groot%20Heiligland%2062%2C%202011%20ES%20Haarlem!3m2!1d52.376584799999996!2d4.6336674!4m5!1s0x47c5ef1555569fcf%3A0x1f066ef6d1316959!2sProveniershuis%2C%20Grote%20Houtstraat%20142D%2C%202011%20SW%20Haarlem!3m2!1d52.377356299999995!2d4.6308248!4m5!1s0x47c5ef14ed768603%3A0x5ff6ab7a87061c90!2sJopen%2C%20Gedempte%20Voldersgracht%202%2C%202011%20WD%20Haarlem!3m2!1d52.3812145!2d4.6297315!4m5!1s0x47c5ef6eac878693%3A0x4f36049541e081f1!2sWaalse%20Kerk%2C%20Begijnhof%2028%2C%202011%20HE%20Haarlem!3m2!1d52.382486799999995!2d4.6391539!4m5!1s0x47c5ef6ee47c7b93%3A0xb548e94f26e9e63b!2sWindmill%20De%20Adriaan%20(1779)%2C%20Papentorenvest%201A%2C%202011%20AV%20Haarlem!3m2!1d52.3838047!2d4.6426257!4m5!1s0x47c5ef663e696523%3A0x6b7eed60d6568553!2sAmsterdamse%20Poort%2C%20Haarlem%2C%202011%20BZ%20Haarlem!3m2!1d52.380516199999995!2d4.6465988!4m5!1s0x47c5ef000c245f87%3A0x132348d65b49b1be!2sHofje%20van%20Bakenes%2C%20Wijde%20Appelaarsteeg%2011G%2C%202011%20HB%20Haarlem!3m2!1d52.381577099999994!2d4.6399383!5e0!3m2!1sen!2snl!4v1775342955187!5m2!1sen!2snl" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
        <div class="guides-container">
            <h2 class="section-title-alt">Meet Your Guide</h2>
            <p class="guide-intro"><?= $section->description ?></p>

            <div class="guides-grid">
                <?php foreach($section->getItemsByCategorie('guide') as $item): ?>
                <div class="guide-card">
                    <img src="<?= $item->image ?>" alt="<?= $item->title?>" class="guide-img">
                    <h4><?= $item->title ?></h4>
                    <p class="guide-role"><?= $item->subTitle ?></p>
                    <p class="guide-desc"><?= $item->content ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>