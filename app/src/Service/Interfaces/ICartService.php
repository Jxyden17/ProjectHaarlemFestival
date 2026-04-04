<?php

namespace App\Service\Interfaces;

interface ICartService
{
    //Get the active cart for the current logged-in user, or create a new one if it doesn't exist
    public function getOrCreateActiveCart(): array;
    //Get the cart with its items for the current logged-in user
    public function getCartWithItems(): array;
    //Add a session to the cart with the specified quantity
    public function addSessionToCart(int $sessionId, int $quantity = 1, ?float $customPrice = null): void;
    //Update the quantity of a cart item
    public function updateCartItemQuantity(int $cartItemId, int $quantity): void;
    //Remove a cart item from the cart
    public function removeCartItem(int $cartItemId): void;
    //Clear all items from the cart
    public function getCartItemCount(): int;
    //Get the subtotal of the cart by summing the price of all items multiplied by their quantity
    public function getCartSubtotal(): float;
    //Get the session details for a given session ID, used for validation and price calculation
    public function getSessionForBooking(int $sessionId): ?array;


}
