<section >
  
    <div >
        <h2 >          
            Featured Artists
        </h2>
<?php
// Associatieve array met uitzonderingen (key = artiestennaam lowercase)
$specialImages = [
    "rilan & the bombadiers" => "Rilan-&-The-Bombadiers",
    "eric vloeimans and hotspot!" => "hotspot"
];?>
<section class="jazz-artists">
    <div class="jazz-artists-inner">
        <div class="jazz-artists-grid">
            <?php
                foreach ($performers as $performer) 
                {
                    $performerNameClean = strtolower(trim($performer->performerName));
                    if (isset($specialImages[$performerNameClean])) {
                        $imgName = $specialImages[$performerNameClean];
                    } else 
                    {
                        $imgName = preg_replace('/[^a-z0-9]+/', '-', $performerNameClean);
                    }
                    $imgPath = "/img/jazzIMG/{$imgName}.png";
                    ?>
                    <div class="jazz-artist-card">
                        <img src="<?= $imgPath ?>" alt="<?= htmlspecialchars($performer->performerName) ?>">
                    </div>
                <?php }
            ?>
        </div>
    </div>
</section>
