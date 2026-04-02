<?php

namespace App\Controllers;

use App\Service\Interfaces\ICheckoutService;
use App\Service\Interfaces\IPaymentService;

class CheckoutController extends BaseController
{
    private ICheckoutService $checkoutService;
    private IPaymentService $paymentService;

    public function __construct(ICheckoutService $checkoutService, IPaymentService $paymentService)
    {
        $this->checkoutService = $checkoutService;
        $this->paymentService = $paymentService;
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
            $checkoutUrl = $this->paymentService->createPayment($orderId);
        } catch (\Throwable $e) {
            http_response_code(400);
            $this->render('shared/error', [
                'errorTitle' => 'Checkout unavailable',
                'errorMessage' => $e->getMessage(),
            ]);
            return;
        }

        header('Location: ' . $checkoutUrl);
        exit;
    }
}
