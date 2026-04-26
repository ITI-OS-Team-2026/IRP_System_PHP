<?php

require_once __DIR__ . '/../../config/database.php';

class SampleSizeController {

    /**
     * Display the list of research waiting for sample size calculation
     */
    public function queue() {
        AuthMiddleware::requireRole('sample_size_officer');
        
        $db = Database::getConnection();
        
        // Fetch submissions that have a serial number, status 'initial_paid', 
        // and a confirmed initial payment record
        $query = "SELECT rs.id, rs.title, rs.principal_investigator, rs.serial_number, rs.created_at, u.full_name as student_name 
                  FROM research_submissions rs
                  JOIN users u ON rs.student_id = u.id
                  JOIN payments p ON rs.id = p.submission_id
                  WHERE rs.status = 'initial_paid' 
                    AND rs.serial_number IS NOT NULL 
                    AND p.payment_type = 'initial' 
                    AND p.payment_status = 'completed'
                  GROUP BY rs.id
                  ORDER BY rs.created_at ASC";
                  
        $result = $db->query($query);
        $submissions = [];
        while ($row = $result->fetch_assoc()) {
            $submissions[] = $row;
        }
        
        $pageTitle = 'قائمة انتظار حساب حجم العينة';
        require __DIR__ . '/../Views/officer/calculation_queue.php';
    }

    /**
     * Display processed research submissions (Archives)
     */
    public function archives() {
        AuthMiddleware::requireRole('sample_size_officer');
        
        $db = Database::getConnection();
        
        // Fetch submissions that have been processed (sample_sized or later)
        $query = "SELECT rs.id, rs.title, rs.principal_investigator, rs.serial_number, rs.sample_size, rs.status, rs.updated_at, u.full_name as student_name 
                  FROM research_submissions rs
                  JOIN users u ON rs.student_id = u.id
                  WHERE rs.sample_size IS NOT NULL 
                    AND rs.status NOT IN ('submitted', 'admin_reviewed', 'initial_paid')
                  ORDER BY rs.updated_at DESC";
                  
        $result = $db->query($query);
        $submissions = [];
        while ($row = $result->fetch_assoc()) {
            $submissions[] = $row;
        }
        
        $pageTitle = 'أرشيف حسابات حجم العينة';
        require __DIR__ . '/../Views/officer/archives.php';
    }

    /**
     * Show the input form for a specific research
     */
    public function inputForm() {
        AuthMiddleware::requireRole('sample_size_officer');
        
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: ' . BASE_URL . '/officer/sample-size/queue');
            exit;
        }
        
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT rs.*, u.full_name as student_name 
                              FROM research_submissions rs
                              JOIN users u ON rs.student_id = u.id
                              JOIN payments p ON rs.id = p.submission_id
                              WHERE rs.id = ? 
                                AND rs.status = 'initial_paid' 
                                AND rs.serial_number IS NOT NULL
                                AND p.payment_type = 'initial'
                                AND p.payment_status = 'completed'
                              LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $submission = $stmt->get_result()->fetch_assoc();
        
        if (!$submission) {
            header('Location: ' . BASE_URL . '/officer/sample-size/queue');
            exit;
        }
        
        $pageTitle = 'إدخال حجم العينة - ' . $submission['title'];
        require __DIR__ . '/../Views/officer/sample_size_form.php';
    }

    /**
     * Store the calculated sample size
     */
    public function store() {
        AuthMiddleware::requireRole('sample_size_officer');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/officer/sample-size/queue');
            exit;
        }
        
        $id = (int) ($_POST['submission_id'] ?? 0);
        $sampleSize = (int) ($_POST['sample_size'] ?? 0);
        
        if ($id <= 0 || $sampleSize <= 0) {
            $_SESSION['officer_error'] = 'يرجى إدخال حجم عينة صالح.';
            header('Location: ' . BASE_URL . '/officer/sample-size/input/' . $id);
            exit;
        }
        
        $db = Database::getConnection();
        $db->begin_transaction();
        
        try {
            // Update research status and sample size with strict verification
            $stmt = $db->prepare("UPDATE research_submissions 
                                  SET sample_size = ?, status = 'sample_sized' 
                                  WHERE id = ? 
                                    AND status = 'initial_paid'
                                    AND serial_number IS NOT NULL
                                    AND EXISTS (
                                        SELECT 1 FROM payments 
                                        WHERE submission_id = ? 
                                          AND payment_type = 'initial' 
                                          AND payment_status = 'completed'
                                    )
                                  LIMIT 1");
            $stmt->bind_param('iii', $sampleSize, $id, $id);
            $stmt->execute();
            
            if ($db->affected_rows === 0) {
                throw new Exception("فشل في تحديث بيانات البحث. قد يكون البحث قد تمت معالجته بالفعل.");
            }
            
            require_once __DIR__ . '/../Helpers/NotificationHelper.php';
            NotificationHelper::handleStatusChange($db, $id, 'initial_paid', 'sample_sized');
            
            // Log the action
            $officerId = $_SESSION['user_id'];
            $logStmt = $db->prepare("INSERT INTO system_logs (user_id, submission_id, action, details) 
                                     VALUES (?, ?, 'sample_size_recorded', ?)");
            $details = "تم تسجيل حجم العينة: " . $sampleSize;
            $logStmt->bind_param('iis', $officerId, $id, $details);
            $logStmt->execute();
            
            $db->commit();
            
            $_SESSION['officer_success'] = 'تم تسجيل حجم العينة بنجاح ونقل البحث لمرحلة الدفع.';
            header('Location: ' . BASE_URL . '/officer/sample-size/queue');
            exit;
            
        } catch (Exception $e) {
            $db->rollback();
            $_SESSION['officer_error'] = 'حدث خطأ: ' . $e->getMessage();
            header('Location: ' . BASE_URL . '/officer/sample-size/input/' . $id);
            exit;
        }
    }
}
