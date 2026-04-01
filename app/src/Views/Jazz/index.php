<link href="/css/Jazz/jazz.css" rel="stylesheet">
<?php
$performers=$jazzViewModel->jazzPerformers;
$scheduleData = $jazzViewModel->schedule;
$page=$jazzViewModel->page;



//require __DIR__ . '/../partialsViews/schedule.php';
//require __DIR__ . '/../partialsViews/jazz/jazz-featured-artists.php';   
//var_dump($page);
//die();
$map = [
    'jazz_schedule' => '/../partialsViews/schedule.php',   
    'jazz_artists' => '/../partialsViews/jazz/jazz-featured-artists.php',  
    'jazz_passes' => '/../partialsViews/jazz/passes.php',
];
?>

<main class="cms-content">
<?php

foreach ($page->sections as $section){ ?>

    <?php
    $type = trim($section->type);

    if (isset($map[$type])) {
        require __DIR__ . $map[$type];
    } else {
        echo "<!-- Unknown section: {$type} -->";
    }
    ?>

<?php } ?>
</main>