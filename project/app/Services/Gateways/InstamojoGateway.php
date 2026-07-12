<?php

namespace App\Services\Gateways;

use App\Models\Payment;
use Instamojo\Instamojo;
use Illuminate\Http\Request;

class InstamojoGateway extends Gateway implements PaymentGatewayInterface
{
    /**
     * The internal key for the gateway.
     *
     * @var string
     */
    protected static $key = 'instamojo';

    /**
     * The publicly visible name of the gateway.
     *
     * @var string
     */
    protected static $image = 'instamojo.png';

    /**
     * The configuration fields for the gateway.
     *
     * @var array
     */
    protected static $config = [
        'api_key' => 'Api Key',
        'auth_token' => 'Auth Token',
        'private_salt' => 'Private Salt',
    ];

    /**
     * Get the supported currencies for this gateway.
     *
     * @return array
     */
    public function getSupportedCurrencies(): array
    {
        return ["INR"];
    }

    public function create($payment): string
    {
        $charge = $this->prepareCharge($payment);

        $api = $this->getApi();

        try {
            $response = $api->paymentRequestCreate([
                'purpose'                 => 'Payment to ' . generalSetting('site_title'),
                'amount'                  => $charge['amount'],
                'buyer_name'              => $payment->user->name,
                'email'                   => $payment->user->email,
                'redirect_url'            => route('payment.notify', ['gateway' => 'instamojo', 'trx' => encrypt($payment->id)]),
                'allow_repeated_payments' => false
            ]);

            $payment->meta = array_merge($payment->meta ?? [], [
                'payment_request_id' => $response['id'],
            ]);
            $payment->save();

            return redirect()->away($response['longurl']);
        } catch (\Exception $e) {
            return back()->withError('Something went wrong, please try again later.');
        }
    }

    public function verify($request)
    {
        $data = $request->all();
        $privateSalt = self::dbConfig()?->private_salt;
        $mac = $data['mac'];
        unset($data['mac']);
        ksort($data, SORT_STRING);
        $mac_calculated = hash_hmac("sha1", implode("|", $data), $privateSalt);

        if ($mac !== $mac_calculated) {
            return back()->withError('Payment Failed: Invalid MAC');
        }

        try {
            $id = decrypt($request->trx);
        } catch (\Throwable $th) {
            return back()->withError('Payment Failed');
        }

        $payment = Payment::where('id', $id)->first();

        if (!$payment) {
            return back()->withError('Payment not found');
        }

        if ($request->payment_request_id !== $payment->meta['payment_request_id']) {
            return back()->withError('Payment Failed: Invalid Payment Request ID');
        }

        if ($data['status'] === 'Credit') {
            $payment->paid_at = now();
            $payment->status = 'success';
            $payment->meta = array_merge($payment->meta ?? [], ['payment_id' => $request->payment_id]);
            $payment->save();

            return $this->paymentSuccess($payment);
        }

        return back()->withError('Payment not completed');
    }

    /**
     * Get the Instamojo API instance.
     *
     * @return \Instamojo\Instamojo
     */
    private function getApi()
    {
        $config = self::dbConfig();
        // Use 'https://test.instamojo.com/api/1.1/' for the test environment
        $testMode = generalSetting('instamojo_test_mode') ?? false; 
        $apiEndpoint = $testMode ? 'https://test.instamojo.com/api/1.1/' : 'https://www.instamojo.com/api/1.1/';

        return new Instamojo(
            $config?->api_key,
            $config?->auth_token,
            $apiEndpoint
        );
    }
}
