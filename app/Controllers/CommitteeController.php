<?php

require_once __DIR__ . '/../../config/database.php';

class CommitteeController {
    public function dashboard() {
        $this->certificates();
    }

    public function approvals() {
        AuthMiddleware::requireRole('manager');

        $db = Database::getConnection();
        $hasCertificatesTable = $this->hasCertificatesTable($db);
        if (!$hasCertificatesTable) {
            $_SESSION['committee_error'] = 'جدول الشهادات غير موجود بعد. شغّل ملف migration الخاص بالشهادات.';
        }

        if ($hasCertificatesTable) {
            $query = "SELECT rs.id, rs.title, rs.serial_number, rs.created_at, rs.updated_at,
                             u.full_name AS student_name, u.department, u.specialty,
                             r.reviewed_at, r.feedback_notes,
                             c.id AS certificate_id, c.certificate_number, c.issued_at
                      FROM research_submissions rs
                      INNER JOIN reviews r ON r.submission_id = rs.id
                      INNER JOIN users u ON u.id = rs.student_id
                      LEFT JOIN certificates c ON c.submission_id = rs.id
                      WHERE rs.status = 'approved' AND r.review_status = 'approved' AND c.id IS NULL
                      ORDER BY COALESCE(r.reviewed_at, rs.updated_at) DESC";
        } else {
            $query = "SELECT rs.id, rs.title, rs.serial_number, rs.created_at, rs.updated_at,
                             u.full_name AS student_name, u.department, u.specialty,
                             r.reviewed_at, r.feedback_notes,
                             NULL AS certificate_id, NULL AS certificate_number, NULL AS issued_at
                      FROM research_submissions rs
                      INNER JOIN reviews r ON r.submission_id = rs.id
                      INNER JOIN users u ON u.id = rs.student_id
                      WHERE rs.status = 'approved' AND r.review_status = 'approved'
                      ORDER BY COALESCE(r.reviewed_at, rs.updated_at) DESC";
        }

        $result = $db->query($query);
        $submissions = [];
        while ($row = $result->fetch_assoc()) {
            $submissions[] = $row;
        }

        require __DIR__ . '/../Views/committee/approvals.php';
    }

    public function certificates() {
        AuthMiddleware::requireRole('manager');

        $db = Database::getConnection();
        if (!$this->hasCertificatesTable($db)) {
            $_SESSION['committee_error'] = 'جدول الشهادات غير موجود. شغّل migration أولاً.';
            $certificates = [];
            require __DIR__ . '/../Views/committee/dashboard.php';
            return;
        }

        $query = "SELECT c.id AS certificate_id, c.submission_id, c.certificate_number, c.issued_at,
                         rs.title, rs.serial_number,
                         u.full_name AS student_name
                  FROM certificates c
                  INNER JOIN research_submissions rs ON rs.id = c.submission_id
                  INNER JOIN users u ON u.id = rs.student_id
                  ORDER BY c.issued_at DESC";

        $result = $db->query($query);
        $certificates = [];
        while ($row = $result->fetch_assoc()) {
            $certificates[] = $row;
        }

        require __DIR__ . '/../Views/committee/dashboard.php';
    }

    public function deleteCertificate($certificateId) {
        AuthMiddleware::requireRole('manager');
        $certificateId = (int) $certificateId;
        $db = Database::getConnection();

        if (!$this->hasCertificatesTable($db)) {
            $_SESSION['committee_error'] = 'جدول الشهادات غير موجود.';
            header('Location: ' . BASE_URL . '/committee/certificates');
            exit;
        }

        $stmt = $db->prepare("DELETE FROM certificates WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $certificateId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION['committee_success'] = 'تم حذف الشهادة بنجاح.';
        } else {
            $_SESSION['committee_error'] = 'لم يتم العثور على الشهادة المطلوبة.';
        }
        header('Location: ' . BASE_URL . '/committee/certificates');
        exit;
    }

