<?php

require __DIR__ . '/../../config/database.php';

class AdminRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function countPendingActivations() {
        $sql = "SELECT COUNT(*) AS total FROM users WHERE role = 'student' AND is_active = 0";
        return (int) $this->fetchOne($sql)['total'];
    }

    public function countSerialQueue() {
        $sql = "SELECT COUNT(*) AS total FROM research_submissions WHERE status = 'submitted' AND serial_number IS NULL";
        return (int) $this->fetchOne($sql)['total'];
    }

    public function countReadyForReviewerAssignment() {
        $sql = "SELECT COUNT(*) AS total FROM research_submissions WHERE status = 'fully_paid' AND serial_number IS NOT NULL";
        return (int) $this->fetchOne($sql)['total'];
    }

    public function countActiveUsers() {
        $sql = "SELECT COUNT(*) AS total FROM users WHERE is_active = 1";
        return (int) $this->fetchOne($sql)['total'];
    }

    public function countCompletedSubmissions() {
        $sql = "SELECT COUNT(*) AS total FROM research_submissions WHERE status IN ('approved', 'rejected')";
        return (int) $this->fetchOne($sql)['total'];
    }

    public function countTotalSubmissions() {
        $sql = "SELECT COUNT(*) AS total FROM research_submissions";
        return (int) $this->fetchOne($sql)['total'];
    }

    public function averageReviewTimeDays() {
        $sql = "SELECT AVG(TIMESTAMPDIFF(DAY, rs.created_at, rv.reviewed_at)) AS average_days
                FROM reviews rv
                INNER JOIN research_submissions rs ON rs.id = rv.submission_id
                WHERE rv.reviewed_at IS NOT NULL";
        $row = $this->fetchOne($sql);
        return $row['average_days'] !== null ? (float) $row['average_days'] : null;
    }

    public function getRecentSystemActivity($limit = 3) {
        $limit = (int) $limit;
        $sql = "SELECT sl.action, sl.details, sl.created_at, u.full_name, rs.serial_number
                FROM system_logs sl
                LEFT JOIN users u ON u.id = sl.user_id
                LEFT JOIN research_submissions rs ON rs.id = sl.submission_id
                ORDER BY sl.created_at DESC
                LIMIT $limit";

        $result = $this->db->query($sql);
        $items = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }

        return $items;
    }

    public function getDashboardSummary() {
        return [
            'pendingActivations' => $this->countPendingActivations(),
            'serialQueue' => $this->countSerialQueue(),
            'readyForReviewerAssignment' => $this->countReadyForReviewerAssignment(),
            'activeUsers' => $this->countActiveUsers(),
            'completedSubmissions' => $this->countCompletedSubmissions(),
            'totalSubmissions' => $this->countTotalSubmissions(),
            'averageReviewTimeDays' => $this->averageReviewTimeDays(),
            'recentActivity' => $this->getRecentSystemActivity(3),
        ];
    }

    public function getPendingStudentUsers($limit = 20) {
        $limit = (int) $limit;
        $sql = "SELECT
                    u.id,
                    u.full_name,
                    u.department,
                    u.specialty,
                    u.created_at,
                    u.is_active,
                    (
                        SELECT COUNT(*)
                        FROM research_submissions rs
                        WHERE rs.student_id = u.id
                    ) AS submission_count,
                    (
                        SELECT COUNT(*)
                        FROM research_documents rd
                        INNER JOIN research_submissions rs ON rs.id = rd.submission_id
                        WHERE rs.student_id = u.id
                    ) AS document_count
                FROM users u
                WHERE u.role = 'student' AND u.is_active = 0
                ORDER BY u.created_at DESC
                LIMIT $limit";

        $result = $this->db->query($sql);
        $users = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }

        return $users;
    }

    public function activateStudentUser($userId) {
        $userId = (int) $userId;
        $stmt = $this->db->prepare("UPDATE users SET is_active = 1 WHERE id = ? AND role = 'student' LIMIT 1");
        $stmt->bind_param('i', $userId);
        $stmt->execute();

        return $stmt->affected_rows > 0;
    }

    public function logAction($action, $details, $userId = null, $submissionId = null) {
        $stmt = $this->db->prepare("INSERT INTO system_logs (user_id, submission_id, action, details) VALUES (?, ?, ?, ?)");
        $userId = $userId !== null ? (int) $userId : null;
        $submissionId = $submissionId !== null ? (int) $submissionId : null;
        $stmt->bind_param('iiss', $userId, $submissionId, $action, $details);
        $stmt->execute();

        return $stmt->insert_id;
    }

    public function getInitialPreviewQueueSubmissions($limit = 25) {
        $limit = (int) $limit;
        $sql = "SELECT
                    rs.id,
                    rs.title,
                    rs.created_at,
                    u.full_name,
                    u.department,
                    u.specialty
                FROM research_submissions rs
                INNER JOIN users u ON u.id = rs.student_id
                WHERE rs.status = 'submitted' AND rs.serial_number IS NULL
                ORDER BY rs.created_at ASC
                LIMIT $limit";

        $result = $this->db->query($sql);
        if (!$result) {
            throw new Exception('Failed to fetch initial preview queue: ' . $this->db->error);
        }

        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }

        return $items;
    }

    public function serialNumberExists($serialNumber) {
        $stmt = $this->db->prepare("SELECT id FROM research_submissions WHERE serial_number = ? LIMIT 1");
        $stmt->bind_param('s', $serialNumber);
        $stmt->execute();

        $result = $stmt->get_result();
        return (bool) $result->fetch_assoc();
    }

    public function getMaxSerialSequenceByYear($year) {
        $prefix = 'IRB-' . $year . '-%';
        $stmt = $this->db->prepare(
            "SELECT MAX(CAST(SUBSTRING_INDEX(serial_number, '-', -1) AS UNSIGNED)) AS max_seq
             FROM research_submissions
             WHERE serial_number LIKE ?"
        );
        $stmt->bind_param('s', $prefix);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return (int) ($row['max_seq'] ?? 0);
    }

    public function assignSerialNumberToSubmission($submissionId, $serialNumber) {
        $submissionId = (int) $submissionId;
        $stmt = $this->db->prepare(
            "UPDATE research_submissions
             SET serial_number = ?, status = 'admin_reviewed'
             WHERE id = ? AND status = 'submitted' AND serial_number IS NULL
             LIMIT 1"
        );
        $stmt->bind_param('si', $serialNumber, $submissionId);
        $stmt->execute();

        return $stmt->affected_rows > 0;
    }

    public function getReviewerAssignmentSubmissions($search = '', $limit = 30, $offset = 0) {
        $limit = (int) $limit;
        $offset = (int) $offset;
        $search = trim((string) $search);

        $where = "rs.status = 'fully_paid' AND rs.serial_number IS NOT NULL";
        $types = '';
        $params = [];

        if ($search !== '') {
            $where .= " AND (rs.serial_number LIKE ? OR rs.title LIKE ? OR u.full_name LIKE ?)";
            $like = '%' . $search . '%';
            $types .= 'sss';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $sql = "SELECT
                    rs.id,
                    rs.serial_number,
                    rs.title,
                    rs.status,
                    rv.reviewer_id,
                    ru.full_name AS reviewer_name,
                    ru.specialty AS reviewer_specialty
                FROM research_submissions rs
                INNER JOIN users u ON u.id = rs.student_id
                LEFT JOIN reviews rv ON rv.submission_id = rs.id
                LEFT JOIN users ru ON ru.id = rv.reviewer_id
                WHERE $where
                ORDER BY rs.created_at DESC
                LIMIT $limit OFFSET $offset";

        $stmt = $this->db->prepare($sql);
        if ($types !== '') {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }

        return $items;
    }

    public function countReviewerAssignmentSubmissions($search = '') {
        $search = trim((string) $search);

        $where = "rs.status = 'fully_paid' AND rs.serial_number IS NOT NULL";
        $types = '';
        $params = [];

        if ($search !== '') {
            $where .= " AND (rs.serial_number LIKE ? OR rs.title LIKE ? OR u.full_name LIKE ?)";
            $like = '%' . $search . '%';
            $types .= 'sss';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $sql = "SELECT COUNT(*) AS total
                FROM research_submissions rs
                INNER JOIN users u ON u.id = rs.student_id
                WHERE $where";

        $stmt = $this->db->prepare($sql);
        if ($types !== '') {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return (int) ($row['total'] ?? 0);
    }

    public function getAssignedReviewerIdForSubmission($submissionId) {
        $submissionId = (int) $submissionId;
        $stmt = $this->db->prepare("SELECT reviewer_id FROM reviews WHERE submission_id = ? LIMIT 1");
        $stmt->bind_param('i', $submissionId);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row ? (int) $row['reviewer_id'] : null;
    }

    public function getReviewers() {
        $sql = "SELECT id, full_name, specialty
                FROM users
                WHERE role = 'reviewer' AND is_active = 1
                ORDER BY full_name ASC";

        $result = $this->db->query($sql);
        if (!$result) {
            throw new Exception('Failed to fetch reviewers: ' . $this->db->error);
        }

        $reviewers = [];
        while ($row = $result->fetch_assoc()) {
            $reviewers[] = $row;
        }

        return $reviewers;
    }

    public function submissionExistsForReviewerAssignment($submissionId) {
        $submissionId = (int) $submissionId;
        $stmt = $this->db->prepare(
            "SELECT id FROM research_submissions
             WHERE id = ? AND status = 'fully_paid' AND serial_number IS NOT NULL
             LIMIT 1"
        );
        $stmt->bind_param('i', $submissionId);
        $stmt->execute();

        $result = $stmt->get_result();
        return (bool) $result->fetch_assoc();
    }

    public function reviewerExists($reviewerId) {
        $reviewerId = (int) $reviewerId;
        $stmt = $this->db->prepare(
            "SELECT id FROM users WHERE id = ? AND role = 'reviewer' AND is_active = 1 LIMIT 1"
        );
        $stmt->bind_param('i', $reviewerId);
        $stmt->execute();

        $result = $stmt->get_result();
        return (bool) $result->fetch_assoc();
    }

    public function upsertReviewerAssignment($submissionId, $reviewerId) {
        $submissionId = (int) $submissionId;
        $reviewerId = (int) $reviewerId;

        $stmt = $this->db->prepare("SELECT id FROM reviews WHERE submission_id = ? LIMIT 1");
        $stmt->bind_param('i', $submissionId);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();

        if ($existing) {
            $reviewId = (int) $existing['id'];
            $update = $this->db->prepare(
                "UPDATE reviews
                 SET reviewer_id = ?, review_status = 'pending', assigned_at = CURRENT_TIMESTAMP
                 WHERE id = ?
                 LIMIT 1"
            );
            $update->bind_param('ii', $reviewerId, $reviewId);
            $update->execute();

            return 'updated';
        }

        $insert = $this->db->prepare(
            "INSERT INTO reviews (submission_id, reviewer_id, review_status)
             VALUES (?, ?, 'pending')"
        );
        $insert->bind_param('ii', $submissionId, $reviewerId);
        $insert->execute();

        return 'created';
    }

    private function fetchOne($sql) {
        $result = $this->db->query($sql);
        if (!$result) {
            throw new Exception('Failed to fetch admin dashboard data: ' . $this->db->error);
        }

        return $result->fetch_assoc() ?: ['total' => 0];
    }
}
