<?php

class PaymentService {
    private $config;
    private $repository;
    private $logFile;

    public function __construct($repository) {
        $this->config = require __DIR__ . '/../../config/paymob.php';
        $this->repository = $repository;
        $this->logFile = __DIR__ . '/../../storage/logs/paymob_debug.log';
        $this->ensureLogDirectory();
    }

    public function getConfiguredAmount() {
        return (float) ($this->config['payment']['amount'] ?? 0);
    }

    public function getSampleSizeAmount($sampleSize) {
        $rate = (float) ($this->config['payment']['sample_size_rate'] ?? 10);
        return (float) ($sampleSize * $rate);
    }

    public function getHmacSecret() {
        return $this->config['hmac_secret'] ?? '';
    }

    /**
     * Initiate payment - Card uses Intention API, Wallet uses Classic API
     */
    public function initiatePayment($paymentId, $studentProfile, $paymentMethod = 'card') {
        try {
            $paymentData = $this->repository->getPaymentById($paymentId);
            if (!$paymentData) {
                throw new Exception('تفاصيل الدفع غير موجودة');
            }
            $amountCents = (int) round($paymentData['amount'] * 100);

            $this->log('=== PAYMENT INITIATION START === Payment ID: ' . $paymentId . ' Method: ' . $paymentMethod . ' Type: ' . $paymentData['payment_type'] . ' Amount: ' . $paymentData['amount'] . ' (' . $amountCents . ' cents)');
            $this->validateConfiguration();

            if ($paymentMethod === 'wallet') {
                $redirectUrl = $this->initiateWalletPayment($paymentId, $studentProfile, $amountCents);
            } else {
                $redirectUrl = $this->initiateCardPayment($paymentId, $studentProfile, $amountCents);
            }

            $this->log('=== PAYMENT INITIATION COMPLETE ===');
            return $redirectUrl;

        } catch (Exception $e) {
            $this->log('=== PAYMENT INITIATION FAILED === Error: ' . $e->getMessage());
            $this->repository->markPaymentFailed($paymentId, $e->getMessage());
            throw $e;
        }
    }

    /**
     * Card payment via Intention API + Unified Checkout
     */
    private function initiateCardPayment($paymentId, $studentProfile, $amountCents) {
        $this->log('=== CARD PAYMENT (Intention API) ===');

        $intentionResponse = $this->createIntention($paymentId, $studentProfile, 'card', $amountCents);
        $clientSecret = $intentionResponse['client_secret'] ?? '';
        $paymobOrderId = (int) ($intentionResponse['intention_order_id'] ?? 0);

        if (empty($clientSecret)) {
            $this->log('FAILED: No client_secret in response');
            throw new Exception('تعذر إنشاء طلب الدفع مع Paymob (no client_secret)');
        }

        $this->log('SUCCESS: client_secret received, order_id=' . $paymobOrderId);
        $this->repository->updatePaymentWithPaymobData($paymentId, $paymobOrderId);

        $redirectUrl = $this->buildUnifiedCheckoutUrl($clientSecret);
        $this->log('Redirect URL = ' . $redirectUrl);
        return $redirectUrl;
    }

