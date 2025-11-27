<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\User;
use App\Services\NTTDataPaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NTTDataPaymentController extends Controller
{
    protected $paymentService;

    public function __construct(NTTDataPaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Initiate payment for an order
     */
    public function initiatePayment(Request $request, $orderId)
    {
        try {
            \Log::info('Payment initiation started', [
                'order_id' => $orderId,
                'user_id' => Auth::guard('customer')->id(),
            ]);

            $order = Order::where('id', $orderId)
                ->where('user_id', Auth::guard('customer')->id())
                ->firstOrFail();

            \Log::info('Order found for payment', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'amount' => $order->grand_total,
                'payment_status' => $order->payment_status,
            ]);

            if ($order->payment_status === 'paid') {
                return redirect()->route('customer.orderPageCod', $order->id)
                    ->with('error', 'Order is already paid.');
            }

            // Generate merchant transaction ID
            $merchantTxnId = $this->paymentService->generateMerchantTxnId();

            // Create payment record
            $payment = OrderPayment::create([
                'order_id' => $order->id,
                'merchant_transaction_id' => $merchantTxnId,
                'amount' => $order->grand_total,
                'currency' => 'INR',
                'status' => 'initiated',
            ]);

            $user = User::find($order->user_id);

            // Atom sandbox credentials (matching FeesPaymentController)
            $login = $this->paymentService->getConfig()['login'];
            $password = $this->paymentService->getConfig()['password'];
            $prod_id = $this->paymentService->getConfig()['prod_id'];
            $encRequestKey = $this->paymentService->getConfig()['enc_request_key'];
            $decResponseKey = $this->paymentService->getConfig()['dec_response_key'];
            $api_url = $this->paymentService->getConfig()['api_url'];

            // Prepare data for Atom payment form (exact match with FeesPaymentController)
            $payData = [
                'login' => $login,
                'password' => $password,
                'amount' => $order->grand_total,
                'prod_id' => $prod_id,
                'txnId' => $merchantTxnId,
                'date' => Carbon::now('Asia/Kolkata')->format('Y-m-d H:i:s'),
                'encKey' => $encRequestKey,
                'decKey' => $decResponseKey,
                'payUrl' => $api_url,
                'email' => $user->email ?? 'dummy@email.com',
                'mobile' => $user->phone ?? '9999999999',
                'txnCurrency' => 'INR',
                'returnUrl' => route('nttdata.payment.return', ['orderId' => $order->id]),
                'udf1' => (string) $order->id,
                'udf2' => $order->order_number ?? '',
                'udf3' => '',
                'udf4' => '',
                'udf5' => '',
            ];

            // Generate Atom token (exact same method as FeesPaymentController)
            $atomTokenId = $this->createTokenId($payData);

            if (!$atomTokenId) {
                $payment->update([
                    'status' => 'failed',
                    'remark' => 'Failed to generate payment token.',
                ]);
                return redirect()->route('customer.orderPageCod', $order->id)
                    ->with('error', 'Failed to generate payment token.');
            }

            // Update payment record
            $payment->update([
                'response_data' => json_encode(['atomTokenId' => $atomTokenId]),
            ]);

            // Log token for debugging
            \Log::info('Preparing Atom payment form', [
                'atomTokenId' => $atomTokenId,
                'merchantTxnId' => $merchantTxnId,
                'order_id' => $order->id,
            ]);

            // Return view with Atom payment form (exact match with FeesPaymentController)
            return view('frontend.order.payment_initiated', [
                'data' => $payData,
                'atomTokenId' => $atomTokenId,
            ]);
        } catch (\Exception $e) {
            Log::error('Atom Payment Initiation Error: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->route('customer.orderPageCod', $orderId ?? 0)
                ->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Create Atom Token ID (exact same as FeesPaymentController)
     */
    public function createTokenId(array $data)
    {
        // Build JSON payload
        $jsondata = json_encode(
            [
                'payInstrument' => [
                    'headDetails' => [
                        'version' => 'OTSv1.1',
                        'api' => 'AUTH',
                        'platform' => 'FLASH',
                    ],
                    'merchDetails' => [
                        'merchId' => $data['login'],
                        'userId' => '',
                        'password' => $data['password'],
                        'merchTxnId' => $data['txnId'],
                        'merchTxnDate' => $data['date'],
                    ],
                    'payDetails' => [
                        'amount' => (string) $data['amount'],
                        'product' => $data['prod_id'],
                        'custAccNo' => '213232323',
                        'txnCurrency' => $data['txnCurrency'],
                    ],
                    'custDetails' => [
                        'custEmail' => $data['email'],
                        'custMobile' => $data['mobile'],
                    ],
                    'extras' => [
                        'udf1' => $data['udf1'],
                        'udf2' => $data['udf2'],
                        'udf3' => $data['udf3'],
                        'udf4' => $data['udf4'],
                        'udf5' => $data['udf5'],
                    ],
                    'returnUrl' => $data['returnUrl'],
                ],
            ],
            JSON_UNESCAPED_SLASHES,
        );

        // Encrypt request
        $encData = $this->encrypt($jsondata, $data['encKey'], $data['encKey']);

        // Call Atom API (SSL handling - disable for local, enable for production)
        $curl = curl_init();
        $isLocal = config('app.env') === 'local';

        $curlOptions = [
            CURLOPT_URL => $data['payUrl'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => $isLocal ? 0 : 2,
            CURLOPT_SSL_VERIFYPEER => $isLocal ? 0 : 1,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'encData' => $encData,
                'merchId' => $data['login'],
            ]),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        ];

        // Add CA certificate if file exists and not local
        if (!$isLocal) {
            $caCertPath = __DIR__ . '/cacert.pem';
            if (file_exists($caCertPath)) {
                $curlOptions[CURLOPT_CAINFO] = $caCertPath;
            }
        }

        curl_setopt_array($curl, $curlOptions);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            Log::error('Atom Curl Error: ' . curl_error($curl));
            curl_close($curl);
            return null;
        }
        curl_close($curl);

        // Decrypt response
        parse_str($response, $respArr);
        $encresp = $respArr['encResp'] ?? ($respArr['encData'] ?? null);

        $decData = $encresp ? $this->decrypt($encresp, $data['decKey'], $data['decKey']) : null;
        if (!$decData) {
            Log::error('Decryption failed. Encrypted Response: ' . $encresp);
            return null;
        }

        Log::info('Decrypted Response: ' . $decData);

        $res = $decData ? json_decode($decData, true) : null;
        if ($res) {
            Log::info('Decrypted Atom Response:', $res);

            $statusCode = data_get($res, 'responseDetails.txnStatusCode');
            $errorMsg = data_get($res, 'responseDetails.errorMessage');

            if ($statusCode === 'OTS0000') {
                return data_get($res, 'atomTokenId');
            } else {
                Log::error("Atom API Error: [$statusCode] $errorMsg");
            }
        } else {
            Log::error('Atom API Error: Failed to decode response.');
        }

        return null;
    }

    protected function encrypt($data, $salt, $key)
    {
        $method = 'AES-256-CBC';
        $ivBytes = pack('C*', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
        $hash = openssl_pbkdf2($key, $salt, 256 / 8, 65536, 'sha512');
        $enc = openssl_encrypt($data, $method, $hash, OPENSSL_RAW_DATA, $ivBytes);
        return strtoupper(bin2hex($enc));
    }

    protected function decrypt($data, $salt, $key)
    {
        $encrypted = hex2bin($data);
        $method = 'AES-256-CBC';
        $ivBytes = pack('C*', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
        $hash = openssl_pbkdf2($key, $salt, 256 / 8, 65536, 'sha512');
        return openssl_decrypt($encrypted, $method, $hash, OPENSSL_RAW_DATA, $ivBytes);
    }

    /**
     * Handle payment callback (server-to-server)
     */
    public function paymentCallback(Request $request)
    {
        Log::info('Atom Payment Callback Received', $request->all());

        try {
            $merchantTxnId = $request->input('merchant_transaction_id')
                ?? $request->input('transaction_id')
                ?? $request->input('merchantTxnId');

            if (!$merchantTxnId) {
                Log::error('Missing merchant_transaction_id in callback');
                return response()->json(['error' => 'Invalid callback payload'], 400);
            }

            // Find payment record
            $payment = OrderPayment::where('merchant_transaction_id', $merchantTxnId)->first();

            if (!$payment) {
                Log::error('Payment record not found for MerchantTxnId: ' . $merchantTxnId);
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Atom callback typically comes as POST with encData
            $encData = $request->input('encData');
            if ($encData) {
                $decKey = $this->paymentService->getConfig()['dec_response_key'];
                $decData = $this->decrypt($encData, $decKey, $decKey);
                $jsonData = json_decode($decData, true);

                if ($jsonData && isset($jsonData['payInstrument']['responseDetails']['statusCode'])) {
                    $statusCode = $jsonData['payInstrument']['responseDetails']['statusCode'];
                    $paymentStatus = ($statusCode === 'OTS0000') ? 'success' : 'failed';

                    $payment->update([
                        'status' => $paymentStatus,
                        'ntt_data_transaction_id' => $jsonData['payInstrument']['payDetails']['atomTxnId'] ?? null,
                        'bank_transaction_id' => $jsonData['payInstrument']['payModeSpecificData']['bankDetails']['bankTxnId'] ?? null,
                        'payment_mode' => $jsonData['payInstrument']['payModeSpecificData']['bankDetails']['cardType'] ?? null,
                        'response_data' => json_encode($jsonData),
                        'transaction_date' => $jsonData['payInstrument']['merchDetails']['merchTxnDate'] ?? now(),
                        'transaction_completed_at' => $jsonData['payInstrument']['payDetails']['txnCompleteDate'] ?? now(),
                        'remark' => $jsonData['payInstrument']['responseDetails']['message'] ?? 'Payment processed',
                    ]);

                    // Update order payment status
                    if ($paymentStatus === 'success') {
                        $order = Order::find($payment->order_id);
                        if ($order) {
                            $order->update([
                                'payment_status' => 'paid',
                                'payment_method' => 'online',
                                'status' => 'confirmed',
                            ]);
                        }
                    }

                    return response()->json(['message' => 'Callback processed successfully']);
                }
            }

            return response()->json(['error' => 'Invalid callback data'], 400);
        } catch (\Exception $e) {
            Log::error('Atom Payment Callback Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Callback processing failed'], 500);
        }
    }

    /**
     * Handle payment return (browser redirect) - Atom format
     * Matches working project pattern with better error handling
     */
    public function paymentReturn(Request $request, $orderId)
    {
        // Wrap everything to prevent exceptions from reaching Laravel's exception handler
        try {
            \Log::info('Payment return called', [
                'order_id' => $orderId,
                'has_post' => isset($_POST['encData']),
                'request_method' => $request->method(),
                'all_input' => $request->all(),
                'post_data' => $_POST ?? [],
                'raw_input' => file_get_contents('php://input'),
                'headers' => $request->headers->all(),
            ]);

            // Check for encData (Atom sends it via POST)
            // If missing, user might have cancelled payment or closed the gateway
            if (!isset($_POST['encData'])) {
                \Log::warning('Atom response missing encData - possible payment cancellation', [
                    'order_id' => $orderId,
                    'request_all' => $request->all(),
                    'request_method' => $request->method(),
                ]);

                // Try to find order and payment record
                $order = Order::where('id', $orderId)->first();
                if ($order) {
                    $payment = OrderPayment::where('order_id', $order->id)->latest()->first();
                    if ($payment && $payment->status === 'initiated') {
                        // Update payment as cancelled
                        try {
                            $payment->update([
                                'status' => 'failed',
                                'remark' => 'Payment cancelled by user or gateway timeout',
                            ]);
                            \Log::info('Payment marked as cancelled', [
                                'order_id' => $order->id,
                                'payment_id' => $payment->id,
                            ]);
                        } catch (\Exception $e) {
                            \Log::error('Error updating cancelled payment', ['error' => $e->getMessage()]);
                        }
                    }

                    // Show cancellation message
                    return $this->simpleResponse('Payment was cancelled. Your order #' . $order->order_number . ' is still pending. You can try payment again from your orders page.');
                }

                return $this->simpleResponse('Invalid payment response. Please contact support with Order ID: ' . $orderId);
            }

            $encResp = $_POST['encData'];
            $decKey = $this->paymentService->getConfig()['dec_response_key'] ?? '75AEF0FA1B94B3C10D4F5B268F757F11';

            $decData = $this->decrypt($encResp, $decKey, $decKey);

            if (!$decData) {
                \Log::error('Failed to decrypt Atom response', [
                    'order_id' => $orderId,
                    'encResp_length' => strlen($encResp ?? ''),
                ]);
                return $this->simpleRedirect('customer.orderPageCod', $orderId, 'Failed to decrypt payment response.');
            }

            $jsonData = json_decode($decData, true);

            if (!$jsonData || !isset($jsonData['payInstrument']['responseDetails']['statusCode'])) {
                \Log::error('Invalid or corrupted Atom response', [
                    'order_id' => $orderId,
                    'decrypted_data' => substr($decData, 0, 200), // Log first 200 chars only
                ]);
                return $this->simpleRedirect('customer.orderPageCod', $orderId, 'Failed to process payment response.');
            }

            // Find order safely - don't require auth since Atom is calling this
            $order = null;
            try {
                // First try to find by order_id (no auth check since Atom redirects)
                $order = Order::where('id', $orderId)->first();

                // If not found, try to find by merchant_transaction_id from response
                if (!$order && isset($jsonData['payInstrument']['merchDetails']['merchTxnId'])) {
                    $merchantTxnId = $jsonData['payInstrument']['merchDetails']['merchTxnId'];
                    \Log::info('Order not found by order_id, trying merchant_transaction_id', [
                        'order_id' => $orderId,
                        'merchant_txn_id' => $merchantTxnId,
                    ]);
                    $payment = OrderPayment::where('merchant_transaction_id', $merchantTxnId)->first();
                    if ($payment) {
                        $order = Order::where('id', $payment->order_id)->first();
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Error finding order', [
                    'order_id' => $orderId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return $this->simpleResponse('Order not found. Please contact support with Order ID: ' . $orderId);
            }

            if (!$order) {
                \Log::error('Order not found for payment return', [
                    'order_id' => $orderId,
                    'merchant_txn_id' => $jsonData['payInstrument']['merchDetails']['merchTxnId'] ?? 'not provided',
                ]);
                return $this->simpleResponse('Order not found. Please contact support with Order ID: ' . $orderId);
            }

            // Find payment record safely - try by order_id first, then by merchant_transaction_id
            $payment = null;
            try {
                $payment = OrderPayment::where('order_id', $order->id)->latest()->first();

                // If not found, try to find by merchant_transaction_id from response
                if (!$payment && isset($jsonData['payInstrument']['merchDetails']['merchTxnId'])) {
                    $merchantTxnId = $jsonData['payInstrument']['merchDetails']['merchTxnId'];
                    \Log::info('Payment not found by order_id, trying merchant_transaction_id', [
                        'order_id' => $order->id,
                        'merchant_txn_id' => $merchantTxnId,
                    ]);
                    $payment = OrderPayment::where('merchant_transaction_id', $merchantTxnId)->first();
                }
            } catch (\Exception $e) {
                \Log::error('Error finding payment', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }

            if (!$payment) {
                \Log::error('Payment record not found', [
                    'order_id' => $order->id,
                    'merchant_txn_id' => $jsonData['payInstrument']['merchDetails']['merchTxnId'] ?? 'not provided',
                    'available_payments' => OrderPayment::where('order_id', $order->id)->get(['id', 'merchant_transaction_id', 'status'])->toArray(),
                ]);
                return $this->simpleRedirect('customer.orderPageCod', $order->id, 'Payment record not found.');
            }

            \Log::info('Payment record found', [
                'payment_id' => $payment->id,
                'current_status' => $payment->status,
                'merchant_txn_id' => $payment->merchant_transaction_id,
            ]);

            // Check if payment is successful
            $statusCode = $jsonData['payInstrument']['responseDetails']['statusCode'] ?? '';

            \Log::info('Processing payment response', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'status_code' => $statusCode,
                'status_message' => $jsonData['payInstrument']['responseDetails']['message'] ?? 'N/A',
                'merchant_txn_id_from_response' => $jsonData['payInstrument']['merchDetails']['merchTxnId'] ?? 'N/A',
            ]);

            if ($statusCode === 'OTS0000') {
                // Payment successful - update records safely
                try {
                    DB::beginTransaction();

                    $payment->update([
                        'bank_transaction_id' => $jsonData['payInstrument']['payModeSpecificData']['bankDetails']['bankTxnId'] ?? null,
                        'ntt_data_transaction_id' => $jsonData['payInstrument']['payDetails']['atomTxnId'] ?? null,
                        'amount' => $jsonData['payInstrument']['payDetails']['amount'] ?? $order->grand_total,
                        'transaction_date' => $jsonData['payInstrument']['merchDetails']['merchTxnDate'] ?? now(),
                        'transaction_completed_at' => $jsonData['payInstrument']['payDetails']['txnCompleteDate'] ?? now(),
                        'status' => 'success',
                        'payment_mode' => $jsonData['payInstrument']['payModeSpecificData']['bankDetails']['cardType'] ?? null,
                        'response_data' => json_encode($jsonData),
                        'remark' => $jsonData['payInstrument']['responseDetails']['message'] ?? 'Success',
                    ]);

                    $order->update([
                        'payment_status' => 'paid',
                        'payment_method' => 'online',
                        'status' => 'confirmed',
                    ]);

                    // Clear cart after successful payment (same as COD flow)
                    try {
                        $cart = Cart::where('user_id', $order->user_id)
                            ->where('status', 'active')
                            ->first();

                        if ($cart) {
                            $cart->delete();
                            \Log::info('Cart cleared after successful payment', [
                                'order_id' => $order->id,
                                'user_id' => $order->user_id,
                                'cart_id' => $cart->id,
                            ]);
                        }
                    } catch (\Exception $cartException) {
                        \Log::warning('Failed to clear cart after payment', [
                            'order_id' => $order->id,
                            'error' => $cartException->getMessage(),
                        ]);
                        // Don't fail the payment if cart clearing fails
                    }

                    DB::commit();

                    \Log::info('Payment and order updated successfully', [
                        'order_id' => $order->id,
                        'payment_id' => $payment->id,
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error('Error updating payment/order records', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                // Try to get user and render view
                try {
                    $user = User::find($order->user_id);
                    if (!$user) {
                        return $this->simpleRedirect('customer.orderPageCod', $order->id, 'Payment successful! Order #' . $order->order_number);
                    }

                    // Load relationships safely
                    try {
                        $order->load(['orderItems.product.productSkus', 'orderItems.sku', 'user.userAddress']);
                    } catch (\Exception $e) {
                        \Log::warning('Failed to load relationships', ['error' => $e->getMessage()]);
                    }

                    \Log::info('Rendering payment success page', [
                        'order_id' => $order->id,
                        'payment_id' => $payment->id,
                    ]);

                    return view('frontend.order.payment_success', compact('order', 'payment', 'user'));
                } catch (\Throwable $viewException) {
                    \Log::error('Failed to render payment success view', [
                        'order_id' => $order->id,
                        'error' => $viewException->getMessage(),
                        'file' => $viewException->getFile(),
                        'line' => $viewException->getLine(),
                    ]);
                    return $this->simpleRedirect('customer.orderPageCod', $order->id, 'Payment successful! Order #' . $order->order_number);
                }
            } else {
                // Payment failed or cancelled
                $statusMessage = $jsonData['payInstrument']['responseDetails']['message'] ?? 'Payment Failed';

                \Log::warning('Atom Payment Failed or Cancelled', [
                    'order_id' => $order->id,
                    'payment_id' => $payment->id,
                    'status_code' => $statusCode,
                    'status_message' => $statusMessage,
                    'full_response' => $jsonData,
                ]);

                try {
                    DB::beginTransaction();

                    $payment->update([
                        'status' => 'failed',
                        'response_data' => json_encode($jsonData),
                        'remark' => $statusMessage,
                        'transaction_date' => $jsonData['payInstrument']['merchDetails']['merchTxnDate'] ?? now(),
                    ]);

                    // Don't update order status to failed - keep it as pending so user can retry
                    // Only update payment_status if needed
                    if ($order->payment_status === 'paid') {
                        // If somehow already paid, don't change it
                    } else {
                        $order->update([
                            'payment_status' => 'failed',
                            // Keep status as 'pending' so user can retry payment
                        ]);
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error('Error updating failed payment', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                // Try to render failed payment view
                try {
                    $user = User::find($order->user_id);
                    if (!$user) {
                        return $this->simpleResponse('Payment failed for Order #' . $order->order_number . '. Please contact support.');
                    }

                    // Load relationships safely
                    try {
                        $order->load(['orderItems.product.productSkus', 'orderItems.sku', 'user.userAddress']);
                    } catch (\Exception $e) {
                        \Log::warning('Failed to load relationships for failed payment', [
                            'order_id' => $order->id,
                            'error' => $e->getMessage(),
                        ]);
                    }

                    \Log::info('Rendering payment failed page', [
                        'order_id' => $order->id,
                        'payment_id' => $payment->id,
                    ]);

                    return view('frontend.order.payment_failed', compact('order', 'payment', 'user'));
                } catch (\Throwable $viewException) {
                    \Log::error('Failed to render payment failed view', [
                        'order_id' => $order->id,
                        'error' => $viewException->getMessage(),
                        'file' => $viewException->getFile(),
                        'line' => $viewException->getLine(),
                    ]);
                    return $this->simpleResponse('Payment failed for Order #' . $order->order_number . '. Please try again or contact support.');
                }
            }
        } catch (\Throwable $e) {
            // Log the actual exception
            \Log::error('Atom Payment Return Exception', [
                'order_id' => $orderId ?? 'unknown',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return simple response without triggering exception handler
            return $this->simpleResponse('Payment processing error. Please contact support with Order ID: ' . ($orderId ?? 'N/A'));
        }
    }

    /**
     * Simple redirect helper that won't trigger exception handler
     */
    private function simpleRedirect($route, $param = null, $message = null)
    {
        try {
            if ($param) {
                $redirect = redirect()->route($route, $param);
            } else {
                $redirect = redirect()->route($route);
            }
            if ($message) {
                $redirect = $redirect->with(is_numeric(strpos($message, 'success')) ? 'success' : 'error', $message);
            }
            return $redirect;
        } catch (\Exception $e) {
            return $this->simpleResponse($message ?? 'An error occurred. Please contact support.');
        }
    }

    /**
     * Simple HTML response that won't trigger exception handler
     */
    private function simpleResponse($message)
    {
        $html = '<!DOCTYPE html><html><head><title>Payment Status</title><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><style>body{font-family:Arial,sans-serif;max-width:600px;margin:50px auto;padding:20px;text-align:center;}h1{color:#333;}.success{color:#28a745;}.error{color:#dc3545;}</style></head><body><h1 class="' . (strpos($message, 'success') !== false ? 'success' : 'error') . '">' . htmlspecialchars($message) . '</h1><p><a href="' . route('customer.orders') . '">View Orders</a></p></body></html>';
        return response($html, 200)->header('Content-Type', 'text/html');
    }
}

