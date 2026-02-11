<?php
use App\Models\ViewModels\ScheduleViewModel;

$scheduleData = $scheduleData ?? null;

if (!$scheduleData instanceof ScheduleViewModel) {
    return;
}

$title = $scheduleData->title;
$dayFilters = $scheduleData->dayFilters;
$groups = $scheduleData->groups;
?>
<link rel="stylesheet" href="/css/partialViews/schedule.css">

<section class="schedule-section">
    <div class="schedule-container">
        <div class="schedule-header">
            <h2 class="schedule-title"><?= htmlspecialchars($title) ?></h2>
        </div>

        <?php if (!empty($dayFilters)): ?>
            <div class="schedule-filters">
                <div class="schedule-filter-group">
                    <?php foreach ($dayFilters as $filter): ?>
                        <button
                            class="schedule-filter-btn<?= !empty($filter['active']) ? ' is-active' : '' ?>"
                            type="button"
                            data-filter="<?= htmlspecialchars((string)$filter['key']) ?>"
                        >
                            <?= htmlspecialchars((string)$filter['label']) ?>
                            <?php if (!empty($filter['count']) && empty($filter['active'])): ?>
                                (<?= (int)$filter['count'] ?>)
                            <?php endif; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="schedule-list">
            <div class="schedule-row schedule-row-head">
                <div>DATE</div>
                <div>TIME</div>
                <div>EVENT</div>
                <div>LOCATION</div>
                <div>PRICE</div>
                <div></div>
            </div>

            <?php foreach ($groups as $group): ?>
                <div class="schedule-day-group" data-day="<?= htmlspecialchars((string)$group['dayKey']) ?>">
                    <h3 class="schedule-day-title"><?= htmlspecialchars((string)$group['title']) ?></h3>
                    <div class="schedule-day-subtitle"><?= count($group['rows']) ?> events scheduled</div>

                    <?php foreach ($group['rows'] as $row): ?>
                        <div class="schedule-row">
                            <div><?= htmlspecialchars((string)$row['date']) ?></div>
                            <div><?= htmlspecialchars((string)$row['time']) ?></div>
                            <div><?= htmlspecialchars((string)$row['event']) ?></div>
                            <div><?= htmlspecialchars((string)$row['location']) ?></div>
                            <div class="schedule-price"><?= htmlspecialchars((string)$row['price']) ?></div>
                            <div>
                                <a class="schedule-book-btn" href="<?= htmlspecialchars((string)$row['bookUrl']) ?>">
                                    Book Now
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script src="/js/schedule-filters.js"></script>