    /**
     * Wallet payment via Classic 4-step API
     * Step 1: Auth token
     * Step 2: Create order
     * Step 3: Get payment key
     * Step 4: Pay with wallet (returns redirect_url)
     */
    private function initiateWalletPayment($paymentId, $studentProfile, $amountCents) {
        $this->log('=== WALLET PAYMENT (Classic API) ===');

        $integrationId = (int) ($this->config['wallet_integration_id'] ?? 0);
        if ($integrationId <= 0) {
            throw new Exception('لم يتم تكوين معرف دمج المحفظة (PAYMOB_WALLET_INTEGRATION_ID)');
        }

        // Step 1: Auth token
        $this->log('Step 1: Requesting auth token...');
        $authToken = $this->getAuthToken();
        $this->log('Step 1 SUCCESS: auth_token received');

        // Step 2: Create order
        $this->log('Step 2: Creating order...');
        $orderResponse = $this->createClassicOrder($authToken, $amountCents, $paymentId);
        $paymobOrderId = (int) ($orderResponse['id'] ?? 0);
        if ($paymobOrderId <= 0) {
            $this->log('Step 2 FAILED: No order_id');
            throw new Exception('فشل إنشاء طلب الدفع مع Paymob');
        }
        $this->log('Step 2 SUCCESS: Paymob Order ID = ' . $paymobOrderId);
        $this->repository->updatePaymentWithPaymobData($paymentId, $paymobOrderId);

        // Step 3: Get payment key
        $this->log('Step 3: Generating payment key...');
        $billingData = $this->buildBillingData($studentProfile);
        $paymentKeyResponse = $this->getPaymentKey($authToken, $amountCents, $paymobOrderId, $integrationId, $billingData);
        $paymentToken = $paymentKeyResponse['token'] ?? '';
        if (empty($paymentToken)) {
            $this->log('Step 3 FAILED: No payment token');
            throw new Exception('فشل إنشاء مفتاح الدفع مع Paymob');
        }
        $this->log('Step 3 SUCCESS: payment_token received');

        // Step 4: Pay with wallet
        $this->log('Step 4: Initiating wallet payment...');
        $walletPhone = $studentProfile['wallet_phone'] ?? $studentProfile['phone_number'] ?? '';
        $walletResponse = $this->payWithWallet($paymentToken, $walletPhone);
        $redirectUrl = $walletResponse['redirect_url'] ?? '';
        if (empty($redirectUrl)) {
            $this->log('Step 4 FAILED: No redirect_url');
            throw new Exception('فشل بدء دفع المحفظة مع Paymob');
        }
        $this->log('Step 4 SUCCESS: redirect_url = ' . $redirectUrl);
        return $redirectUrl;
    }

    /**
     * Create intention via Paymob Intention API
     */
    private function createIntention($paymentId, $studentProfile, $paymentMethod = 'card', $amountCents = 0) {
        $fullName = trim((string) ($studentProfile['full_name'] ?? 'Student IRB'));
        $nameParts = preg_split('/\s+/', $fullName);
        $firstName = $nameParts[0] ?? 'Student';
        $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : 'IRB';

        $baseUrl = rtrim($this->config['callbacks']['base_url'], '/');

        $webhookUrl = $baseUrl . '/payment/webhook';
        // IMPORTANT: redirection_url must point to /receipt (paymobReturn handler).
        // Paymob appends ?success=true&order=X&id=Y&txn_response_code=... to this URL.
        // The paymobReturn() method reads those params and updates the DB.
        // DO NOT use /payment/receipt here — that route only displays data without updating the DB.
        $receiptUrl = $baseUrl . '/receipt';

        // Card integration ID only (Intention API)
        $integrationId = (int) ($this->config['card_integration_id'] ?? $this->config['integration_id'] ?? 0);
        $this->log('Using card integration ID: ' . $integrationId);
        if ($integrationId <= 0) {
            throw new Exception('لم يتم تكوين معرف دمج البطاقة (PAYMOB_CARD_INTEGRATION_ID)');
        }

        $dynamicAmountCents = $amountCents > 0 ? $amountCents : (int) round($this->getConfiguredAmount() * 100);

        $payload = [
            'amount'          => $dynamicAmountCents,
            'currency'        => $this->config['payment']['currency'],
            'payment_methods' => [$integrationId],
            'billing_data'    => [
                'first_name'  => $firstName,
                'last_name'   => $lastName,
                'email'       => ($studentProfile['email'] ?? '') ?: 'student@irb.edu',
                'phone_number' => $this->normalizePhoneNumber(
                    $studentProfile['wallet_phone'] ?? $studentProfile['phone_number'] ?? ''
                ),
                'apartment'   => 'NA',
                'floor'       => 'NA',
                'street'      => 'NA',
                'building'    => 'NA',
                'city'        => 'Cairo',
                'country'     => 'EG',
                'state'       => 'Cairo',
                'postal_code' => 'NA',
            ],
            // items total MUST equal top-level amount (Paymob 406 validation)
            'items' => [
                [
                    'name'        => 'IRB Research Fee',
                    'amount'      => $dynamicAmountCents,
                    'quantity'    => 1,
                    'description' => 'IRB Payment',
                ],
            ],
            'notification_url' => $webhookUrl,
            'redirection_url'  => $receiptUrl,
        ];

        $url = $this->config['api']['intention_url'];
        $secretKey = $this->config['secret_key'] ?? '';

        $this->log('Intention Request: URL=' . $url);
        $this->log('Intention Payload: ' . json_encode($payload, JSON_UNESCAPED_UNICODE));

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Token ' . $secretKey,
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false || $curlError !== '') {
            $this->log('CURL ERROR: ' . $curlError);
            throw new Exception('فشل الاتصال بخدمة Paymob: ' . $curlError);
        }

