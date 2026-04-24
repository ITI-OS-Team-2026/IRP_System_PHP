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
        $sql = "SELECT COUNT(*) AS total FROM research_submissions WHERE status = 'admin_reviewed'";
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

    private function fetchOne($sql) {
        $result = $this->db->query($sql);
        if (!$result) {
            throw new Exception('Failed to fetch admin dashboard data: ' . $this->db->error);
        }

        return $result->fetch_assoc() ?: ['total' => 0];
    }
}
