<?php
use App\Models\ViewModels\Shared\ScheduleViewModel;

$scheduleData = $scheduleData ?? null;

if (!$scheduleData instanceof ScheduleViewModel) {
    return;
}

$title = $scheduleData->title;
$dayFilters = $scheduleData->dayFilters;
$groups = $scheduleData->groups;
?>
<section class="schedule-section">
    <div class="schedule-container">
        <div class="schedule-header">
            <h2 class="schedule-title"><?= htmlspecialchars($title) ?></h2>
        </div>

        <?php if ($scheduleData->hasFilters): ?>
            <div class="schedule-filters">
                <div class="schedule-filter-group">
                    <?php foreach ($dayFilters as $filter): ?>
                        <button
                            class="schedule-filter-btn<?= $filter->isActive ? ' is-active' : '' ?>"
                            type="button"
                            data-filter="<?= htmlspecialchars($filter->key) ?>"
                        >
                            <?= htmlspecialchars($filter->label) ?>
                            <?= htmlspecialchars($filter->countLabel) ?>
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
                <div class="schedule-day-group" data-day="<?= htmlspecialchars($group->dayKey) ?>">
                    <h3 class="schedule-day-title"><?= htmlspecialchars($group->title) ?></h3>
                    <div class="schedule-day-subtitle"><?= htmlspecialchars($group->subtitle) ?></div>

                    <?php foreach ($group->rows as $row): ?>
                        <div class="schedule-row">
                            <div><?= htmlspecialchars($row->date) ?></div>
                            <div><?= htmlspecialchars($row->time) ?></div>
                            <div><?= htmlspecialchars($row->event) ?></div>
                            <div><?= htmlspecialchars($row->location) ?></div>
                            <div class="schedule-price"><?= htmlspecialchars($row->price) ?></div>
                            <div>
                                <a class="schedule-book-btn" href="<?= htmlspecialchars($row->bookUrl) ?>">
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
