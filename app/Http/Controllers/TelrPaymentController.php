<?php

namespace App\Http\Controllers;

use App\Models\PaymentRequest;
use App\Traits\Processor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;  

class TelrPaymentController extends Controller
{
    use Processor;
    private $config_values;
    private $base_url;
    private PaymentRequest $payment;

    public function __construct(PaymentRequest $payment)
    {
        $config = $this->payment_config('telr', 'payment_config');
        if (!is_null($config) && $config->mode == 'live') {
            $this->config_values = json_decode($config->live_values);
        } elseif (!is_null($config) && $config->mode == 'test') {
            $this->config_values = json_decode($config->test_values);
        }

        if($config){
            $this->base_url = ($config->mode == 'test') 
                ? 'https://secure.telr.com/gateway/order.json' 
                : 'https://secure.telr.com/gateway/order.json';
        }
        $this->payment = $payment;
    }

    /**
     * Generate authentication key for Telr (different from PayPal's OAuth)
     */
    public function generateAuthKey()
    {
        // Telr uses a different authentication approach
        // It typically requires store ID and authentication key
        return base64_encode($this->config_values->store_id . ':' . $this->config_values->auth_key);
    }

    /**
     * Process payment request to Telr
     */
    public function payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $this->error_processor($validator)), 400);
        }

        $data = $this->payment::where(['id' => $request['payment_id']])->where(['is_paid' => 0])->first();
        if (!isset($data)) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
        }

        // Prepare Telr-specific request parameters
        $requestParams = [
            'ivp_method' => 'create',
            'ivp_store' => $this->config_values->store_id,
            'ivp_authkey' => $this->config_values->auth_key,
            'ivp_cart' => substr(preg_replace('/[^A-Za-z0-9]/', '', $data->id), 0, 12),
            'ivp_test' => ($this->config_values->mode == 'test') ? 1 : 0,
            'ivp_amount' => round($data->payment_amount, 2),
            'ivp_currency' => $data->currency_code ?? 'USD',
            'ivp_desc' => 'Payment ID: ' . $data->id,
            'return_auth' => route('telr.success', ['payment_id' => $data->id]),
            'return_decl' => route('telr.cancel', ['payment_id' => $data->id]),
            'return_can' => route('telr.cancel', ['payment_id' => $data->id]),
            'bill_city' => $data->customer_city ?? '',
            'bill_country' => $data->customer_country ?? '',
            'bill_email' => $data->customer_email ?? '',
            'bill_fname' => $data->customer_name ?? '',
            'bill_title' => 'Payment',
        ];
        \Log::info('Telr Params:', $requestParams);


        // Initialize cURL session for Telr
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->base_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($requestParams));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $headers = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_404, null, ['error' => curl_error($ch)]), 404);
        }
        curl_close($ch);

        $responseData = json_decode($response, true);

        if (isset($responseData['order']) && isset($responseData['order']['url'])) {
            // Redirect to Telr payment page
            return Redirect::away($responseData['order']['url']);
        } else {
            // Handle error response from Telr
            $error = $responseData['error'] ?? ['message' => 'Unknown error from Telr'];
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $error), 400);
        }
    }

    /**
     * Handle cancelled payment
     */
    public function cancel(Request $request)
    {
        $data = $this->payment::where(['id' => $request['payment_id']])->first();
        return $this->payment_response($data, 'cancel');
    }

    /**
     * Handle successful payment
     */
    public function success(Request $request)
    {
        // Telr sends payment reference in the request
        $ref = $request->input('order_ref');
        $cartId = $request->input('cart_id');
        
        if (!$ref || !$cartId) {
            $payment_data = $this->payment::where(['id' => $request['payment_id']])->first();
            if (isset($payment_data) && function_exists($payment_data->failure_hook)) {
                call_user_func($payment_data->failure_hook, $payment_data);
            }
            return $this->payment_response($payment_data, 'fail');
        }

        // Verify payment with Telr
        $verifyParams = [
            'ivp_method' => 'check',
            'ivp_store' => $this->config_values->store_id,
            'ivp_authkey' => $this->config_values->auth_key,
            'order_ref' => $ref,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->base_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($verifyParams));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $headers = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $payment_data = $this->payment::where(['id' => $request['payment_id']])->first();
            if (isset($payment_data) && function_exists($payment_data->failure_hook)) {
                call_user_func($payment_data->failure_hook, $payment_data);
            }
            return $this->payment_response($payment_data, 'fail');
        }
        curl_close($ch);

        $responseData = json_decode($response, true);

        // Check if payment was successful
        if (isset($responseData['order']) && 
            isset($responseData['order']['status']) && 
            $responseData['order']['status']['code'] == 3) { // 3 means completed in Telr
            
            $this->payment::where(['id' => $request['payment_id']])->update([
                'payment_method' => 'telr',
                'is_paid' => 1,
                'transaction_id' => $ref,
            ]);

            $data = $this->payment::where(['id' => $request['payment_id']])->first();

            if (isset($data) && function_exists($data->success_hook)) {
                call_user_func($data->success_hook, $data);
            }

            return $this->payment_response($data, 'success');
        }

        $payment_data = $this->payment::where(['id' => $request['payment_id']])->first();
        if (isset($payment_data) && function_exists($payment_data->failure_hook)) {
            call_user_func($payment_data->failure_hook, $payment_data);
        }
        return $this->payment_response($payment_data, 'fail');
    }
}