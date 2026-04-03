<?php

namespace App\Service;

use App\Repository\Interfaces\ICartRepository;
use App\Repository\Interfaces\ICheckoutRepository;
use App\Service\Interfaces\ICartService;
use App\Service\Interfaces\ICheckoutService;

class CheckoutService implements ICheckoutService
{
    private ICartService $cartService;
    private ICartRepository $cartRepository;
    private ICheckoutRepository $checkoutRepository;

    public function __construct(
        ICartService $cartService,
        ICartRepository $cartRepository,
        ICheckoutRepository $checkoutRepository
    ) {
        $this->cartService = $cartService;
        $this->cartRepository = $cartRepository;
        $this->checkoutRepository = $checkoutRepository;
    }

    public function getCheckoutData(): array
    {
        $cartData = $this->cartService->getCartWithItems();

        if (($cartData['items'] ?? []) === []) {
            throw new \RuntimeException('Your cart is empty.');
        }

        $this->validateItemsAgainstLatestStock($cartData['items']);

        return $cartData;
    }

    public function confirmCheckout(): array
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            throw new \RuntimeException('Please log in to complete checkout.');
        }

        $cartData = $this->getCheckoutData();
        $cartId = (int) ($cartData['cart']['id'] ?? 0);
        $subtotal = (float) ($cartData['subtotal'] ?? 0);

        if ($cartId <= 0) {
            throw new \RuntimeException('Active cart not found.');
        }

        if ($subtotal <= 0) {
            throw new \RuntimeException('Order total must be greater than zero.');
        }

        $orderId = $this->checkoutRepository->createOrder($userId, $subtotal);

        return [
            'order_id' => $orderId,
            'cart_id' => $cartId,
        ];
    }

    private function validateItemsAgainstLatestStock(array $items): void
    {
        foreach ($items as $item) {
            $sessionId = (int) ($item['session_id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);
            $session = $this->cartRepository->findSessionById($sessionId);

            if ($session === null) {
                throw new \RuntimeException('One of the selected sessions no longer exists.');
            }

            $remainingSpots = (int) $session['available_spots'] - (int) $session['amount_sold'];
            if ($quantity > $remainingSpots) {
                throw new \RuntimeException('One of the selected sessions no longer has enough available spots.');
            }
        }
    }
}
