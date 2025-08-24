<?php

namespace App\Http\Controllers;

use App\Models\PaymentRequest;
use App\Traits\Processor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class TelrPaymentController extends Controller
{
    use Processor;

    /** @var object|null */
    private $config_values;          // store_id, auth_key
    /** @var string */
    private string $mode = 'live';   // 'test' | 'live'
    /** @var string */
    private string $base_url = 'https://secure.telr.com/gateway/order.json';
    /** @var PaymentRequest */
    private PaymentRequest $payment;

    public function __construct(PaymentRequest $payment)
    {
        $this->payment = $payment;

        // payment_config('telr','payment_config') should return an object with: mode, live_values, test_values
        $config = $this->payment_config('telr', 'payment_config');

        if ($config) {
            $this->mode = $config->mode ?? 'live';

            // pick the right credentials for the active mode
            $this->config_values = json_decode(
                $this->mode === 'live' ? $config->live_values : $config->test_values
            );
        }
    }

    /** Telr does not use OAuth; helper kept for parity */
    public function generateAuthKey(): string
    {
        return base64_encode(($this->config_values->store_id ?? '') . ':' . ($this->config_values->auth_key ?? ''));
    }

    /** Create an order on Telr and redirect the customer */
    public function payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|uuid',
        ]);

        if ($validator->fails()) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $this->error_processor($validator)), 400);
        }

        $data = $this->payment::where(['id' => $request['payment_id'], 'is_paid' => 0])->first();
        if (!$data) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
        }

        // Build Telr create request
        $params = [
            'ivp_method'   => 'create',
            'ivp_store'    => $this->config_values->store_id ?? '',
            'ivp_authkey'  => $this->config_values->auth_key ?? '',
            'ivp_cart'     => substr(preg_replace('/[^A-Za-z0-9]/', '', (string)$data->id), 0, 32),
            'ivp_test'     => ($this->mode === 'test') ? 1 : 0,                    // <- FIXED: use config mode
            'ivp_amount'   => round((float)$data->payment_amount, 2),
            'ivp_currency' => $data->currency_code ?: 'AED',                       // use your default ISO (AED/USD)
            'ivp_desc'     => 'Payment ID: ' . $data->id,
            // Telr will redirect the customer here
            'return_auth'  => route('telr.success'),
            'return_decl'  => route('telr.cancel'),
            'return_can'   => route('telr.cancel'),
            // Optional billing fields
            'bill_city'    => $data->customer_city ?? '',
            'bill_country' => $data->customer_country ?? '',
            'bill_email'   => $data->customer_email ?? '',
            'bill_fname'   => $data->customer_name ?? '',
            'bill_title'   => 'Payment',
        ];

        \Log::info('Telr create params', $params);

        // cURL call
        $ch = curl_init($this->base_url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($params),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT        => 45,
            // In prod you should verify peer; keep false only if CA bundle not present
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $raw = curl_exec($ch);
        if (curl_errno($ch)) {
            \Log::error('Telr create curl error', ['error' => curl_error($ch)]);
            curl_close($ch);
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_404, null, ['error' => 'Gateway not reachable']), 404);
        }
        curl_close($ch);

        $res = json_decode($raw, true);
        \Log::info('Telr create response', ['raw' => $raw, 'json' => $res]);

        if (!empty($res['order']['url'])) {
            return Redirect::away($res['order']['url']);
        }

        $error = $res['error'] ?? ['message' => 'Unknown error from Telr'];
        return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $error), 400);
    }

    /** Customer cancelled/declined */
    public function cancel(Request $request)
{
    // Telr may post back without payment_id; try cart id
    $cartId  = $request->input('cartid', $request->input('cart_id'));
    $payment = null;

    if ($cartId) {
        // our ivp_cart is UUID without dashes
        $payment = $this->payment::whereRaw("REPLACE(id, '-', '') = ?", [$cartId])->first();
    }

    if (!$payment && $request->filled('payment_id')) {
        $payment = $this->payment::find($request['payment_id']);
    }

    return $this->payment_response($payment, 'cancel');
}


    /** Customer returned from Telr; verify the order */
    public function success(Request $request)
{
    $ref    = $request->input('order_ref');
    $cartId = $request->input('cartid', $request->input('cart_id'));

    if (!$ref || !$cartId) {
        \Log::warning('Telr return missing params', $request->all());

        $payment = null;
        if ($cartId) {
            $payment = $this->payment::whereRaw("REPLACE(id, '-', '') = ?", [$cartId])->first();
        } elseif ($request->filled('payment_id')) {
            $payment = $this->payment::find($request['payment_id']);
        }

        if ($payment && function_exists($payment->failure_hook)) {
            call_user_func($payment->failure_hook, $payment);
        }
        return $this->payment_response($payment, 'fail');
    }

    // verify with Telr
    $verify = [
        'ivp_method'  => 'check',
        'ivp_store'   => $this->config_values->store_id ?? '',
        'ivp_authkey' => $this->config_values->auth_key ?? '',
        'order_ref'   => $ref,
    ];

    $ch = curl_init($this->base_url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($verify),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_TIMEOUT        => 45,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $raw = curl_exec($ch);
    if (curl_errno($ch)) {
        \Log::error('Telr check curl error', ['error' => curl_error($ch)]);
        curl_close($ch);

        $payment = $this->payment::whereRaw("REPLACE(id, '-', '') = ?", [$cartId])->first();
        if ($payment && function_exists($payment->failure_hook)) {
            call_user_func($payment->failure_hook, $payment);
        }
        return $this->payment_response($payment, 'fail');
    }
    curl_close($ch);

    $res = json_decode($raw, true);
    \Log::info('Telr check response', ['raw' => $raw, 'json' => $res]);

    if (!empty($res['order']['status']['code']) && (int)$res['order']['status']['code'] === 3) {
        $payment = $this->payment::whereRaw("REPLACE(id, '-', '') = ?", [$cartId])->first();
        if ($payment) {
            $payment->update([
                'payment_method' => 'telr',
                'is_paid'        => 1,
                'transaction_id' => $ref,
            ]);
            if (function_exists($payment->success_hook)) {
                call_user_func($payment->success_hook, $payment);
            }
        }
        return $this->payment_response($payment, 'success');
    }

    $payment = $this->payment::whereRaw("REPLACE(id, '-', '') = ?", [$cartId])->first();
    if ($payment && function_exists($payment->failure_hook)) {
        call_user_func($payment->failure_hook, $payment);
    }
    return $this->payment_response($payment, 'fail');
}
////////ssdff/////
}
