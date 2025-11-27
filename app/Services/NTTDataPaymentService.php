<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NTTDataPaymentService
{
    protected $config;

    public function __construct()
    {
        $env = config('nttdata.environment', 'demo');
        $this->config = config("nttdata.{$env}");
    }

    /**
     * Generate merchant transaction ID
     */
    public function generateMerchantTxnId(): string
    {
        return 'ORD-' . uniqid() . '-' . time();
    }

    /**
     * Encrypt payment data (Atom method)
     */
    public function encrypt($data, $salt = null, $key = null)
    {
        $salt = $salt ?? $this->config['enc_request_key'];
        $key = $key ?? $this->config['enc_request_key'];
        $method = 'AES-256-CBC';
        $ivBytes = pack('C*', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
        $hash = openssl_pbkdf2($key, $salt, 256 / 8, 65536, 'sha512');
        $enc = openssl_encrypt($data, $method, $hash, OPENSSL_RAW_DATA, $ivBytes);
        return strtoupper(bin2hex($enc));
    }

    /**
     * Decrypt payment data (Atom method)
     */
    public function decrypt($data, $salt = null, $key = null)
    {
        $salt = $salt ?? $this->config['dec_response_key'];
        $key = $key ?? $this->config['dec_response_key'];
        $encrypted = hex2bin($data);
        $method = 'AES-256-CBC';
        $ivBytes = pack('C*', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15);
        $hash = openssl_pbkdf2($key, $salt, 256 / 8, 65536, 'sha512');
        return openssl_decrypt($encrypted, $method, $hash, OPENSSL_RAW_DATA, $ivBytes);
    }

    /**
     * Create Atom Token ID (similar to FeesPaymentController)
     */
    public function createTokenId(array $paymentData)
    {
        // Build JSON payload for Atom
        $jsondata = json_encode(
            [
                'payInstrument' => [
                    'headDetails' => [
                        'version' => 'OTSv1.1',
                        'api' => 'AUTH',
                        'platform' => 'FLASH',
                    ],
                    'merchDetails' => [
                        'merchId' => $this->config['login'],
                        'userId' => '',
                        'password' => $this->config['password'],
                        'merchTxnId' => $paymentData['merchant_transaction_id'],
                        'merchTxnDate' => $paymentData['date'],
                    ],
                    'payDetails' => [
                        'amount' => (string) $paymentData['amount'],
                        'product' => $this->config['prod_id'],
                        'custAccNo' => '213232323',
                        'txnCurrency' => $paymentData['txnCurrency'] ?? 'INR',
                    ],
                    'custDetails' => [
                        'custEmail' => $paymentData['email'] ?? 'dummy@email.com',
                        'custMobile' => $paymentData['mobile'] ?? '9999999999',
                    ],
                    'extras' => [
                        'udf1' => $paymentData['udf1'] ?? '',
                        'udf2' => $paymentData['udf2'] ?? '',
                        'udf3' => $paymentData['udf3'] ?? '',
                        'udf4' => $paymentData['udf4'] ?? '',
                        'udf5' => $paymentData['udf5'] ?? '',
                    ],
                    'returnUrl' => $paymentData['returnUrl'],
                ],
            ],
            JSON_UNESCAPED_SLASHES,
        );

        // Encrypt request
        $encData = $this->encrypt($jsondata, $this->config['enc_request_key'], $this->config['enc_request_key']);

        // Call Atom API (SSL handling - disable for local, enable for production)
        $curl = curl_init();
        $isLocal = config('app.env') === 'local';

        $curlOptions = [
            CURLOPT_URL => $this->config['api_url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => $isLocal ? 0 : 2,
            CURLOPT_SSL_VERIFYPEER => $isLocal ? 0 : 1,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'encData' => $encData,
                'merchId' => $this->config['login'],
            ]),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        ];

        // Add CA certificate if file exists and not local (ssl)
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

        if (!$encresp) {
            Log::error('No encrypted response from Atom API. Response: ' . $response);
            return null;
        }

        $decData = $this->decrypt($encresp, $this->config['dec_response_key'], $this->config['dec_response_key']);

        if (!$decData) {
            Log::error('Decryption failed. Encrypted Response: ' . $encresp);
            return null;
        }

        Log::info('Decrypted Atom Response: ' . $decData);

        $res = json_decode($decData, true);

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

    /**
     * Initiate payment with Atom
     */
    public function initiatePayment(array $paymentData)
    {
        try {
            // Prepare payment data for Atom
            $atomPaymentData = [
                'merchant_transaction_id' => $paymentData['merchant_transaction_id'],
                'amount' => $paymentData['amount'],
                'date' => Carbon::now('Asia/Kolkata')->format('Y-m-d H:i:s'),
                'txnCurrency' => $paymentData['currency'] ?? 'INR',
                'email' => $paymentData['customer_email'] ?? 'dummy@email.com',
                'mobile' => $paymentData['customer_phone'] ?? '9999999999',
                'returnUrl' => $paymentData['return_url'],
                'udf1' => $paymentData['order_id'] ?? '',
                'udf2' => $paymentData['order_number'] ?? '',
                'udf3' => '',
                'udf4' => '',
                'udf5' => '',
            ];

            // Generate Atom token
            $atomTokenId = $this->createTokenId($atomPaymentData);

            if (!$atomTokenId) {
                return [
                    'success' => false,
                    'message' => 'Failed to generate payment token.',
                ];
            }

            // Return success with token for redirect
            return [
                'success' => true,
                'atomTokenId' => $atomTokenId,
                'merchant_transaction_id' => $paymentData['merchant_transaction_id'],
                'payment_url' => $this->config['api_url'], // Atom uses the same URL for payment page
            ];
        } catch (\Exception $e) {
            Log::error('Atom Payment Initiation Exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get current configuration
     */
    public function getConfig()
    {
        return $this->config;
    }
}
