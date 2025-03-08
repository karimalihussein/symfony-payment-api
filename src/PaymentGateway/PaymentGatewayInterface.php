<?php

namespace App\PaymentGateway;

use App\DTO\UnifiedResponse;

interface PaymentGatewayInterface
{
    public function getGatewayName(): string;
    public function processPayment(float $amount): UnifiedResponse;
}