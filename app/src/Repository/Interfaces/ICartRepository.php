<?php

namespace App\Repository\Interfaces;

interface ICartRepository
{
    //Search Cart by user id that is logged in
    public function findActiveCartByUserId(int $userId): ?array;
    //Search Cart by guest token for users that are not logged in
    public function findActiveCartByGuestToken(string $guestToken): ?array;
    //Create a new cart for a user or guest
    public function createCart(?int $userId, ?string $guestToken): int;
    //Find cart items by cart id
    public function findCartItemsByCartId(int $cartId): array;
    //Check if the session is already in the cart
    public function findCartItemByCartIdAndSessionId(int $cartId, int $sessionId): ?array;
    //Find a cart item by its id
    public function findCartItemById(int $cartItemId): ?array;
    //Add a session to the cart with the specified quantity and unit price
    public function addCartItem(int $cartId, int $sessionId, int $quantity, float $unitPrice): void;
    //Update the quantity of a cart item
    public function updateCartItemQuantity(int $cartItemId, int $quantity): void;
    //Update both quantity and unit price of a cart item
    public function updateCartItem(int $cartItemId, int $quantity, float $unitPrice): void;
    //Remove a cart item from the cart
    public function removeCartItem(int $cartItemId): void;
    //Clear all items from the cart
    public function clearCart(int $cartId): void;
    //Obtain the session real for validation and price calculation
    public function findSessionById(int $sessionId): ?array;
}
