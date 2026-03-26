<?php
    $scheduleSection = $schedule ?? null;
    $items = $scheduleSection ? $scheduleSection->items : [];
?>

<div class="program-container">

    <h2>Your personal schedule</h2>

    <!-- Filters (static for now) -->

        <div class="filters">
            <div class="filter-group">
                <button class="filter-day active" data-day="all">All Days</button>
                <button class="filter-day" data-day="2026-07-25">Thursday</button>
                <button class="filter-day" data-day="2026-07-26">Friday</button>
                <button class="filter-day" data-day="2026-07-27">Saturday</button>
                <button class="filter-day" data-day="2026-07-28">Sunday</button>
            </div>

            <div class="filter-group">
                <button class="filter-event active" data-event="all">All Events</button>
                <button class="filter-event" data-event="Dance">Dance</button>
                <button class="filter-event" data-event="Jazz">Jazz</button>
                <button class="filter-event" data-event="A Stroll Trough History">Tour</button>
                <button class="filter-event" data-event="TellingStory">Story</button>
            </div>
        </div>

        <div class="program-header">
            <span>Event</span>
            <span>Time</span>
            <span>Location</span>
            <span>Name</span>
            <span>Tickets</span>
            <span>Price</span>
            <span></span>
        </div>

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

</div>

<script src="/js/personal-program-filters.js"></script>