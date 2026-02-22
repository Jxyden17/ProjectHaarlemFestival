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
<div class="performers-flex">
    <?php
    foreach ($performers as $performer) {
        // 1️⃣ Naam trimmen en lowercase maken
        $performerNameClean = strtolower(trim($performer->performerName));

        // 2️⃣ Check of er een speciale afbeelding is
        if (isset($specialImages[$performerNameClean])) {
            $imgName = $specialImages[$performerNameClean];
        } else {
            // 3️⃣ Anders fallback: spaties en speciale tekens vervangen door streepjes
            $imgName = preg_replace('/[^a-z0-9]+/', '-', $performerNameClean);
        }

        // 4️⃣ Foto-extensie bepalen
        $imgPath = "/img/jazzIMG/{$imgName}.png";
    ?>
    <div class="performer-card">
        <img src="<?= $imgPath ?>" alt="<?= htmlspecialchars($performer->performerName) ?>">
    </div>
    <?php } ?>
</div>