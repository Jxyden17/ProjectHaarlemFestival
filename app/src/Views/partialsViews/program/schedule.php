<div class="program-container">

    <h2>Your Personal Schedule</h2>

    <!-- Filters (static for now) -->
    <div class="filters">
        <div class="filter-group">
            <button class="active">All days</button>
            <button>Thursday</button>
            <button>Friday</button>
            <button>Saturday</button>
            <button>Sunday</button>
        </div>

        <div class="filter-group">
            <button class="active">All Events</button>
            <button>Dance</button>
            <button>Jazz</button>
            <button>Tour</button>
            <button>Yummy</button>
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

        <div class="day-header">
            <span><?= htmlspecialchars($date) ?></span>
            <span class="day-total">
                Total: €<?= number_format($total, 2) ?>
            </span>
        </div>

        <?php foreach ($sessions as $session): ?>

            <div class="program-row">

                <span class="badge badge-<?= strtolower($session['event']) ?>">
                    <?= htmlspecialchars($session['event']) ?>
                </span>

                <span><?= htmlspecialchars($session['time']) ?></span>

                <span><?= htmlspecialchars($session['venue']) ?></span>

                <span class="name">
                    <?= htmlspecialchars($session['event']) ?>
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

    <?php endforeach; ?>

</div>