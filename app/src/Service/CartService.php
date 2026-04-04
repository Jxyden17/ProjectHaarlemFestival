<?php

namespace App\Service;

use App\Repository\Interfaces\ICartRepository;
use App\Service\Interfaces\ICartService;

class CartService implements ICartService
{
    private ICartRepository $cartRepository;

    public function __construct(ICartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function getOrCreateActiveCart(): array
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        if ($userId <= 0) {
            throw new \RuntimeException('Please log in to manage your shopping cart.');
        }

        $cart = $this->cartRepository->findActiveCartByUserId($userId);
        if ($cart) {
            return $cart;
        }

        $cartId = $this->cartRepository->createCart($userId);

        return [
            'id' => $cartId,
            'user_id' => $userId,
            'status' => 'active',
        ];
    }

    public function getCartWithItems(): array
    {
        $cart = $this->getOrCreateActiveCart();
        $items = $this->cartRepository->findCartItemsByCartId((int) $cart['id']);

        $subtotal = 0.0;
        $groups = [];

        foreach ($items as &$item) {
            $lineTotal = (float) $item['unit_price'] * (int) $item['quantity'];
            $item['line_total'] = $lineTotal;
            $item['remaining_spots'] = max(0, (int) ($item['available_spots'] ?? 0) - (int) ($item['amount_sold'] ?? 0));
            $subtotal += $lineTotal;

            $dateKey = (string) ($item['date'] ?? '');
            if ($dateKey === '') {
                $dateKey = 'unknown';
            }

            if (!isset($groups[$dateKey])) {
                $groups[$dateKey] = [
                    'date' => $dateKey,
                    'title' => $this->formatGroupDateLabel($dateKey),
                    'items' => [],
                    'total' => 0.0,
                ];
            }

            $groups[$dateKey]['items'][] = $item;
            $groups[$dateKey]['total'] += $lineTotal;
        }

        return [
            'cart' => $cart,
            'items' => $items,
            'groups' => array_values($groups),
            'subtotal' => $subtotal,
        ];
    }
    private function formatGroupDateLabel(string $date): string
    {
        if ($date === 'unknown' || trim($date) === '') {
            return 'Unknown Date';
        }

        try {
            $dateTime = new \DateTime($date);
            return $dateTime->format('l - F j, Y');
        } catch (\Throwable $e) {
            return $date;
        }
    }


    public function addSessionToCart(int $sessionId, int $quantity = 1, ?float $customPrice = null): void
    {
        if ($sessionId <= 0) {
            throw new \InvalidArgumentException('Invalid session.');
        }

        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be at least 1.');
        }

        $session = $this->cartRepository->findSessionById($sessionId);
        if (!$session) {
            throw new \RuntimeException('Session not found.');
        }

        $availableSpots = (int) $session['available_spots'];
        $amountSold = (int) $session['amount_sold'];
        $remainingSpots = $availableSpots - $amountSold;

        if ($remainingSpots <= 0) {
            throw new \RuntimeException('This session is sold out.');
        }

        $pricingType = (string) ($session['pricing_type'] ?? 'fixed');
        $minimumPrice = isset($session['minimum_price']) ? (float) $session['minimum_price'] : null;

        if ($pricingType === 'pay_as_you_like') {
            if ($customPrice === null) {
                throw new \RuntimeException('Please choose an amount for this session.');
            }

            if ($minimumPrice !== null && $customPrice < $minimumPrice) {
                throw new \RuntimeException('Minimum amount is €' . number_format($minimumPrice, 2) . '.');
            }

            $unitPrice = $customPrice;
        } else {
            $unitPrice = (float) $session['price'];
        }

        $cart = $this->getOrCreateActiveCart();
        $existingItem = $this->cartRepository->findCartItemByCartIdAndSessionId((int) $cart['id'], $sessionId);

        if ($existingItem) {
            $newQuantity = (int) $existingItem['quantity'] + $quantity;

            if ($newQuantity > $remainingSpots) {
                throw new \RuntimeException('Not enough spots available.');
            }

            if ($pricingType === 'pay_as_you_like') {
                $this->cartRepository->updateCartItem((int) $existingItem['id'], $newQuantity, $unitPrice);
                return;
            }

            $this->cartRepository->updateCartItemQuantity((int) $existingItem['id'], $newQuantity);
            return;
        }

        if ($quantity > $remainingSpots) {
            throw new \RuntimeException('Not enough spots available.');
        }

        $this->cartRepository->addCartItem(
            (int) $cart['id'],
            $sessionId,
            $quantity,
            $unitPrice
        );
    }

    public function updateCartItemQuantity(int $cartItemId, int $quantity): void
    {
        if ($cartItemId <= 0) {
            throw new \InvalidArgumentException('Invalid cart item.');
        }

        if ($quantity <= 0) {
            $this->cartRepository->removeCartItem($cartItemId);
            return;
        }

        $cartItem = $this->cartRepository->findCartItemById($cartItemId);
        if (!$cartItem) {
            throw new \RuntimeException('Cart item not found.');
        }

        $sessionId = (int) ($cartItem['session_id'] ?? 0);
        $session = $this->cartRepository->findSessionById($sessionId);
        if (!$session) {
            throw new \RuntimeException('Session not found.');
        }

        $remainingSpots = (int) ($session['available_spots'] ?? 0) - (int) ($session['amount_sold'] ?? 0);
        if ($quantity > $remainingSpots) {
            throw new \RuntimeException('Not enough spots available.');
        }

        $this->cartRepository->updateCartItemQuantity($cartItemId, $quantity);
    }

    public function removeCartItem(int $cartItemId): void
    {
        if ($cartItemId <= 0) {
            throw new \InvalidArgumentException('Invalid cart item.');
        }

        $this->cartRepository->removeCartItem($cartItemId);
    }

    public function getCartItemCount(): int
    {
        $cartData = $this->getCartWithItems();
        $count = 0;

        foreach ($cartData['items'] as $item) {
            $count += (int) $item['quantity'];
        }

        return $count;
    }

    public function getCartSubtotal(): float
    {
        $cartData = $this->getCartWithItems();

        return (float) $cartData['subtotal'];
    }

    public function getSessionForBooking(int $sessionId): ?array
    {
        if ($sessionId <= 0) {
            return null;
        }

        return $this->cartRepository->findSessionById($sessionId);
    }

}
