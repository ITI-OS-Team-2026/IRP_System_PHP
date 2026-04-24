<?php

require_once __DIR__ . '/../../config/database.php';

class PaymentController {

    /**
     * Show the payment page based on research status
     */
    public function showPayment() {
        AuthMiddleware::requireRole('student');
        
        $id = (int) ($_GET['id'] ?? 0);
        $studentId = (int) $_SESSION['user_id'];
        
        if ($id <= 0) {
            header('Location: ' . BASE_URL . '/student/dashboard');
            exit;
        }
        
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, title, status, sample_size, serial_number 
                              FROM research_submissions 
                              WHERE id = ? AND student_id = ? 
                              LIMIT 1");
        $stmt->bind_param('ii', $id, $studentId);
        $stmt->execute();
        $submission = $stmt->get_result()->fetch_assoc();
        
        if (!$submission) {
            header('Location: ' . BASE_URL . '/student/dashboard');
            exit;
        }
        
        // Define payment details based on status
        $paymentType = '';
        $amount = 0;
        $description = '';
        
        if ($submission['status'] === 'admin_reviewed') {
            $paymentType = 'initial';
            $amount = 500.00; // Example fixed initial fee
            $description = 'رسوم تقديم البحث والمراجعة الأولية';
        } elseif ($submission['status'] === 'sample_sized') {
            $paymentType = 'sample_size';
            // Example calculation: 200 base + 5 per participant
            $amount = 200.00 + ($submission['sample_size'] * 5);
            $description = 'رسوم مراجعة حجم العينة واعتماد البحث (' . $submission['sample_size'] . ' مشارك)';
        } else {
            // Already paid or not ready for payment
            header('Location: ' . BASE_URL . '/student/submissions/' . $id);
            exit;
        }
        
        $pageTitle = 'بوابة الدفع الإلكتروني';
        require __DIR__ . '/../Views/student/payment_page.php';
    }

    /**
     * Process the payment (Mock processing)
     */
    public function process() {
        AuthMiddleware::requireRole('student');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/student/dashboard');
            exit;
        }
        
        $id = (int) ($_POST['submission_id'] ?? 0);
        $paymentType = $_POST['payment_type'] ?? '';
        $amount = (float) ($_POST['amount'] ?? 0);
        $studentId = (int) $_SESSION['user_id'];
        
        $db = Database::getConnection();
        
        // Security check
        $stmt = $db->prepare("SELECT id, status FROM research_submissions WHERE id = ? AND student_id = ? LIMIT 1");
        $stmt->bind_param('ii', $id, $studentId);
        $stmt->execute();
        $submission = $stmt->get_result()->fetch_assoc();
        
        if (!$submission) {
            header('Location: ' . BASE_URL . '/student/dashboard');
            exit;
        }
        
        $db->begin_transaction();
        
        try {
            // 1. Record payment
            $receiptUrl = 'receipt_' . uniqid() . '.pdf'; // Mock receipt
            $payStmt = $db->prepare("INSERT INTO payments (submission_id, payment_type, amount, payment_status, receipt_url) 
                                     VALUES (?, ?, ?, 'completed', ?)");
            $payStmt->bind_param('isds', $id, $paymentType, $amount, $receiptUrl);
            $payStmt->execute();
            
            // 2. Update Research Status
            $newStatus = ($paymentType === 'initial') ? 'initial_paid' : 'under_review';
            $updateStmt = $db->prepare("UPDATE research_submissions SET status = ? WHERE id = ?");
            $updateStmt->bind_param('si', $newStatus, $id);
            $updateStmt->execute();

            // 3. Automatic Reviewer Assignment (If fully paid/sample_sized)
            if ($paymentType === 'sample_size') {
                $revQuery = $db->query("SELECT id FROM users WHERE role = 'reviewer' AND is_active = 1");
                $reviewStmt = $db->prepare("INSERT INTO reviews (submission_id, reviewer_id, review_status) VALUES (?, ?, 'pending')");
                while ($reviewer = $revQuery->fetch_assoc()) {
                    $reviewStmt->bind_param('ii', $id, $reviewer['id']);
                    $reviewStmt->execute();
                }
            }
            
            // 4. Log Action
            $logAction = 'payment_confirmed';
            $details = "تم تأكيد دفع " . ($paymentType === 'initial' ? 'الرسوم الأولية' : 'رسوم العينة') . " بمبلغ " . $amount . " ج.م";
            $logStmt = $db->prepare("INSERT INTO system_logs (user_id, submission_id, action, details) 
                                     VALUES (?, ?, ?, ?)");
            $logStmt->bind_param('iiss', $studentId, $id, $logAction, $details);
            $logStmt->execute();
            
            $db->commit();
            
            $_SESSION['payment_success'] = "تمت عملية الدفع بنجاح. رقم الإيصال: " . $receiptUrl;
            header('Location: ' . BASE_URL . '/student/submissions/' . $id);
            exit;
            
        } catch (Exception $e) {
            $db->rollback();
            $_SESSION['payment_error'] = "حدث خطأ أثناء معالجة الدفع: " . $e->getMessage();
            header('Location: ' . BASE_URL . '/student/payment/' . $id);
            exit;
        }
    }
}
