<?php

namespace App\Repository\Interfaces;

interface ICheckoutRepository
{
    public function createOrder(int $userId, float $totalAmount): int;

    public function markCartAsConverted(int $cartId): void;
}
