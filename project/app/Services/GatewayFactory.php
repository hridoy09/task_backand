<?php

namespace App\Services;

use App\Services\Gateways\AamarpayGateway;
use App\Services\Gateways\BkashGateway;
use App\Services\Gateways\FlutterwaveGateway;
use App\Services\Gateways\InstamojoGateway;
use App\Services\Gateways\IyzicoGateway;
use App\Services\Gateways\KhaltiGateway;
use App\Services\Gateways\PaystackGateway;
use App\Services\Gateways\SslCommerzGateway;
use App\Services\Gateways\StripeGateway;
use App\Services\Gateways\PaymentGatewayInterface;
use App\Services\Gateways\PayPalGateway;
use App\Services\Gateways\RazorpayGateway;
use App\Services\Gateways\SurjopayGateway;
use App\Services\Gateways\TwoCheckoutGateway;
use App\Services\Gateways\WorldpayGateway;
use App\Services\Gateways\XenditGateway;
use Twilio\TwiML\Voice\Pay;

class GatewayFactory
{
    public static function make(string $gateway): PaymentGatewayInterface
    {
        return match ($gateway) {
            'stripe' => new StripeGateway(),
            '2checkout' => new TwoCheckoutGateway(),
            'iyzico' => new IyzicoGateway(),
            'worldpay' => new WorldpayGateway(),
            'paystack' => new PaystackGateway(),
            'sslcommerz' => new SslCommerzGateway(),
            'surjopay' => new SurjopayGateway(),
            'razorpay' => new RazorpayGateway(),
            'aamarpay' => new AamarpayGateway(),
            'authorizenet' => new AamarpayGateway(),
            'flutterwave' => new FlutterwaveGateway(),
            'instamojo' =>   new InstamojoGateway(),
            'khalti' => new KhaltiGateway(),
            'paypal' => new PayPalGateway(),
            'bkash' => new BkashGateway(),
            'xendit' => new XenditGateway(),
            default => throw new \Exception('Unsupported gateway'),
        };
    }
}
