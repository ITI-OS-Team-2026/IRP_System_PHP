<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Repositories/PaymentRepository.php';
require_once __DIR__ . '/../Repositories/NotificationRepository.php';
require_once __DIR__ . '/../Services/PaymentService.php';
require_once __DIR__ . '/../Helpers/PaymentHelpers.php';

class PaymentController {
    private $db;
    private $repository;
    private $service;
    private $notificationRepo;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->repository = new PaymentRepository($this->db);
        $this->service   = new PaymentService($this->repository);
        $this->notificationRepo = new NotificationRepository($this->db);
    }

    /**
     * Create a payment_success notification for the student.
     * Called ONLY when status transitions from non-completed → completed.
     * Catches its own exceptions so it never disrupts the payment flow.
     *
     * @param array  $payment  Row from getPaymentByPaymobOrderId / getPaymentById
     * @param string $logFile  Optional path for debug log
     */
    private function _createPaymentNotification(array $payment, string $logFile = ''): void {
        try {
            $paymentType  = $payment['payment_type'] ?? 'initial';
            $serialNumber = $payment['serial_number'] ?? '';

            if ($paymentType === 'sample_size') {
                $msg = 'تم سداد رسوم حجم العينة بنجاح';
            } else {
                $msg = 'تم سداد رسوم التقديم بنجاح';
            }

            if (!empty($serialNumber)) {
                $msg .= ' للبحث رقم ' . $serialNumber;
            }

            $this->notificationRepo->create([
                'user_id'    => (int) ($payment['student_id'] ?? 0),
                'type'       => 'payment_success',
                'message'    => $msg,
                'related_id' => (int) ($payment['submission_id'] ?? 0),
            ]);

            if ($logFile) {
                file_put_contents(
                    $logFile,
                    '[' . date('Y-m-d H:i:s') . "] Notification created for user {$payment['student_id']}\n",
                    FILE_APPEND | LOCK_EX
                );
            }
        } catch (Exception $e) {
            error_log('PaymentController: notification creation failed: ' . $e->getMessage());
            if ($logFile) {
                file_put_contents(
                    $logFile,
                    '[' . date('Y-m-d H:i:s') . '] Notification failed: ' . $e->getMessage() . "\n",
                    FILE_APPEND | LOCK_EX
                );
            }
        }
    }

    public function initiate() {
        AuthMiddleware::requireRole('student');

        // Prevent browser caching of payment pages
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        try {
            $studentId = (int) ($_SESSION['user_id'] ?? 0);
            $submissionId = (int) ($_GET['submission_id'] ?? 0);

            if ($submissionId <= 0) {
                throw new Exception('معرف البحث غير صالح');
            }

            if (!$this->repository->verifyStudentOwnsSubmission($studentId, $submissionId)) {
                throw new Exception('لا يمكنك الوصول إلى عملية الدفع لهذا البحث');
            }

            $submission = $this->repository->getSubmissionById($submissionId);

            if (!$submission) {
                throw new Exception('لم يتم العثور على البحث المطلوب');
            }

            $paymentType = $_GET['type'] ?? null;
            $logFile = __DIR__ . '/../../storage/logs/payment_debug.log';
            file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] [Initiate] submission_id={$submissionId}, type_param=" . ($paymentType ?? 'MISSING') . ", db_status=" . ($submission['status'] ?? 'UNKNOWN') . "\n", FILE_APPEND | LOCK_EX);

            if (!in_array($paymentType, ['initial', 'sample_size'])) {
                throw new Exception('نوع الدفع غير محدد بشكل صحيح. يرجى المحاولة من خلال لوحة التحكم.');
            }

            // Guard: redirect if payment already completed (back-button / re-visit protection)
            $existingCompleted = $this->repository->getCompletedPaymentForSubmission($submissionId, $paymentType);
            if ($existingCompleted) {
                $_SESSION['submission_success'] = 'تم الدفع بالفعل';
                header('Location: ' . BASE_URL . '/payment/receipt?payment_id=' . (int) $existingCompleted['id']);
                exit;
            }

            if ($paymentType === 'initial' && $submission['status'] !== 'admin_reviewed') {
                throw new Exception('الدفع الأولي متاح فقط بعد مراجعة الإدارة');
            } elseif ($paymentType === 'sample_size' && $submission['status'] !== 'sample_sized') {
                throw new Exception('دفع رسوم العينة متاح فقط بعد حساب حجم العينة');
            }

            $paymentAmount = $paymentType === 'initial'
                ? $this->service->getConfiguredAmount()
                : $this->service->getSampleSizeAmount($submission['sample_size']);
            require __DIR__ . '/../Views/student/payment_initiate.php';

        } catch (Exception $e) {
            $_SESSION['submission_error'] = $e->getMessage();
            header('Location: ' . BASE_URL . '/student/submissions');
            exit;
        }
    }

    public function store() {
        AuthMiddleware::requireRole('student');

        // Prevent browser caching
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('طريقة الطلب غير مسموح بها');
            }

            $studentId = (int) ($_SESSION['user_id'] ?? 0);
            $submissionId = (int) ($_POST['submission_id'] ?? 0);

            if ($submissionId <= 0) {
                throw new Exception('معرف البحث غير صالح');
            }

            if (!$this->repository->verifyStudentOwnsSubmission($studentId, $submissionId)) {
                throw new Exception('لا يمكنك بدء الدفع لهذا البحث');
            }

            $submission = $this->repository->getSubmissionById($submissionId);

            if (!$submission) {
                throw new Exception('لم يتم العثور على البحث المطلوب');
            }

            $paymentType = $_POST['payment_type'] ?? null;
            $logFile = __DIR__ . '/../../storage/logs/payment_debug.log';
            file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] [Store] submission_id={$submissionId}, type_param=" . ($paymentType ?? 'MISSING') . ", db_status=" . ($submission['status'] ?? 'UNKNOWN') . "\n", FILE_APPEND | LOCK_EX);

            if (!in_array($paymentType, ['initial', 'sample_size'])) {
                throw new Exception('نوع الدفع غير محدد بشكل صحيح.');
            }

            // Guard: duplicate payment prevention
            $existingCompleted = $this->repository->getCompletedPaymentForSubmission($submissionId, $paymentType);
            if ($existingCompleted) {
                $_SESSION['submission_success'] = 'تم الدفع بالفعل';
                header('Location: ' . BASE_URL . '/payment/receipt?payment_id=' . (int) $existingCompleted['id']);
                exit;
            }

            if ($paymentType === 'initial' && $submission['status'] !== 'admin_reviewed') {
                throw new Exception('الدفع الأولي غير متاح لهذا البحث حالياً');
            } elseif ($paymentType === 'sample_size' && $submission['status'] !== 'sample_sized') {
                throw new Exception('دفع رسوم العينة غير متاح لهذا البحث حالياً');
            }

            $paymentAmount = $paymentType === 'initial'
                ? $this->service->getConfiguredAmount()
                : $this->service->getSampleSizeAmount($submission['sample_size']);
            $paymentMethod = $_POST['payment_method'] ?? 'card';

            if (!in_array($paymentMethod, ['card', 'wallet'], true)) {
                $paymentMethod = 'card';
            }

            $paymentId = $this->repository->createPendingPayment($submissionId, $paymentAmount, $paymentMethod, $paymentType);
            $studentProfile = $this->repository->getUserPaymentProfile($studentId);

            if ($paymentMethod === 'wallet') {
                $walletPhone = trim($_POST['wallet_phone'] ?? '');
                $digitsOnly = preg_replace('/\D/', '', $walletPhone);
                if (strlen($digitsOnly) !== 11 || !preg_match('/^01[0-9]{9}$/', $digitsOnly)) {
                    throw new Exception('رقم هاتف المحفظة غير صالح. يجب أن يكون 11 رقم ويبدأ بـ 01');
                }
                $studentProfile['wallet_phone'] = $digitsOnly;
            }

            $redirectUrl = $this->service->initiatePayment($paymentId, $studentProfile, $paymentMethod);

            $_SESSION['payment_id'] = $paymentId;

            header('Location: ' . $redirectUrl);
            exit;

        } catch (Exception $e) {
            error_log('PaymentController::store Error: ' . $e->getMessage());
            $_SESSION['submission_error'] = 'تعذر بدء عملية الدفع: ' . $e->getMessage();
            header('Location: ' . BASE_URL . '/student/submissions');
            exit;
        }
    }

    public function webhook() {
        try {
            header('Content-Type: application/json; charset=utf-8');

            $logFile = __DIR__ . '/../../storage/logs/paymob_webhook.log';
            $logDir = dirname($logFile);
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }

            $payload = file_get_contents('php://input');
            $hmacHeader = $_SERVER['HTTP_X_PAYMOB_HMAC'] ?? ($_SERVER['HTTP_HMAC'] ?? ($_POST['hmac'] ?? ($_GET['hmac'] ?? '')));

            file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Webhook received. HMAC header present: " . (empty($hmacHeader) ? 'NO' : 'YES') . "\n", FILE_APPEND | LOCK_EX);

            if ($payload === false || $payload === '') {
                file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Empty payload — rejected\n", FILE_APPEND | LOCK_EX);
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Empty payload']);
                exit;
            }

            file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Payload: " . substr($payload, 0, 500) . "\n", FILE_APPEND | LOCK_EX);

            // Verify HMAC if both header and secret are configured
            // If HMAC secret is not configured, allow through with a warning (dev/staging)
            $hmacConfigured = !empty($this->service->getHmacSecret());
            if ($hmacConfigured && !empty($hmacHeader)) {
                if (!$this->service->verifyWebhookSignature($payload, $hmacHeader)) {
                    file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] HMAC verification FAILED — rejecting\n", FILE_APPEND | LOCK_EX);
                    http_response_code(401);
                    echo json_encode(['status' => 'error', 'message' => 'Invalid signature']);
                    exit;
                }
                file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] HMAC verification PASSED\n", FILE_APPEND | LOCK_EX);
            } else {
                file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] HMAC check SKIPPED (secret not configured or header missing) — processing anyway\n", FILE_APPEND | LOCK_EX);
            }

            $webhookData = json_decode($payload, true);

            if (!is_array($webhookData)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
                exit;
            }

            $this->service->handleWebhookCallback($webhookData);
            file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Webhook processed successfully\n", FILE_APPEND | LOCK_EX);

            http_response_code(200);
            echo json_encode(['status' => 'ok']);
            exit;

        } catch (Exception $e) {
            error_log('PaymentController::webhook Error: ' . $e->getMessage());
            $logFile = __DIR__ . '/../../storage/logs/paymob_webhook.log';
            file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Webhook exception: " . $e->getMessage() . "\n", FILE_APPEND | LOCK_EX);
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Webhook handling failed']);
            exit;
        }
    }

    public function callback() {
        try {
            $logFile = __DIR__ . '/../../storage/logs/paymob_callback.log';
            $logDir = dirname($logFile);
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }

            $logData = [
                'timestamp' => date('Y-m-d H:i:s'),
                'method'    => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
                'uri'       => $_SERVER['REQUEST_URI'] ?? '',
                'get'       => $_GET,
                'post'      => $_POST,
            ];
            file_put_contents($logFile, json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND | LOCK_EX);

            $orderId       = (int) ($_POST['order'] ?? $_GET['order'] ?? 0);
            $transactionId = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
            $rawSuccess    = (string) ($_POST['success'] ?? $_GET['success'] ?? 'false');
            $txnResponseCode = (string) ($_POST['txn_response_code'] ?? $_GET['txn_response_code'] ?? '');

            // Fix: use explicit string comparison — Paymob sends success="true" (string)
            $success = (
                strtolower(trim($rawSuccess)) === 'true' &&
                trim($txnResponseCode) === '200'
            );

            file_put_contents($logFile, "[Callback] raw_success={$rawSuccess}, parsed_success=" . ($success ? '1' : '0') . ", order={$orderId}, txn_id={$transactionId}, txn_response_code={$txnResponseCode}\n", FILE_APPEND | LOCK_EX);

            if ($orderId > 0) {
                $payment = $this->repository->getPaymentByPaymobOrderId($orderId);
                if ($payment) {
                    if ($success) {
                        // Guard: only notify + mark completed if not already completed (idempotency)
                        $wasAlreadyCompleted = ($payment['payment_status'] ?? '') === 'completed';
                        $this->repository->markPaymentCompleted((int) $payment['id'], $transactionId);
                        $newStatus = ($payment['payment_type'] === 'sample_size') ? 'fully_paid' : 'initial_paid';
                        $this->repository->updateSubmissionStatus((int) $payment['submission_id'], $newStatus);
                        file_put_contents($logFile, "[Callback] Payment {$payment['id']} marked COMPLETED, submission marked {$newStatus}\n", FILE_APPEND | LOCK_EX);
                        // Create notification only on first completion (prevents duplicates from webhook+callback)
                        if (!$wasAlreadyCompleted) {
                            $this->_createPaymentNotification($payment, $logFile);
                        }
                    } else {
                        $failureReason = $_POST['data_message'] ?? $_GET['data_message'] ?? 'فشل الدفع (callback)';
                        $this->repository->markPaymentFailed((int) $payment['id'], $failureReason, $transactionId);
                        file_put_contents($logFile, "[Callback] Payment {$payment['id']} marked FAILED: {$failureReason}\n", FILE_APPEND | LOCK_EX);
                    }
                } else {
                    file_put_contents($logFile, "[Callback] No payment found for paymob_order_id={$orderId}\n", FILE_APPEND | LOCK_EX);
                }
            }

            http_response_code(200);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'OK';
            exit;

        } catch (Exception $e) {
            error_log('PaymentController::callback Error: ' . $e->getMessage());
            http_response_code(200);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'OK';
            exit;
        }
    }

    public function paymobReturn() {
        // Prevent browser caching of return/receipt pages
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        try {
            $logFile = __DIR__ . '/../../storage/logs/paymob_receipt.log';
            $logDir = dirname($logFile);
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }

            // Guard: if no Paymob params at all, redirect safely to dashboard
            $hasPaymobParams = isset($_GET['order']) || isset($_GET['id']) || isset($_GET['success']);
            if (!$hasPaymobParams) {
                $_SESSION['submission_error'] = '\u0631\u0627\u0628\u0637 \u0627\u0644\u0639\u0648\u062f\u0629 \u063a\u064a\u0631 \u0635\u0627\u0644\u062d';
                header('Location: ' . BASE_URL . '/student/dashboard');
                exit;
            }

            $logData = [
                'timestamp' => date('Y-m-d H:i:s'),
                'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
                'uri' => $_SERVER['REQUEST_URI'] ?? '',
                'get' => $_GET,
                'post' => $_POST,
            ];
            file_put_contents($logFile, json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND | LOCK_EX);

            $rawSuccess = (string) ($_GET['success'] ?? $_POST['success'] ?? 'false');
            $rawPending = (string) ($_GET['pending'] ?? $_POST['pending'] ?? 'false');
            $orderId = (int) ($_GET['order'] ?? $_GET['order_id'] ?? $_POST['order'] ?? 0);
            $transactionId = (int) ($_GET['id'] ?? $_GET['transaction_id'] ?? $_POST['id'] ?? 0);
            $amountCents = (int) ($_GET['amount_cents'] ?? 0);
            $currency = htmlspecialchars($_GET['currency'] ?? 'EGP', ENT_QUOTES, 'UTF-8');
            $txnResponseCode = (string) ($_GET['txn_response_code'] ?? $_POST['txn_response_code'] ?? '');

            // Fix: Paymob Unified Checkout (card) may send success="true" without txn_response_code=200
            // Accept success=true as valid, regardless of txn_response_code
            $success = strtolower(trim($rawSuccess)) === 'true';
            // Treat txn_response_code=200 or empty/missing as valid (not a hard failure indicator)
            if (!empty($txnResponseCode) && $txnResponseCode !== '200' && $txnResponseCode !== 'APPROVED') {
                $success = false; // explicit failure code present — override
            }
            $pending = strtolower(trim($rawPending)) === 'true';

            // Debug logging
            file_put_contents($logFile, "[Return] raw_success={$rawSuccess}, raw_pending={$rawPending}, txn_response_code={$txnResponseCode}, parsed_success=" . ($success ? '1' : '0') . ", parsed_pending=" . ($pending ? '1' : '0') . ", order={$orderId}, txn_id={$transactionId}\n", FILE_APPEND | LOCK_EX);

            $payment = null;
            if ($orderId > 0) {
                $payment = $this->repository->getPaymentByPaymobOrderId($orderId);

                if ($payment) {
                    $isConfirmedSuccess = $success && !$pending;
                    $dbStatus = $payment['payment_status'] ?? 'unknown';
                    file_put_contents($logFile, "[Return] Found payment ID={$payment['id']}, db_status={$dbStatus}, confirmed_success=" . ($isConfirmedSuccess ? '1' : '0') . "\n", FILE_APPEND | LOCK_EX);

                    // DB fallback: if payment is already completed in DB (e.g. via webhook/callback),
                    // always treat as success for the UI regardless of GET params
                    if ($dbStatus === 'completed') {
                        $success = true;
                        $pending = false;
                        file_put_contents($logFile, "[Return] Payment {$payment['id']} already COMPLETED in DB — forcing UI success\n", FILE_APPEND | LOCK_EX);
                    } elseif ($isConfirmedSuccess) {
                        $this->repository->markPaymentCompleted((int) $payment['id'], $transactionId);
                        $newStatus = ($payment['payment_type'] === 'sample_size') ? 'fully_paid' : 'initial_paid';
                        $this->repository->updateSubmissionStatus((int) $payment['submission_id'], $newStatus);
                        file_put_contents($logFile, "[Return] Payment {$payment['id']} marked COMPLETED, submission marked {$newStatus}\n", FILE_APPEND | LOCK_EX);
                        // $dbStatus was not 'completed' (that branch was handled above), so this is a fresh completion
                        $this->_createPaymentNotification($payment, $logFile);
                    } elseif (!$pending) {
                        $failureReason = $_GET['data_message'] ?? $_POST['data_message'] ?? 'فشل الدفع';
                        $this->repository->markPaymentFailed((int) $payment['id'], $failureReason, $transactionId);
                        file_put_contents($logFile, "[Return] Payment {$payment['id']} marked FAILED: {$failureReason}\n", FILE_APPEND | LOCK_EX);
                    } else {
                        file_put_contents($logFile, "[Return] Payment {$payment['id']} still PENDING\n", FILE_APPEND | LOCK_EX);
                    }
                } else {
                    file_put_contents($logFile, "[Return] No payment found for paymob_order_id={$orderId}\n", FILE_APPEND | LOCK_EX);
                }
            }

            if ($payment && isset($_SESSION['user_id']) && (int) $payment['student_id'] === (int) $_SESSION['user_id']) {
                header('Location: ' . BASE_URL . '/payment/receipt?payment_id=' . (int) $payment['id']);
                exit;
            }

            $baseUrl = BASE_URL;
            require __DIR__ . '/../Views/student/payment_result.php';

        } catch (Exception $e) {
            error_log('PaymentController::paymobReturn Error: ' . $e->getMessage());
            $success = false;
            $pending = false;
            $orderId = 0;
            $transactionId = 0;
            $amountCents = 0;
            $currency = 'EGP';
            $baseUrl = BASE_URL;
            require __DIR__ . '/../Views/student/payment_result.php';
        }
    }

    public function receipt() {
        AuthMiddleware::requireRole('student');

        // Prevent stale cached receipt pages
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        try {
            $studentId = (int) ($_SESSION['user_id'] ?? 0);
            $paymentId = (int) ($_GET['payment_id'] ?? ($_SESSION['payment_id'] ?? 0));

            if ($paymentId <= 0) {
                throw new Exception('معرف الدفعة غير صالح');
            }


            // Always fetch fresh from DB — never rely on session/cache for status
            $payment = $this->repository->getPaymentWithSubmission($paymentId);

            if (!$payment) {
                throw new Exception('\u0644\u0645 \u064a\u062a\u0645 \u0627\u0644\u0639\u062b\u0648\u0631 \u0639\u0644\u0649 \u0628\u064a\u0627\u0646\u0627\u062a \u0627\u0644\u062f\u0641\u0639\u0629');
            }

            // Security: ownership check
            if ((int) $payment['student_id'] !== $studentId) {
                throw new Exception('\u0644\u0627 \u064a\u0645\u0643\u0646\u0643 \u0627\u0644\u0648\u0635\u0648\u0644 \u0625\u0644\u0649 \u0647\u0630\u0647 \u0627\u0644\u062f\u0641\u0639\u0629');
            }

            require __DIR__ . '/../Views/student/payment_receipt.php';

        } catch (Exception $e) {
            $_SESSION['submission_error'] = $e->getMessage();
            header('Location: ' . BASE_URL . '/student/submissions');
            exit;
        }
    }

    public function downloadReceipt() {
        AuthMiddleware::requireRole('student');

        try {
            $studentId = (int) ($_SESSION['user_id'] ?? 0);
            $paymentId = (int) ($_GET['payment_id'] ?? 0);

            if ($paymentId <= 0) {
                throw new Exception('معرف الدفعة غير صالح');
            }

            $payment = $this->repository->getPaymentWithSubmission($paymentId);

            if (!$payment) {
                throw new Exception('لم يتم العثور على بيانات الدفعة');
            }

            if ((int) $payment['student_id'] !== $studentId) {
                throw new Exception('لا يمكنك الوصول إلى هذه الدفعة');
            }

            if (!class_exists('\\Mpdf\\Mpdf')) {
                throw new Exception('مكتبة PDF غير متوفرة. يرجى تشغيل: composer install');
            }

            $paymentStatus = $payment['payment_status'] ?? 'pending';
            $statusLabel = PaymentHelpers::getStatusLabel($paymentStatus);
            $paymentMethod = ($payment['payment_method'] ?? '') === 'wallet' ? 'محفظة إلكترونية' : 'بطاقة';
            $paymentTypeLabel = ($payment['payment_type'] ?? 'initial') === 'sample_size' ? 'رسوم حجم العينة' : 'رسوم التقديم';
            $amount = PaymentHelpers::formatAmount($payment['amount']);
            $date = PaymentHelpers::formatDate($payment['transaction_date'] ?? $payment['created_at'] ?? '');
            $title = htmlspecialchars($payment['title'] ?? '', ENT_QUOTES, 'UTF-8');
            $serialNumber = htmlspecialchars($payment['serial_number'] ?? '', ENT_QUOTES, 'UTF-8');
            $txnId = $payment['paymob_transaction_id'] ?? '';
            $orderId = $payment['paymob_order_id'] ?? '';

            $statusColor = $paymentStatus === 'completed' ? '#059669' : ($paymentStatus === 'pending' ? '#d97706' : '#dc2626');

            $html = '
            <div style="font-family: DejaVu Sans, sans-serif; direction: rtl; text-align: right; padding: 20px;">
                <div style="text-align: center; margin-bottom: 30px; border-bottom: 2px solid #3f4779; padding-bottom: 20px;">
                    <h1 style="color: #3f4779; margin: 0; font-size: 28px;">IRB</h1>
                    <p style="color: #64748b; margin: 5px 0 0 0; font-size: 14px;">لجنة أخلاقيات البحث المؤسسية</p>
                </div>

                <h2 style="text-align: center; color: #1e293b; font-size: 20px; margin-bottom: 25px;">إيصال دفع</h2>

                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px 8px; color: #64748b; width: 40%;">رقم الدفعة</td>
                        <td style="padding: 12px 8px; font-weight: bold; color: #1e293b;">#' . (int) $payment['id'] . '</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px 8px; color: #64748b;">نوع الدفع</td>
                        <td style="padding: 12px 8px; font-weight: bold; color: #1e293b;">' . $paymentTypeLabel . '</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px 8px; color: #64748b;">الرقم التسلسلي</td>
                        <td style="padding: 12px 8px; font-weight: bold; color: #1e293b;">' . $serialNumber . '</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px 8px; color: #64748b;">الحالة</td>
                        <td style="padding: 12px 8px;"><span style="color: ' . $statusColor . '; font-weight: bold;">' . $statusLabel . '</span></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px 8px; color: #64748b;">المبلغ</td>
                        <td style="padding: 12px 8px; font-weight: bold; color: #1e293b; font-size: 16px;">' . $amount . '</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px 8px; color: #64748b;">طريقة الدفع</td>
                        <td style="padding: 12px 8px; color: #1e293b;">' . $paymentMethod . '</td>
                    </tr>';

            if ($txnId) {
                $html .= '
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px 8px; color: #64748b;">رقم العملية</td>
                        <td style="padding: 12px 8px; color: #1e293b;">' . htmlspecialchars($txnId, ENT_QUOTES, 'UTF-8') . '</td>
                    </tr>';
            }

            if ($orderId) {
                $html .= '
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px 8px; color: #64748b;">رقم الطلب</td>
                        <td style="padding: 12px 8px; color: #1e293b;">' . htmlspecialchars($orderId, ENT_QUOTES, 'UTF-8') . '</td>
                    </tr>';
            }

            $html .= '
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px 8px; color: #64748b;">تاريخ العملية</td>
                        <td style="padding: 12px 8px; color: #1e293b;">' . $date . '</td>
                    </tr>
                </table>';

            if ($title !== '') {
                $html .= '
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                    <p style="color: #64748b; margin: 0 0 5px 0; font-size: 12px;">عنوان البحث</p>
                    <p style="color: #1e293b; margin: 0; font-weight: bold;">' . $title . '</p>
                </div>';
            }

            $html .= '
                <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                    <p style="color: #94a3b8; font-size: 11px; margin: 0;">تم إنشاء هذا الإيصال آليا بواسطة نظام IRB</p>
                    <p style="color: #94a3b8; font-size: 11px; margin: 3px 0 0 0;">' . date('Y-m-d H:i:s') . '</p>
                </div>
            </div>';

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font' => 'dejavusans',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 20,
                'margin_bottom' => 20,
                'tempDir' => __DIR__ . '/../../storage/tmp',
            ]);

            $mpdf->SetTitle('إيصال دفع #' . (int) $payment['id']);
            $mpdf->SetAuthor('IRB System');
            $mpdf->WriteHTML($html);

            $filename = 'receipt_' . (int) $payment['id'] . '.pdf';
            $mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
            exit;

        } catch (Exception $e) {
            $_SESSION['submission_error'] = $e->getMessage();
            header('Location: ' . BASE_URL . '/student/submissions');
            exit;
        }
    }
}
