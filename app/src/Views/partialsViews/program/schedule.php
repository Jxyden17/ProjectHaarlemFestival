<?php
    $scheduleSection = $schedule ?? null;
    $items = $scheduleSection ? $scheduleSection->items : [];
?>

<div class="program-container">

    <h2><?= htmlspecialchars($scheduleSection->title ?? '') ?></h2>

    <div class="filters">

        <div class="filter-group">
            <button class="filter-day active" data-day="all">
                All Days
            </button>

            <?php foreach ($program as $date => $_): ?>
                <button class="filter-day"
                        data-day="<?= htmlspecialchars($date) ?>">
                    <?= date('l', strtotime($date)) ?>
                </button>
            <?php endforeach; ?>
        </div>


        <div class="filter-group">

            <button class="filter-event active" data-event="all">
                All Events
            </button>

            <?php
            $events = [];

            foreach ($program as $sessions) {
                foreach ($sessions as $session) {
                    $events[$session['event']] = true;
                }
            }

            foreach (array_keys($events) as $event):
            ?>
                <button class="filter-event"
                        data-event="<?= htmlspecialchars($event) ?>">
                    <?= htmlspecialchars($event) ?>
                </button>
            <?php endforeach; ?>

        </div>

    </div>

    <div class="program-header">

        <?php foreach ($items as $item): ?>
            <span><?= htmlspecialchars($item->title) ?></span>
        <?php endforeach; ?>

    </div>

    <?php if (empty($program)): ?>

        <div class="empty-schedule">
            <p>No tickets saved yet.</p>
        </div>

    <?php else: ?>
        <?php foreach ($program as $date => $sessions): ?>

            <?php
            $total = 0;
            foreach ($sessions as $s) {
                $total += $s['price'] * $s['tickets'];
            }
            ?>

            <div class="day-group" data-date="<?= htmlspecialchars($date) ?>">

                <div class="day-header">
                    <span><?= htmlspecialchars($date) ?></span>
                    <span class="day-total">
                        Total: €<?= number_format($total, 2) ?>
                    </span>
                </div>

                <?php foreach ($sessions as $session): ?>

                    <div class="program-row"
                        data-event="<?= (htmlspecialchars($session['event'])) ?>"
                        data-session-id="<?= $session['session_id'] ?>">


                        <span class="badge badge-<?= strtolower($session['event']) ?>">
                            <?= htmlspecialchars($session['event']) ?>
                        </span>

                        <span><?= htmlspecialchars($session['time']) ?></span>

                        <span><?= htmlspecialchars($session['address']) ?></span>

                        <span class="name">
                            <?= htmlspecialchars($session['venue']) ?>
                        </span>

                        <span class="tickets">
                            <?= $session['tickets'] ?>
                        </span>

                        <span class="price">
                            €<?= number_format($session['price'], 2) ?>
                        </span>

                        <button class="delete-btn">Delete</button>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endforeach; ?>
        
    <?php endif; ?>

</div>

<script src="/js/personal-program-filters.js"></script>