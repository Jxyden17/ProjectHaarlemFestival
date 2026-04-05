<?php

namespace App\Controllers;

use App\Service\Interfaces\ICartService;

class CartController extends BaseController
{
    private ICartService $cartService;

    public function __construct(ICartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(): void
    {
        $this->requireAuth();

        $cartData = $this->cartService->getCartWithItems();

        $this->render('cart/index', [
            'title' => 'Shopping Cart',
            'cart' => $cartData['cart'],
            'items' => $cartData['items'],
            'groups' => $cartData['groups'],
            'subtotal' => $cartData['subtotal'],
        ]);
    }

    public function add(): void
    {
        $this->requireAuth();

        $sessionId = (int) ($_POST['session_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 1);
        $customPriceInput = $_POST['custom_price'] ?? null;
        $customPrice = $customPriceInput !== null && $customPriceInput !== '' ? (float) $customPriceInput : null;

        $this->cartService->addSessionToCart($sessionId, $quantity, $customPrice);

        header('Location: /cart');
        exit;
    }

    public function update(): void
    {
        $this->requireAuth();

        $cartItemId = (int) ($_POST['cart_item_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 0);

        $this->cartService->updateCartItemQuantity($cartItemId, $quantity);

        header('Location: /cart');
        exit;
    }

    public function remove(): void
    {
        $this->requireAuth();

        $cartItemId = (int) ($_POST['cart_item_id'] ?? 0);

        $this->cartService->removeCartItem($cartItemId);

        header('Location: /cart');
        exit;
    }
}
