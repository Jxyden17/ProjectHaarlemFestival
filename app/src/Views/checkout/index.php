<?php
$groups = is_array($groups ?? null) ? $groups : [];
$subtotal = (float) ($subtotal ?? 0);
?>

<div class="container-fluid px-0" style="background-color: #132746; min-height: 100vh;">
    <section class="py-4 py-lg-5" style="background: linear-gradient(rgba(7, 16, 38, 0.72), rgba(7, 16, 38, 0.92)), url('/img/home/home-banner.png') center/cover no-repeat;">
        <div class="container">
            <a href="/cart" class="btn btn-sm mb-3" style="background-color: transparent; color: #ffffff; border: 1px solid #ffffff; padding-inline: 1rem;">
                Back to Personal Program
            </a>
            <h1 class="display-3 fw-bold mb-2 text-white">Checkout</h1>
            <p class="fs-4 mb-0 text-white">Review your final order before confirming it.</p>
        </div>
    </section>

    <div class="container py-5">
        <div class="row g-4 align-items-start">
            <div class="col-lg-8">
                <section class="card border-0 shadow-sm h-100" style="border-radius: 0.5rem; background-color: #132746; border: 2px solid #d6a436;">
                    <div class="card-body p-4 p-lg-5">
                        <h2 class="display-5 mb-4" style="color: #f8f6f1;">Order Summary</h2>

                        <?php foreach ($groups as $group): ?>
                            <section class="mb-5">
                                <div class="d-flex justify-content-between align-items-start gap-3 border-bottom pb-3 mb-3" style="border-color: rgba(214, 164, 54, 0.35) !important;">
                                    <div>
                                        <h3 class="h2 mb-1" style="color: #f8f6f1;"><?= htmlspecialchars((string) ($group['title'] ?? 'Unknown Date')) ?></h3>
                                        <div class="small" style="color: rgba(255, 255, 255, 0.7);">
                                            <?= count($group['items'] ?? []) ?> ticket<?= count($group['items'] ?? []) === 1 ? '' : 's' ?>
                                        </div>
                                    </div>

                                    <div class="h3 mb-0" style="color: #d6a436;">&euro;<?= number_format((float) ($group['total'] ?? 0), 2) ?></div>
                                </div>

                                <div class="d-grid gap-3">
                                    <?php foreach (($group['items'] ?? []) as $item): ?>
                                        <?php
                                        $eventName = (string) ($item['event_name'] ?? '');
                                        $eventLabel = match (strtolower(str_replace(' ', '', $eventName))) {
                                            'tellingstory' => 'Stories',
                                            'astrollthroughhistory' => 'Tour',
                                            'dance' => 'Dance',
                                            'jazz' => 'Jazz',
                                            'yummy' => 'Yummy',
                                            default => $eventName !== '' ? $eventName : 'Event',
                                        };

                                        $itemTitle = trim((string) ($item['performer_names'] ?? ''));
                                        if ($itemTitle === '') {
                                            $itemTitle = 'Session #' . (int) ($item['session_id'] ?? 0);
                                        }

                                        $timeLabel = substr((string) ($item['start_time'] ?? ''), 0, 5);
                                        ?>
                                        <div class="border rounded-3 p-3 p-lg-4" style="border-color: rgba(214, 164, 54, 0.18) !important; background-color: #1a3154;">
                                            <div class="d-flex justify-content-between align-items-start gap-3">
                                                <div>
                                                    <div class="mb-3">
                                                        <span class="badge rounded-pill px-3 py-2" style="background-color: #7b8794; color: #ffffff; font-size: 0.8rem;">
                                                            <?= htmlspecialchars($eventLabel) ?>
                                                        </span>
                                                    </div>

                                                    <div class="h3 mb-1" style="color: #f8f6f1;"><?= htmlspecialchars($itemTitle) ?></div>
                                                    <div class="fs-5" style="color: rgba(255, 255, 255, 0.72);"><?= htmlspecialchars((string) ($item['venue_name'] ?? 'Unknown venue')) ?></div>
                                                    <div class="small mt-2" style="color: rgba(255, 255, 255, 0.6);"><?= htmlspecialchars($timeLabel) ?></div>
                                                </div>

                                                <div class="text-end" style="min-width: 110px;">
                                                    <div style="color: rgba(255, 255, 255, 0.65);">Tickets</div>
                                                    <div class="h4 mb-3" style="color: #f8f6f1;"><?= (int) ($item['quantity'] ?? 0) ?></div>
                                                    <div style="color: rgba(255, 255, 255, 0.65);">Line Total</div>
                                                    <div class="h3 mb-0" style="color: #d6a436;">&euro;<?= number_format((float) ($item['line_total'] ?? 0), 2) ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>

            <div class="col-lg-4">
                <section class="card border-0 shadow-sm" style="border-radius: 0.5rem; background-color: #132746; border: 2px solid #d6a436;">
                    <div class="card-body p-4">
                        <h2 class="display-5 mb-4" style="color: #f8f6f1;">Final Total</h2>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fs-4" style="color: #f8f6f1;">Subtotal</span>
                            <strong class="fs-3" style="color: #d6a436;">&euro;<?= number_format($subtotal, 2) ?></strong>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <span class="badge rounded-pill px-3 py-2" style="background-color: rgba(214, 164, 54, 0.14); color: #f4d27a;">iDEAL</span>
                            <span class="badge rounded-pill px-3 py-2" style="background-color: rgba(255, 255, 255, 0.08); color: #f8f6f1;">Visa</span>
                            <span class="badge rounded-pill px-3 py-2" style="background-color: rgba(255, 255, 255, 0.08); color: #f8f6f1;">Mastercard</span>
                            <span class="badge rounded-pill px-3 py-2" style="background-color: rgba(255, 255, 255, 0.08); color: #f8f6f1;">PayPal</span>
                        </div>

                        <form method="POST" action="/checkout/confirm">
                            <button type="submit" class="btn w-100 py-3 fs-5" style="background-color: #246df0; color: #ffffff; border: none;">
                                Continue to iDEAL
                            </button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