        $this->log('Intention Response HTTP=' . $httpCode . ' BODY=' . $response);

        $decoded = json_decode($response, true);

        if ($httpCode < 200 || $httpCode >= 300) {
            $errorMsg = 'Paymob HTTP ' . $httpCode;
            if (is_array($decoded) && !empty($decoded['detail'])) {
                $errorMsg .= ': ' . json_encode($decoded['detail']);
            } elseif (is_array($decoded) && !empty($decoded['message'])) {
                $errorMsg .= ': ' . $decoded['message'];
            } else {
                $errorMsg .= ': ' . $response;
            }
            throw new Exception($errorMsg);
        }

        if (!is_array($decoded)) {
            throw new Exception('رد Paymob غير مفهوم: ' . substr($response, 0, 500));
        }

        return $decoded;
    }

    // ==================== CLASSIC API HELPERS (Wallet) ====================

    /**
     * Step 1: Get auth token from Paymob Classic API
     */
    private function getAuthToken() {
        $apiKey = $this->config['api_key'] ?? '';
        if (empty($apiKey)) {
            throw new Exception('PAYMOB_API_KEY not configured');
        }
        $url = 'https://accept.paymobsolutions.com/api/auth/tokens';
        $response = $this->postJson($url, ['api_key' => $apiKey]);
        $token = $response['token'] ?? '';
        if (empty($token)) {
            $this->log('Auth FAILED: ' . json_encode($response));
            throw new Exception('فشل المصادقة مع Paymob: ' . json_encode($response));
        }
        return $token;
    }

    /**
     * Step 2: Create order on Paymob Classic API
     */
    private function createClassicOrder($authToken, $amountCents, $merchantOrderId) {
        $url = 'https://accept.paymobsolutions.com/api/ecommerce/orders';
        $payload = [
            'auth_token' => $authToken,
            'delivery_needed' => 'false',
            'amount_cents' => (string) $amountCents,
            'currency' => $this->config['payment']['currency'],
            'merchant_order_id' => 'irb_payment_' . $merchantOrderId,
            'items' => [[
                'name' => 'Initial Research Review Fee',
                'amount_cents' => (string) $amountCents,
                'description' => 'IRB Initial Payment',
                'quantity' => '1',
            ]],
        ];
        $this->log('Classic Order Request: ' . json_encode($payload));
        $response = $this->postJson($url, $payload);
        $this->log('Classic Order Response: ' . json_encode($response));
        return $response;
    }

    /**
     * Step 3: Get payment key from Paymob Classic API
     */
    private function getPaymentKey($authToken, $amountCents, $orderId, $integrationId, $billingData) {
        $url = 'https://accept.paymobsolutions.com/api/acceptance/payment_keys';
        $payload = [
            'auth_token' => $authToken,
            'amount_cents' => (string) $amountCents,
            'expiration' => 3600,
            'order_id' => (string) $orderId,
            'currency' => $this->config['payment']['currency'],
            'integration_id' => $integrationId,
            'lock_order_when_paid' => 'false',
            'billing_data' => $billingData,
        ];
        $this->log('Classic PaymentKey Request: ' . json_encode($payload));
        $response = $this->postJson($url, $payload);
        $this->log('Classic PaymentKey Response: ' . json_encode($response));
        return $response;
    }

    /**
     * Step 4: Pay with wallet using Paymob Classic API
     */
    private function payWithWallet($paymentToken, $phoneNumber) {
        $url = 'https://accept.paymob.com/api/acceptance/payments/pay';
        $normalizedPhone = $this->normalizePhoneNumber($phoneNumber);
        $payload = [
            'source' => [
                'identifier' => $normalizedPhone,
                'subtype' => 'WALLET',
            ],
            'payment_token' => $paymentToken,
        ];
        $this->log('Wallet Pay Request: phone=' . $normalizedPhone);
        $response = $this->postJson($url, $payload);
        $this->log('Wallet Pay Response: ' . json_encode($response));
        return $response;
    }

    /**
     * Build billing_data for Classic API
     */
    private function buildBillingData($studentProfile) {
        $fullName = trim((string) ($studentProfile['full_name'] ?? 'Student IRB'));
        $nameParts = preg_split('/\s+/', $fullName, 2);
        $firstName = $nameParts[0] ?? 'Student';
        $lastName = $nameParts[1] ?? 'IRB';
        return [
            'apartment' => 'NA',
            'email' => ($studentProfile['email'] ?? '') ?: 'student@irb.edu',
            'floor' => 'NA',
            'first_name' => $firstName,
            'street' => 'NA',
            'building' => 'NA',
            'phone_number' => $this->normalizePhoneNumber($studentProfile['phone_number'] ?? ''),
            'shipping_method' => 'NA',
            'postal_code' => 'NA',
            'city' => 'Cairo',
            'country' => 'EG',
            'last_name' => $lastName,
            'state' => 'Cairo',
        ];
    }

    /**
     * Build Unified Checkout redirect URL
     */
    private function buildUnifiedCheckoutUrl($clientSecret) {
        $publicKey = $this->config['public_key'] ?? '';
        $baseUrl = $this->config['api']['unified_checkout'];

        if (empty($publicKey)) {
            $this->log('WARNING: PAYMOB_PUBLIC_KEY not set. Cannot build Unified Checkout URL.');
            throw new Exception('مفتاح Paymob العام (Public Key) غير مضبوط');
        }

        return $baseUrl . '?publicKey=' . urlencode($publicKey) . '&clientSecret=' . urlencode($clientSecret);
    }

    /**
     * Validate configuration
     */
    private function validateConfiguration() {
        $errors = [];

        // Card requires Intention API credentials
        if (empty($this->config['secret_key'])) {
            $errors[] = 'PAYMOB_SECRET_KEY missing (needed for card payments)';
        }
        if (empty($this->config['public_key'])) {
            $errors[] = 'PAYMOB_PUBLIC_KEY missing (needed for card payments)';
        }

        // Wallet requires Classic API credentials
        if (empty($this->config['api_key'])) {
            $errors[] = 'PAYMOB_API_KEY missing (needed for wallet payments)';
        }

        $hasWalletId = (int) ($this->config['wallet_integration_id'] ?? 0) > 0;
        $hasCardId = (int) ($this->config['card_integration_id'] ?? 0) > 0;
        $hasGenericId = (int) ($this->config['integration_id'] ?? 0) > 0;

        if (!$hasWalletId && !$hasCardId && !$hasGenericId) {
            $errors[] = 'No Paymob integration ID configured';
        }

        if ($this->getConfiguredAmount() <= 0) {
            $errors[] = 'Payment amount not configured';
        }

        if (!empty($errors)) {
            $msg = 'Paymob config errors: ' . implode(', ', $errors);
            $this->log($msg);
            throw new Exception($msg);
        }
    }

    /**
     * Verify webhook HMAC signature (for server-to-server callbacks)
     */
    public function verifyWebhookSignature($payload, $hmacHeader) {
        if ($hmacHeader === '' || empty($this->config['hmac_secret'])) {
            return false;
        }

        $directHmac = hash_hmac('sha512', $payload, $this->config['hmac_secret']);
        if (hash_equals($directHmac, $hmacHeader)) {
            return true;
        }

        $decoded = json_decode($payload, true);
        if (!is_array($decoded) || !isset($decoded['obj']) || !is_array($decoded['obj'])) {
            return false;
        }

        $flattened = [];
        $this->flattenWebhookValues($decoded['obj'], $flattened);
        ksort($flattened, SORT_STRING);

        $concatenated = '';
        foreach ($flattened as $value) {
            $concatenated .= $this->normalizeHmacValue($value);
        }

        $calculated = hash_hmac('sha512', $concatenated, $this->config['hmac_secret']);
        return hash_equals($calculated, $hmacHeader);
    }

    /**
     * Handle Paymob webhook callback data
     */
    public function handleWebhookCallback($webhookData) {
        $transactionId = (int) ($webhookData['obj']['id'] ?? 0);
        $orderId = (int) ($webhookData['obj']['order']['id'] ?? 0);
        $success = (bool) ($webhookData['obj']['success'] ?? false);

        if ($transactionId <= 0 || $orderId <= 0) {
            throw new Exception('بيانات webhook غير مكتملة');
        }

        if ($this->repository->isTransactionAlreadyProcessed($transactionId)) {
            return true;
        }

        $payment = $this->repository->getPaymentByPaymobOrderId($orderId);

        if (!$payment) {
            throw new Exception('تعذر مطابقة طلب Paymob مع سجلات الدفع');
        }

        if ($success) {
            $this->repository->markPaymentCompleted((int) $payment['id'], $transactionId);
            $newStatus = ($payment['payment_type'] === 'sample_size') ? 'fully_paid' : 'initial_paid';
            $this->repository->updateSubmissionStatus((int) $payment['submission_id'], $newStatus);

            if (!empty($payment['student_id'])) {
                try {
                    $this->repository->logSystemAction(
                        (int) $payment['student_id'],
                        (int) $payment['submission_id'],
                        'payment_confirmed',
                        'تم تأكيد دفع الرسوم الأولية عبر Paymob للبحث: ' . ($payment['title'] ?? ('#' . $payment['submission_id']))
                    );
                } catch (Exception $e) {
                    error_log('PaymentService::handleWebhookCallback Log Error: ' . $e->getMessage());
                }
            }

            return true;
        }

        $failureReason = 'فشل الدفع';
        if (!empty($webhookData['obj']['data']['message'])) {
            $failureReason = (string) $webhookData['obj']['data']['message'];
        } elseif (!empty($webhookData['obj']['txn_response_code'])) {
            $failureReason = 'رمز الاستجابة: ' . $webhookData['obj']['txn_response_code'];
        }

        $this->repository->markPaymentFailed((int) $payment['id'], $failureReason, $transactionId);
        return false;
    }

    private function flattenWebhookValues($data, &$flattened, $prefix = '') {
        foreach ($data as $key => $value) {
            $composedKey = $prefix === '' ? (string) $key : $prefix . '.' . $key;

            if (is_array($value)) {
                $this->flattenWebhookValues($value, $flattened, $composedKey);
            } else {
                $flattened[$composedKey] = $value;
            }
        }
    }

    private function normalizeHmacValue($value) {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return '';
        }

        return (string) $value;
    }

    private function normalizePhoneNumber($phoneNumber) {
        $digitsOnly = preg_replace('/\D+/', '', (string) $phoneNumber);

        if ($digitsOnly === '') {
            return '201000000000';
        }

        if (strpos($digitsOnly, '20') === 0) {
            return $digitsOnly;
        }

        if (strpos($digitsOnly, '0') === 0) {
            return '20' . substr($digitsOnly, 1);
        }

        return '20' . $digitsOnly;
    }

    private function postJson($url, $data) {
        $jsonPayload = json_encode($data);
        $this->log('postJson REQUEST: URL=' . $url . ' BODY=' . $jsonPayload);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonPayload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false || $curlError !== '') {
            $this->log('postJson CURL ERROR: ' . $curlError);
            throw new Exception('فشل الاتصال بخدمة Paymob: ' . $curlError);
        }

        $this->log('postJson RESPONSE: HTTP=' . $httpCode . ' BODY=' . $response);

        $decoded = json_decode($response, true);

        if ($httpCode < 200 || $httpCode >= 300) {
            $errorMsg = 'Paymob HTTP ' . $httpCode;
            if (is_array($decoded) && !empty($decoded['message'])) {
                $errorMsg .= ': ' . $decoded['message'];
            } elseif (is_array($decoded) && !empty($decoded['detail'])) {
                $errorMsg .= ': ' . json_encode($decoded['detail']);
            } else {
                $errorMsg .= ': ' . substr($response, 0, 500);
            }
            throw new Exception($errorMsg);
        }

        if (!is_array($decoded)) {
            throw new Exception('رد Paymob غير مفهوم: ' . substr($response, 0, 500));
        }

        return $decoded;
    }

    private function log($message) {
        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        file_put_contents($this->logFile, $line, FILE_APPEND | LOCK_EX);
    }

    private function ensureLogDirectory() {
        $dir = dirname($this->logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}

