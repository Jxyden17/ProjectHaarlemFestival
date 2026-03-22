<?php

namespace App\Controllers;

use App\Service\Interfaces\ICheckoutService;

class CheckoutController extends BaseController
{
    private ICheckoutService $checkoutService;

    public function __construct(ICheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function index(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        try {
            $checkoutData = $this->checkoutService->getCheckoutData();
        } catch (\RuntimeException $e) {
            http_response_code(400);
            $this->render('shared/error', [
                'errorTitle' => 'Checkout unavailable',
                'errorMessage' => $e->getMessage(),
            ]);
            return;
        }

        $this->render('checkout/index', [
            'title' => 'Checkout',
            'cart' => $checkoutData['cart'],
            'items' => $checkoutData['items'],
            'groups' => $checkoutData['groups'],
            'subtotal' => $checkoutData['subtotal'],
        ]);
    }

    public function confirm(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        try {
            $orderId = $this->checkoutService->confirmCheckout();
        } catch (\RuntimeException $e) {
            http_response_code(400);
            $this->render('shared/error', [
                'errorTitle' => 'Checkout unavailable',
                'errorMessage' => $e->getMessage(),
            ]);
            return;
        }

        $this->render('checkout/success', [
            'title' => 'Order Confirmed',
            'orderId' => $orderId,
        ]);
    }
}