    public function certificate($submissionId) {
        AuthMiddleware::requireRole('manager');

        $submissionId = (int) $submissionId;
        $db = Database::getConnection();
        $managerId = (int) $_SESSION['user_id'];
        $hasCertificatesTable = $this->hasCertificatesTable($db);
        if (!$hasCertificatesTable) {
            $_SESSION['committee_error'] = 'جدول الشهادات غير موجود. شغّل migration أولاً.';
            header('Location: ' . BASE_URL . '/committee/dashboard');
            exit;
        }

        $stmt = $db->prepare(
            "SELECT rs.id, rs.title, rs.serial_number, rs.created_at, rs.updated_at,
                    rs.principal_investigator, rs.co_investigators,
                    u.id AS student_id, u.full_name AS student_name, u.department, u.specialty,
                    r.review_status, r.feedback_notes, r.reviewed_at,
                    c.id AS certificate_id, c.certificate_number, c.issued_at
             FROM research_submissions rs
             INNER JOIN users u ON u.id = rs.student_id
             INNER JOIN reviews r ON r.submission_id = rs.id
             LEFT JOIN certificates c ON c.submission_id = rs.id
             WHERE rs.id = ? AND rs.status = 'approved' AND r.review_status = 'approved'
             LIMIT 1"
        );
        $stmt->bind_param('i', $submissionId);
        $stmt->execute();
        $submission = $stmt->get_result()->fetch_assoc();

        if (!$submission) {
            $_SESSION['committee_error'] = 'لا يمكن إصدار شهادة لهذا البحث حالياً.';
            header('Location: ' . BASE_URL . '/committee/dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->issueCertificate($submission, $managerId);
            header('Location: ' . BASE_URL . '/committee/certificate/' . $submissionId);
            exit;
        }

        require __DIR__ . '/../Views/committee/certificate.php';
    }

    public function downloadCertificate($submissionId) {
        AuthMiddleware::requireRole(['manager', 'student']);

        $submissionId = (int) $submissionId;
        $db = Database::getConnection();
        if (!$this->hasCertificatesTable($db)) {
            http_response_code(404);
            echo 'Certificate table not found.';
            exit;
        }
        $currentUser = AuthMiddleware::user();
        $isStudent = ($currentUser['role'] ?? '') === 'student';

        $query = "SELECT c.certificate_number, c.issued_at, rs.id, rs.title, rs.serial_number,
                         rs.principal_investigator, rs.co_investigators, rs.created_at,
                         u.full_name AS student_name, u.department, u.specialty
                  FROM certificates c
                  INNER JOIN research_submissions rs ON rs.id = c.submission_id
                  INNER JOIN users u ON u.id = rs.student_id
                  WHERE rs.id = ?";

        if ($isStudent) {
            $query .= " AND rs.student_id = ?";
        }

        $stmt = $db->prepare($query . " LIMIT 1");
        if ($isStudent) {
            $studentId = (int) $_SESSION['user_id'];
            $stmt->bind_param('ii', $submissionId, $studentId);
        } else {
            $stmt->bind_param('i', $submissionId);
        }
        $stmt->execute();
        $certificate = $stmt->get_result()->fetch_assoc();

        if (!$certificate) {
            http_response_code(404);
            echo 'Certificate not found.';
            exit;
        }

        // Printable certificate page for "Save as PDF" from browser.
        require __DIR__ . '/../Views/committee/certificate_print.php';
    }

    private function issueCertificate(array $submission, int $managerId) {
        if (!empty($submission['certificate_id'])) {
            return;
        }

        $db = Database::getConnection();
        $db->begin_transaction();

        try {
            $year = date('Y');
            $prefix = 'IRB-CERT-' . $year . '-';

            $seqStmt = $db->prepare("SELECT COUNT(*) AS total FROM certificates WHERE certificate_number LIKE CONCAT(?, '%')");
            $seqStmt->bind_param('s', $prefix);
            $seqStmt->execute();
            $seqRow = $seqStmt->get_result()->fetch_assoc();
            $nextSequence = ((int) ($seqRow['total'] ?? 0)) + 1;
            $certificateNumber = $prefix . str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);

            $insertStmt = $db->prepare(
                "INSERT INTO certificates (submission_id, certificate_number, issued_by)
                 VALUES (?, ?, ?)"
            );
            $insertStmt->bind_param('isi', $submission['id'], $certificateNumber, $managerId);
            $insertStmt->execute();

            $studentId = (int) $submission['student_id'];
            $logAction = 'certificate_issued';
            $details = 'تم إصدار شهادة اعتماد نهائية للبحث: ' . $submission['title'] . ' (' . $certificateNumber . ')';
            $logStmt = $db->prepare(
                "INSERT INTO system_logs (user_id, submission_id, action, details)
                 VALUES (?, ?, ?, ?)"
            );
            $logStmt->bind_param('iiss', $studentId, $submission['id'], $logAction, $details);
            $logStmt->execute();

            $db->commit();
            $_SESSION['committee_success'] = 'تم إصدار الشهادة بنجاح.';
        } catch (Exception $e) {
            $db->rollback();
            $_SESSION['committee_error'] = 'فشل إصدار الشهادة: ' . $e->getMessage();
        }
    }

    private function hasCertificatesTable(mysqli $db): bool {
        $result = $db->query("SHOW TABLES LIKE 'certificates'");
        return $result instanceof mysqli_result && $result->num_rows > 0;
    }
}
