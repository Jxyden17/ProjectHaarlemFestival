<?php

use App\Models\ViewModels\Yummy\YummyIndexViewModel;

$yummyIndexViewModel = $yummyIndexViewModel ?? null;

if (!$yummyIndexViewModel instanceof YummyIndexViewModel) {
    return;
}

$venues = $yummyIndexViewModel->venues;
?>

<link href="/css/Yummy/index.css" rel="stylesheet">
<link href="/css/partialViews/yummy/yummy-map.css" rel="stylesheet">
<link href="/css/partialViews/yummy/yummy-restaurants.css" rel="stylesheet">

<div class="yummy-page">

    <section class="yummy-hero">
        <div class="yummy-container">
            <h1 class="yummy-title">Yummy!</h1>
            <p class="yummy-subtitle">Gourmet with a twist</p>
        </div>
    </section>

    <?php require __DIR__ . '/../partialsViews/yummy/yummy-map.php'; ?>

    <?php require __DIR__ . '/../partialsViews/yummy/yummy-restaurants.php'; ?>

</div>
