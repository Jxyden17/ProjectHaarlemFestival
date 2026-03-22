<div class="container py-5">
    <div class="d-flex justify-content-end mb-4">
        <a href="/" class="btn btn-outline-light">Continue Shopping</a>
    </div>

    <?php if (empty($items)): ?>
        <section class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 px-4">
                <h1 class="display-5 mb-3">Your cart is empty</h1>
                <p class="lead text-muted mb-4">You have not added any tickets yet.</p>
                <a href="/" class="btn btn-primary px-4">View Events</a>
            </div>
        </section>
    <?php else: ?>
        <section class="mb-5">
            <h1 class="display-4 mb-2">Book Tickets</h1>
            <p class="lead mb-0">You can first add tickets to the list then buy it all in one go</p>
        </section>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="h2 mb-4">Your cart</h2>

                        <?php foreach ($items as $item): ?>
                            <article class="border rounded p-3 mb-3">
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-5">
                                        <h3 class="h5 mb-2">Session #<?= htmlspecialchars((string) $item['session_id']) ?></h3>

                                        <div class="small text-muted">
                                            <div>Date: <?= htmlspecialchars((string) ($item['date'] ?? '')) ?></div>
                                            <div>Time: <?= htmlspecialchars((string) ($item['start_time'] ?? '')) ?></div>

                                            <?php if (!empty($item['label'])): ?>
                                                <div>Language: <?= htmlspecialchars((string) $item['label']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="small text-muted mb-1">Price</div>
                                        <div class="fw-semibold">&euro;<?= number_format((float) $item['unit_price'], 2) ?></div>
                                    </div>

                                    <div class="col-md-3">
                                        <form method="POST" action="/cart/update">
                                            <input type="hidden" name="cart_item_id" value="<?= (int) $item['id'] ?>">

                                            <label for="quantity-<?= (int) $item['id'] ?>" class="form-label small text-muted">
                                                Quantity
                                            </label>

                                            <div class="input-group">
                                                <input
                                                    id="quantity-<?= (int) $item['id'] ?>"
                                                    type="number"
                                                    name="quantity"
                                                    min="0"
                                                    value="<?= (int) $item['quantity'] ?>"
                                                    class="form-control"
                                                >
                                                <button type="submit" class="btn btn-outline-secondary">
                                                    Update
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="col-md-2 text-md-end">
                                        <div class="small text-muted mb-1">Total</div>
                                        <div class="fw-bold mb-3">&euro;<?= number_format((float) $item['line_total'], 2) ?></div>

                                        <form method="POST" action="/cart/remove">
                                            <input type="hidden" name="cart_item_id" value="<?= (int) $item['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h2 mb-4">Total</h2>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fs-5">Subtotal</span>
                            <strong class="fs-2">&euro;<?= number_format((float) $subtotal, 2) ?></strong>
                        </div>

                        <div class="form-check bg-light rounded p-3 mb-4">
                            <input class="form-check-input" type="checkbox" value="" id="cart-confirm" disabled>
                            <label class="form-check-label" for="cart-confirm">
                                I confirm: all participants are 12+ years old and no strollers.
                            </label>
                        </div>

                        <button type="button" class="btn btn-secondary w-100" disabled>
                            Checkout coming soon
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
