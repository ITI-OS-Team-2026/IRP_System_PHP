<?php

class PaymentRepository {
    private $db;
    private static $schemaEnsured = false;

    public function __construct($db) {
        $this->db = $db;
        $this->ensurePaymentColumns();
    }

    public function createPendingPayment($submissionId, $amount, $paymentMethod = 'wallet', $paymentType = 'initial') {
        // Normalize payment method — only 'card' and 'wallet' are supported
        $method = strtolower(trim((string) $paymentMethod));
        if (in_array($method, ['visa', 'mastercard', 'credit', 'debit', 'card'])) {
            $method = 'card';
        } elseif (str_contains($method, 'cash') || str_contains($method, 'wallet')) {
            $method = 'wallet';
        } else {
            $method = 'card'; // safe fallback
        }

        $stmt = $this->prepare(
            "INSERT INTO payments (submission_id, payment_type, amount, payment_status, payment_method)
             VALUES (?, ?, ?, 'pending', ?)"
        );
        $stmt->bind_param('isds', $submissionId, $paymentType, $amount, $method);
        $stmt->execute();

        if ($stmt->errno !== 0) {
            throw new Exception('فشل إنشاء سجل الدفع: ' . $stmt->error);
        }

        return (int) $this->db->insert_id;
    }

    public function updatePaymentWithPaymobData($paymentId, $paymobOrderId) {
        $stmt = $this->prepare(
            "UPDATE payments
             SET paymob_order_id = ?
             WHERE id = ?"
        );
        $stmt->bind_param('ii', $paymobOrderId, $paymentId);
        $stmt->execute();

        if ($stmt->errno !== 0) {
            throw new Exception('فشل حفظ بيانات Paymob: ' . $stmt->error);
        }
    }

    public function markPaymentCompleted($paymentId, $transactionId) {
        $stmt = $this->prepare(
            "UPDATE payments
             SET payment_status = 'completed',
                 paymob_transaction_id = ?,
                 failure_reason = NULL,
                 transaction_date = NOW()
             WHERE id = ?"
        );
        $stmt->bind_param('ii', $transactionId, $paymentId);
        $stmt->execute();

        if ($stmt->errno !== 0) {
            throw new Exception('فشل تحديث حالة الدفع: ' . $stmt->error);
        }
    }

    public function markPaymentFailed($paymentId, $failureReason, $transactionId = null) {
        if ($transactionId === null) {
            $stmt = $this->prepare(
                "UPDATE payments
                 SET payment_status = 'failed',
                     failure_reason = ?,
                     paymob_transaction_id = NULL
                 WHERE id = ?"
            );
            $stmt->bind_param('si', $failureReason, $paymentId);
        } else {
            $stmt = $this->prepare(
                "UPDATE payments
                 SET payment_status = 'failed',
                     failure_reason = ?,
                     paymob_transaction_id = ?
                 WHERE id = ?"
            );
            $stmt->bind_param('sii', $failureReason, $transactionId, $paymentId);
        }

        $stmt->execute();

        if ($stmt->errno !== 0) {
            throw new Exception('فشل تحديث حالة فشل الدفع: ' . $stmt->error);
        }

        // Trigger optional payment failure notification
        $subStmt = $this->prepare("SELECT submission_id FROM payments WHERE id = ? LIMIT 1");
        $subStmt->bind_param('i', $paymentId);
        $subStmt->execute();
        $subRow = $subStmt->get_result()->fetch_assoc();

        if ($subRow && !empty($subRow['submission_id'])) {
            require_once __DIR__ . '/../Helpers/NotificationHelper.php';
            NotificationHelper::handleStatusChange($this->db, $subRow['submission_id'], null, null, 'payment_failed');
        }
    }

