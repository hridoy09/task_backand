<?php

namespace App\Services\Gateways;

use App\Models\Payment;

interface PaymentGatewayInterface
{
    
    public function create(Payment $payment): string;
    
    public function verify($mixed);

    public function getSupportedCurrencies(): array;
}
