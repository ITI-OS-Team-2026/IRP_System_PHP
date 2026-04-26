<?php

require_once __DIR__ . '/../../config/database.php';

class ReviewerController {

    /**
     * Display the reviewer's dashboard with assigned reviews
     */
    public function dashboard() {
        AuthMiddleware::requireRole('reviewer');
        
        $reviewerId = (int) $_SESSION['user_id'];
        $db = Database::getConnection();
        
        // Fetch assigned reviews (Blind)
        $query = "SELECT r.id as review_id, r.review_status, rs.id as submission_id, rs.title, rs.serial_number, rs.created_at, rs.updated_at
                  FROM reviews r
                  JOIN research_submissions rs ON r.submission_id = rs.id
                  WHERE r.reviewer_id = ? AND r.review_status = 'pending'
                  ORDER BY rs.created_at ASC";
                  
        $stmt = $db->prepare($query);
        $stmt->bind_param('i', $reviewerId);
        $stmt->execute();
        $assignedReviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        require __DIR__ . '/../Views/reviewer/reviewer_dashboard.php';
    }

    /**
     * Display review history (non-pending reviews)
     */
    public function history() {
        AuthMiddleware::requireRole('reviewer');

        $reviewerId = (int) $_SESSION['user_id'];
        $db = Database::getConnection();

        $query = "SELECT r.id as review_id, r.review_status, r.feedback_notes, r.reviewed_at,
                         rs.id as submission_id, rs.title, rs.serial_number, rs.updated_at
                  FROM reviews r
                  JOIN research_submissions rs ON r.submission_id = rs.id
                  WHERE r.reviewer_id = ? AND r.review_status <> 'pending'
                  ORDER BY r.reviewed_at DESC, rs.updated_at DESC";

        $stmt = $db->prepare($query);
        $stmt->bind_param('i', $reviewerId);
        $stmt->execute();
        $reviewHistory = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        require __DIR__ . '/../Views/reviewer/reviewer_history.php';
    }

    /**
     * Show blind review details for a submission (includes evaluation form)
     */
    public function viewSubmission($submissionId) {
        AuthMiddleware::requireRole('reviewer');
        $reviewerId = (int) $_SESSION['user_id'];
        $submissionId = (int) $submissionId;
        
        $db = Database::getConnection();
        
        // Ensure assigned
        $check = $db->prepare("SELECT id FROM reviews WHERE submission_id = ? AND reviewer_id = ? LIMIT 1");
        $check->bind_param('ii', $submissionId, $reviewerId);
        $check->execute();
        if (!$check->get_result()->fetch_assoc()) {
            header('Location: ' . BASE_URL . '/reviewer/dashboard');
            exit;
        }
        
        // Fetch research data (Blind)
        $stmt = $db->prepare("SELECT id, title, serial_number, created_at, status FROM research_submissions WHERE id = ?");
        $stmt->bind_param('i', $submissionId);
        $stmt->execute();
        $submission = $stmt->get_result()->fetch_assoc();
        
        // Fetch current documents; if this is a re-review cycle, show only files changed in the latest revision round.
        $latestRoundStmt = $db->prepare("SELECT MAX(revision_round_id) AS latest_round FROM research_documents WHERE submission_id = ? AND revision_round_id IS NOT NULL");
        $latestRoundStmt->bind_param('i', $submissionId);
        $latestRoundStmt->execute();
        $latestRoundRow = $latestRoundStmt->get_result()->fetch_assoc();
        $latestRevisionRoundId = (int) ($latestRoundRow['latest_round'] ?? 0);

        if (($submission['status'] ?? '') === 'under_review' && $latestRevisionRoundId > 0) {
            $docStmt = $db->prepare(
                "SELECT document_type, file_path, uploaded_at, version
                 FROM research_documents
                 WHERE submission_id = ? AND revision_round_id = ? AND is_current = 1
                 ORDER BY uploaded_at DESC"
            );
            $docStmt->bind_param('ii', $submissionId, $latestRevisionRoundId);
        } else {
            $docStmt = $db->prepare(
                "SELECT document_type, file_path, uploaded_at, version
                 FROM research_documents
                 WHERE submission_id = ? AND is_current = 1
                 ORDER BY uploaded_at DESC"
            );
            $docStmt->bind_param('i', $submissionId);
        }
        $docStmt->execute();
        $documents = $docStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        require __DIR__ . '/../Views/reviewer/blind-review.php';
    }

