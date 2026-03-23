<?php

namespace App\Service\Interfaces;

interface ICheckoutService
{
    public function getCheckoutData(): array;

    public function confirmCheckout(): int;
}