    public function getPaymentById($paymentId) {
        $stmt = $this->prepare(
            "SELECT p.*, rs.title, rs.student_id, rs.status, rs.serial_number
             FROM payments p
             LEFT JOIN research_submissions rs ON p.submission_id = rs.id
             WHERE p.id = ?
             LIMIT 1"
        );
        $stmt->bind_param('i', $paymentId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Fetch payment with full submission details (serial_number, title, status).
     * Safe read-only enhancement for receipt display (UI + PDF).
     */
    public function getPaymentWithSubmission($paymentId) {
        $stmt = $this->prepare(
            "SELECT p.*, rs.serial_number, rs.title, rs.student_id, rs.status
             FROM payments p
             JOIN research_submissions rs ON rs.id = p.submission_id
             WHERE p.id = ?
             LIMIT 1"
        );
        $stmt->bind_param('i', $paymentId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Returns the most recent completed payment for a given submission,
     * used to guard against duplicate payments.
     */
    public function getCompletedPaymentForSubmission($submissionId, $paymentType) {
        $stmt = $this->prepare(
            "SELECT p.*
             FROM payments p
             WHERE p.submission_id = ?
               AND p.payment_type  = ?
               AND p.payment_status = 'completed'
             ORDER BY p.id DESC
             LIMIT 1"
        );
        $stmt->bind_param('is', $submissionId, $paymentType);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getPaymentByPaymobOrderId($paymobOrderId) {
        $stmt = $this->prepare(
            "SELECT p.*, rs.student_id, rs.title, rs.status, rs.serial_number
             FROM payments p
             LEFT JOIN research_submissions rs ON p.submission_id = rs.id
             WHERE p.paymob_order_id = ?
             LIMIT 1"
        );
        $stmt->bind_param('i', $paymobOrderId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function isTransactionAlreadyProcessed($transactionId) {
        $stmt = $this->prepare(
            "SELECT id
             FROM payments
             WHERE paymob_transaction_id = ? AND payment_status = 'completed'
             LIMIT 1"
        );
        $stmt->bind_param('i', $transactionId);
        $stmt->execute();
        $result = $stmt->get_result();
        return (bool) $result->fetch_assoc();
    }

    public function updateSubmissionStatus($submissionId, $newStatus) {
        $statusStmt = $this->prepare("SELECT status FROM research_submissions WHERE id = ? LIMIT 1");
        $statusStmt->bind_param('i', $submissionId);
        $statusStmt->execute();
        $oldStatus = $statusStmt->get_result()->fetch_assoc()['status'] ?? '';

        $stmt = $this->prepare(
            "UPDATE research_submissions
             SET status = ?, updated_at = NOW()
             WHERE id = ?"
        );
        $stmt->bind_param('si', $newStatus, $submissionId);
        $stmt->execute();

        if ($stmt->errno !== 0) {
            throw new Exception('فشل تحديث حالة البحث: ' . $stmt->error);
        }

        require_once __DIR__ . '/../Helpers/NotificationHelper.php';
        NotificationHelper::handleStatusChange($this->db, $submissionId, $oldStatus, $newStatus);
    }

    public function getSubmissionById($submissionId) {
        $stmt = $this->prepare(
            "SELECT *
             FROM research_submissions
             WHERE id = ?
             LIMIT 1"
        );
        $stmt->bind_param('i', $submissionId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function verifyStudentOwnsSubmission($studentId, $submissionId) {
        $stmt = $this->prepare(
            "SELECT id
             FROM research_submissions
             WHERE id = ? AND student_id = ?
             LIMIT 1"
        );
        $stmt->bind_param('ii', $submissionId, $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        return (bool) $result->fetch_assoc();
    }

    public function getUserPaymentProfile($userId) {
        $stmt = $this->prepare(
            "SELECT full_name, email, phone_number, department, specialty
             FROM users
             WHERE id = ?
             LIMIT 1"
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            throw new Exception('لم يتم العثور على بيانات الطالب');
        }

        return $user;
    }

    public function logSystemAction($userId, $submissionId, $action, $details) {
        $stmt = $this->prepare(
            "INSERT INTO system_logs (user_id, submission_id, action, details)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param('iiss', $userId, $submissionId, $action, $details);
        $stmt->execute();

        if ($stmt->errno !== 0) {
            throw new Exception('فشل تسجيل سجل النظام: ' . $stmt->error);
        }
    }

    private function prepare($sql) {
        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception('فشل تجهيز الاستعلام: ' . $this->db->error);
        }

        return $stmt;
    }

    private function ensurePaymentColumns() {
        if (self::$schemaEnsured) {
            return;
        }

        $columns = [
            'paymob_order_id' => "ALTER TABLE payments ADD COLUMN paymob_order_id BIGINT NULL AFTER payment_status",
            'paymob_transaction_id' => "ALTER TABLE payments ADD COLUMN paymob_transaction_id BIGINT NULL AFTER paymob_order_id",
            'payment_method' => "ALTER TABLE payments ADD COLUMN payment_method VARCHAR(50) NULL AFTER paymob_transaction_id",
            'failure_reason' => "ALTER TABLE payments ADD COLUMN failure_reason VARCHAR(500) NULL AFTER payment_method",
        ];

        foreach ($columns as $columnName => $alterSql) {
            $result = $this->db->query("SHOW COLUMNS FROM payments LIKE '" . $this->db->real_escape_string($columnName) . "'");
            if ($result && $result->num_rows === 0) {
                $this->db->query($alterSql);
                if ($this->db->errno !== 0) {
                    throw new Exception('تعذر تجهيز جدول payments لدمج Paymob: ' . $this->db->error);
                }
            }
        }

        // Fix payment_method column if it exists as ENUM (convert to VARCHAR to avoid truncation)
        $colInfo = $this->db->query("SHOW COLUMNS FROM payments LIKE 'payment_method'");
        if ($colInfo && $colInfo->num_rows > 0) {
            $col = $colInfo->fetch_assoc();
            if (stripos($col['Type'], 'enum') !== false) {
                $this->db->query("ALTER TABLE payments MODIFY payment_method VARCHAR(50) NULL");
            }
        }

        $indexes = [
            'idx_paymob_order_id' => "ALTER TABLE payments ADD INDEX idx_paymob_order_id (paymob_order_id)",
            'idx_paymob_transaction_id' => "ALTER TABLE payments ADD INDEX idx_paymob_transaction_id (paymob_transaction_id)",
        ];

        foreach ($indexes as $indexName => $alterSql) {
            $result = $this->db->query("SHOW INDEX FROM payments WHERE Key_name = '" . $this->db->real_escape_string($indexName) . "'");
            if ($result && $result->num_rows === 0) {
                $this->db->query($alterSql);
            }
        }

        self::$schemaEnsured = true;
    }
}
