<?php

namespace App\Services\Gateways;

use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class AuthorizeNetGateway extends Gateway implements PaymentGatewayInterface
{
    protected static $key = 'authorizenet';
    protected static $image = 'authorizenet.png';

    protected static $config = [
        'api_login_id'   => 'API Login ID',
        'transaction_key'=> 'Transaction Key',
        'sandbox_mode'   => 'Enable Sandbox Mode (yes/no)',
    ];

    public function getSupportedCurrencies(): array
    {
        // Authorize.Net mainly supports USD, CAD, GBP, EUR
        return ["USD", "CAD", "GBP", "EUR"];
    }

    protected function getEnvironment()
    {
        $config = self::dbConfig();
        $isSandbox = (isset($config->sandbox_mode) && strtolower($config->sandbox_mode) === 'yes');
        return $isSandbox
            ? \net\authorize\api\constants\ANetEnvironment::SANDBOX
            : \net\authorize\api\constants\ANetEnvironment::PRODUCTION;
    }

    public function create(Payment $payment): string
    {
        $charge = $this->prepareCharge($payment);

        $config = self::dbConfig();
        if (!$config || !isset($config->api_login_id) || !isset($config->transaction_key)) {
            Log::error('Authorize.Net: Missing configuration.');
            throw new \Exception('Authorize.Net gateway not configured properly.');
        }

        // Prepare merchant auth
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($config->api_login_id);
        $merchantAuthentication->setTransactionKey($config->transaction_key);

        $refId = 'REF' . $payment->id . Str::random(5);

        // Payment amount
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($charge['amount']);

        // Create request for hosted payment page
        $request = new AnetAPI\GetHostedPaymentPageRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);

        $transactionRequest = new AnetAPI\TransactionRequestType();
        $transactionRequest->setTransactionType("authCaptureTransaction");
        $transactionRequest->setAmount($charge['amount']);

        $request->setTransactionRequest($transactionRequest);

        $controller = new AnetController\GetHostedPaymentPageController($request);
        $response = $controller->executeWithApiResponse($this->getEnvironment());

        if ($response != null && $response->getMessages()->getResultCode() == "Ok") {
            $token = $response->getToken();

            $payment->transaction_id = $refId;
            $payment->meta = array_merge($payment->meta ?? [], [
                'authorizenet_token' => $token,
            ]);
            $payment->save();

            $redirectUrl = "https://accept.authorize.net/payment/payment/" . $token;
            return $redirectUrl;
        } else {
            $error = $response->getMessages()->getMessage()[0]->getText() ?? 'Unknown error';
            Log::error("Authorize.Net: Failed to create payment page. Error: " . $error);
            throw new \Exception('Authorize.Net payment initiation failed: ' . $error);
        }
    }

    public function verify($request)
    {
        $paymentIdEnc = $request->trx ?? null;
        try {
            $id = decrypt($paymentIdEnc);
        } catch (\Throwable $th) {
            return back()->withError('Payment Failed');
        }

        $payment = Payment::find($id);
        if (!$payment) {
            return back()->withError('Payment not found');
        }

        $config = self::dbConfig();
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($config->api_login_id);
        $merchantAuthentication->setTransactionKey($config->transaction_key);

        // Transaction ID should be stored in meta or response
        $transactionId = $payment->transaction_id;

        $request = new AnetAPI\GetTransactionDetailsRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setTransId($transactionId);

        $controller = new AnetController\GetTransactionDetailsController($request);
        $response = $controller->executeWithApiResponse($this->getEnvironment());

        if ($response != null && $response->getMessages()->getResultCode() == "Ok") {
            $transaction = $response->getTransaction();
            if ($transaction->getTransactionStatus() === "settledSuccessfully" ||
                $transaction->getTransactionStatus() === "capturedPendingSettlement") {
                $payment->paid_at = now();
                $payment->status = 'success';
                $payment->save();
                return $this->paymentSuccess($payment);
            } else {
                return back()->withError('Payment not completed. Status: ' . $transaction->getTransactionStatus());
            }
        } else {
            $error = $response->getMessages()->getMessage()[0]->getText() ?? 'Unknown error';
            Log::error("Authorize.Net: Failed to verify transaction. Error: " . $error);
            return back()->withError('Payment verification failed: ' . $error);
        }
    }
}
