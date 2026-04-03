
<?php
//var_dump($section);
//die();
?>

<div class="cardcontainer" style="width: 100%;">
  <article class="Jazz-info-card">
    <h3 class="Jazz-info-card-title">
      <span class="Jazz-info-card-icon">
        <i data-lucide="calendar-days" aria-hidden="true"></i>
      </span>
      <?= htmlspecialchars($section->title) ?>
    </h3>
    <div class="row">
      <?php foreach ($section->items as $pass) { ?>
    
        <div class="col-md-4 ">
          <div class="card text-warning Jazz-info-card-icon-warn">
            <div class="card-body">
              <h5 class="card-title"><?= $pass->title ?></h5>
              <p class="card-text"><?= $pass->content ?></p>
              <a href="http://localhost/book/<?=$pass->id ?>" class="pass-book-btn">book now</a>
            </div>
          </div>
        </div>

      <?php } ?>
    </div>
  </article>
</div>



