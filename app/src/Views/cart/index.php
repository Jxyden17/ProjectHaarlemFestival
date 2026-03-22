<div class="container py-5">
    <section class="mb-5">
        <a href="/" class="btn btn-outline-light btn-sm mb-3">Back to Events</a>
        <h1 class="display-4 mb-2">Personal Program</h1>
        <p class="lead mb-0">All tickets that you are interested in are here</p>
    </section>

    <?php if (empty($items)): ?>
        <section class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 px-4">
                <h2 class="display-6 mb-3">Your personal program is empty</h2>
                <p class="lead text-muted mb-4">You have not added any tickets yet.</p>
                <a href="/" class="btn btn-primary px-4">View Events</a>
            </div>
        </section>
    <?php else: ?>
        <section class="card border-0 shadow-sm">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                    <div>
                        <h2 class="h1 mb-1">Your Personal Schedule</h2>
                        <p class="text-muted mb-0">Review, update, and prepare your festival tickets.</p>
                    </div>

                    <div class="text-end">
                        <div class="small text-muted">Total</div>
                        <div class="fs-2 fw-bold">&euro;<?= number_format((float) $subtotal, 2) ?></div>
                    </div>
                </div>

                <?php foreach ($groups as $group): ?>
                    <section class="mb-5">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                            <div>
                                <h3 class="h4 mb-1"><?= htmlspecialchars((string) ($group['title'] ?? 'Unknown Date')) ?></h3>
                                <div class="small text-muted">
                                    <?= count($group['items'] ?? []) ?> ticket<?= count($group['items'] ?? []) === 1 ? '' : 's' ?> scheduled
                                </div>
                            </div>

                            <div class="text-end">
                                <div class="small text-muted">Day Total</div>
                                <div class="fw-bold">&euro;<?= number_format((float) ($group['total'] ?? 0), 2) ?></div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Event</th>
                                        <th scope="col">Time</th>
                                        <th scope="col">Location</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Tickets</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Total</th>
                                        <th scope="col" class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (($group['items'] ?? []) as $item): ?>
                                        <?php
                                        $eventName = (string) ($item['event_name'] ?? '');
                                        $eventLabel = match (strtolower(str_replace(' ', '', $eventName))) {
                                            'tellingstory' => 'Stories',
                                            'astrollthroughhistory' => 'Tour',
                                            'dance' => 'Dance',
                                            'jazz' => 'Jazz',
                                            default => $eventName !== '' ? $eventName : 'Event',
                                        };

                                        $itemTitle = (string) ($item['performer_names'] ?? '');
                                        if ($itemTitle === '') {
                                            $itemTitle = 'Session #' . (int) $item['session_id'];
                                        }

                                        $languageLabel = trim((string) ($item['label'] ?? ''));
                                        $timeLabel = substr((string) ($item['start_time'] ?? ''), 0, 5);
                                        ?>
                                        <tr>
                                            <td>
                                                <span class="badge rounded-pill text-bg-secondary">
                                                    <?= htmlspecialchars($eventLabel) ?>
                                                </span>
                                            </td>

                                            <td>
                                                <div class="fw-semibold"><?= htmlspecialchars($timeLabel) ?></div>
                                                <?php if ($languageLabel !== ''): ?>
                                                    <div class="small text-muted"><?= htmlspecialchars($languageLabel) ?></div>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars((string) ($item['venue_name'] ?? 'Unknown venue')) ?>
                                            </td>

                                            <td>
                                                <div class="fw-semibold"><?= htmlspecialchars($itemTitle) ?></div>
                                                <div class="small text-muted">
                                                    Session #<?= (int) $item['session_id'] ?>
                                                </div>
                                            </td>

                                            <td style="min-width: 180px;">
                                                <div class="d-inline-flex align-items-center gap-2">
                                                    <form method="POST" action="/cart/update" class="m-0">
                                                        <input type="hidden" name="cart_item_id" value="<?= (int) $item['id'] ?>">
                                                        <input type="hidden" name="quantity" value="<?= max(0, (int) $item['quantity'] - 1) ?>">
                                                        <button type="submit" class="btn btn-outline-secondary btn-sm" aria-label="Decrease quantity">
                                                            -
                                                        </button>
                                                    </form>

                                                    <span class="fw-semibold px-2" aria-label="Current quantity">
                                                        <?= (int) $item['quantity'] ?>
                                                    </span>

                                                    <form method="POST" action="/cart/update" class="m-0">
                                                        <input type="hidden" name="cart_item_id" value="<?= (int) $item['id'] ?>">
                                                        <input type="hidden" name="quantity" value="<?= (int) $item['quantity'] + 1 ?>">
                                                        <button type="submit" class="btn btn-outline-secondary btn-sm" aria-label="Increase quantity">
                                                            +
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>

                                            <td>
                                                <?php
                                                $priceValue = (float) ($item['unit_price'] ?? 0);
                                                ?>
                                                <?php if ($priceValue <= 0): ?>
                                                    <span class="text-warning fw-semibold">Pay as you like</span>
                                                <?php else: ?>
                                                    <span>&euro;<?= number_format($priceValue, 2) ?></span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="fw-bold">
                                                &euro;<?= number_format((float) ($item['line_total'] ?? 0), 2) ?>
                                            </td>

                                            <td class="text-end">
                                                <form method="POST" action="/cart/remove">
                                                    <input type="hidden" name="cart_item_id" value="<?= (int) $item['id'] ?>">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endforeach; ?>

                <div class="border-top pt-4 mt-4">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-6">
                            <div class="form-check bg-light rounded p-3">
                                <input class="form-check-input" type="checkbox" value="" id="program-confirm" disabled>
                                <label class="form-check-label" for="program-confirm">
                                    I confirm: all participants are 12+ years old and no strollers.
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-6 text-lg-end">
                            <div class="mb-3">
                                <div class="small text-muted">Grand Total</div>
                                <div class="display-6 fw-bold mb-0">&euro;<?= number_format((float) $subtotal, 2) ?></div>
                            </div>

                            <button type="button" class="btn btn-primary px-5" disabled>
                                Buy Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>
</div>
