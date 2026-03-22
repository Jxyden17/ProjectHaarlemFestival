<?php

namespace App\Service\Interfaces;

interface IPaymentService
{
    //Create a payment for the given order ID and return the payment URL for the user to complete the payment
    public function createPayment(int $orderId): string;
    //Handle the return from the payment provider after the user completes or cancels the payment, and return an array with the payment status and any relevant information
    public function handleReturn(string $providerPaymentId): array;
    //Handle webhook notifications from the payment provider about payment status changes, and update the order and payment records accordingly
    public function handleWebhook(string $providerPaymentId): void;
    //Get the current payment status for a given provider payment ID
    public function getPaymentStatus(string $providerPaymentId): string;
}
