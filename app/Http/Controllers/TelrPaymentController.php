<?php

namespace App\Http\Controllers;

use App\Models\PaymentRequest;
use App\Traits\Processor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class TelrPaymentController extends Controller
{
    use Processor;

    private $config_values;
    private string $mode = 'live';
    private string $base_url = 'https://secure.telr.com/gateway/order.json';
    private PaymentRequest $payment;

    public function __construct(PaymentRequest $payment)
    {
        $this->payment = $payment;
        $config = $this->payment_config('telr', 'payment_config');

        if ($config) {
            $this->mode = $config->mode;
            $values = $this->mode === 'live' ? $config->live_values : $config->test_values;
            $this->config_values = json_decode($values);
        }
    }

    public function payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $this->error_processor($validator)), 400);
        }

        $data = $this->payment::where(['id' => $request['payment_id'], 'is_paid' => 0])->first();
        if (!$data) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
        }

        // Generate cart ID (exactly 32 characters)
        $cartId = str_replace('-', '', $request['payment_id']);
        $cartId = substr($cartId, 0, 32);

        $params = [
            'ivp_method'   => 'create',
            'ivp_store'    => $this->config_values->store_id,
            'ivp_authkey'  => $this->config_values->auth_key,
            'ivp_cart'     => $cartId,
            'ivp_test'     => $this->mode === 'test' ? 1 : 0,
            'ivp_amount'   => number_format((float)$data->payment_amount, 2, '.', ''),
            'ivp_currency' => strtoupper($data->currency_code ?: 'USD'),
            'ivp_desc'     => 'Payment for order: ' . $data->id,
            'return_auth'  => route('telr.success', ['payment_id' => $data->id]),
            'return_decl'  => route('telr.cancel', ['payment_id' => $data->id]),
            'return_can'   => route('telr.cancel', ['payment_id' => $data->id]),
            'bill_email'   => $data->customer_email,
            'bill_fname'   => $data->customer_name,
        ];

        Log::info('Telr Payment Request:', $params);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->base_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode != 200) {
            Log::error('Telr API Error: HTTP ' . $httpCode);
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, ['error' => 'Telr gateway unavailable']), 400);
        }

        $result = json_decode($response, true);

        if (isset($result['order']['url'])) {
            return Redirect::away($result['order']['url']);
        }

        Log::error('Telr Order Creation Failed:', $result);
        return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $result['error'] ?? ['message' => 'Unknown error']), 400);
    }

    public function success(Request $request)
    {
        Log::info('Telr Success Callback:', $request->all());

        $payment = $this->payment::where('id', $request->payment_id)->first();
        if (!$payment) {
            Log::error('Payment not found for ID:', ['payment_id' => $request->payment_id]);
            return $this->payment_response(null, 'fail');
        }

        // Verify payment with Telr
        $verifyParams = [
            'ivp_method' => 'check',
            'ivp_store' => $this->config_values->store_id,
            'ivp_authkey' => $this->config_values->auth_key,
            'order_ref' => $request->order_ref,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->base_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($verifyParams),
        ]);

        $verifyResponse = curl_exec($ch);
        curl_close($ch);

        $verifyResult = json_decode($verifyResponse, true);
        Log::info('Telr Verification Response:', $verifyResult);

        if (isset($verifyResult['order']['status']['code']) && $verifyResult['order']['status']['code'] == 3) {
            $payment->update([
                'payment_method' => 'telr',
                'is_paid' => 1,
                'transaction_id' => $request->order_ref,
            ]);

            if (function_exists($payment->success_hook)) {
                call_user_func($payment->success_hook, $payment);
            }

            return $this->payment_response($payment, 'success');
        }

        if (function_exists($payment->failure_hook)) {
            call_user_func($payment->failure_hook, $payment);
        }

        return $this->payment_response($payment, 'fail');
    }

    public function cancel(Request $request)
    {
        Log::info('Telr Cancel Callback:', $request->all());

        $payment = $this->payment::where('id', $request->payment_id)->first();
        if (!$payment) {
            return $this->payment_response(null, 'cancel');
        }

        if (function_exists($payment->failure_hook)) {
            call_user_func($payment->failure_hook, $payment);
        }

        return $this->payment_response($payment, 'cancel');
    }
}