    /**
     * Process evaluation submission using feedback_notes in reviews table
     */
    public function submitEvaluation() {
        AuthMiddleware::requireRole('reviewer');
        $reviewerId = (int) $_SESSION['user_id'];
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/reviewer/dashboard');
            exit;
        }
        
        $submissionId = (int) ($_POST['submission_id'] ?? 0);
        $decision = trim((string) ($_POST['decision'] ?? ''));
        $reviewerNotes = trim((string) ($_POST['reviewer_comments'] ?? ''));
        
        $reviewStatusMap = [
            'approve' => 'approved',
            'modify' => 'modification_requested',
            'reject' => 'rejected'
        ];
        
        $submissionStatusMap = [
            'approve' => 'approved',
            'modify' => 'revision_requested',
            'reject' => 'rejected'
        ];
        
        $revStatus = $reviewStatusMap[$decision] ?? 'pending';
        $subStatus = $submissionStatusMap[$decision] ?? 'under_review';

        if (!isset($reviewStatusMap[$decision])) {
            $_SESSION['review_error'] = 'قرار المراجعة غير صالح.';
            header('Location: ' . BASE_URL . '/reviewer/view/' . $submissionId);
            exit;
        }

        if ($decision === 'modify' && $reviewerNotes === '') {
            $_SESSION['review_error'] = 'يرجى إدخال ملاحظات التعديل عند طلب التعديل.';
            header('Location: ' . BASE_URL . '/reviewer/view/' . $submissionId);
            exit;
        }
        
        $db = Database::getConnection();
        $db->begin_transaction();
        
        try {
            // 1. Get Review ID
            $stmt = $db->prepare("SELECT id FROM reviews WHERE submission_id = ? AND reviewer_id = ? LIMIT 1");
            $stmt->bind_param('ii', $submissionId, $reviewerId);
            $stmt->execute();
            $review = $stmt->get_result()->fetch_assoc();
            
            if (!$review) throw new Exception("Review record not found.");
            $reviewId = $review['id'];
            
            // 2. Update Review record
            $updateReview = $db->prepare("UPDATE reviews SET review_status = ?, feedback_notes = ?, reviewed_at = CURRENT_TIMESTAMP WHERE id = ?");
            $updateReview->bind_param('ssi', $revStatus, $reviewerNotes, $reviewId);
            $updateReview->execute();
            
            $statusStmt = $db->prepare("SELECT status FROM research_submissions WHERE id = ?");
            $statusStmt->bind_param('i', $submissionId);
            $statusStmt->execute();
            $oldStatus = $statusStmt->get_result()->fetch_assoc()['status'] ?? '';

            // 3. Update main research_submissions status
            $updateSub = $db->prepare("UPDATE research_submissions SET status = ? WHERE id = ?");
            $updateSub->bind_param('si', $subStatus, $submissionId);
            $updateSub->execute();
            
            require_once __DIR__ . '/../Helpers/NotificationHelper.php';
            NotificationHelper::handleStatusChange($db, $submissionId, $oldStatus, $subStatus);
            
            $db->commit();
            $_SESSION['review_success'] = 'تم تسجيل قرارك بنجاح. شكراً لك.';
            header('Location: ' . BASE_URL . '/reviewer/dashboard');
            exit;
            
        } catch (Exception $e) {
            $db->rollback();
            $_SESSION['review_error'] = 'حدث خطأ: ' . $e->getMessage();
            header('Location: ' . BASE_URL . '/reviewer/view/' . $submissionId);
            exit;
        }
    }
}
