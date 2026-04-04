<?php

namespace App\Repository\Interfaces;

interface ICheckoutRepository
{
    public function createOrder(int $userId, float $totalAmount): int;

    public function markCartAsPaid(int $cartId): void;
}
