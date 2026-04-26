<?php

/**
 * NotificationRepository
 *
 * Wraps system_logs as user-facing notifications.
 * A dedicated notifications table is preferred but not available,
 * so system_logs (which already tracks user-facing events) is used
 * as the backing store to avoid schema changes.
 */
class NotificationRepository {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Create a notification entry backed by system_logs.
     *
     * Expected $data keys:
     *   user_id    int   required
     *   type       string required (stored as action)
     *   message    string required (stored as details)
     *   related_id int   optional submission_id
     */
    public function create(array $data): int {
        $userId = (int) ($data['user_id'] ?? 0);
        $type   = (string) ($data['type'] ?? 'general');
        $message = (string) ($data['message'] ?? '');
        $relatedId = !empty($data['related_id']) ? (int) $data['related_id'] : null;

        if ($userId <= 0) {
            throw new Exception('User ID is required for notifications');
        }

        $stmt = $this->db->prepare(
            "INSERT INTO system_logs (user_id, submission_id, action, details)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param('iiss', $userId, $relatedId, $type, $message);
        $stmt->execute();

        if ($stmt->errno !== 0) {
            throw new Exception('Failed to create notification: ' . $stmt->error);
        }

        return (int) $this->db->insert_id;
    }

    /**
     * Fetch recent notifications for a user.
     *
     * Since system_logs lacks an is_read column, all recent entries
     * are treated as unread. Returns latest N entries (default 10).
     */
    public function getUnreadByUser(int $userId, int $limit = 10): array {
        $limit = max(1, min(50, $limit));

        $stmt = $this->db->prepare(
            "SELECT sl.id,
                    sl.action AS type,
                    sl.details AS message,
                    sl.created_at,
                    sl.submission_id AS related_id,
                    rs.title AS related_title
             FROM system_logs sl
             LEFT JOIN research_submissions rs ON rs.id = sl.submission_id
             WHERE sl.user_id = ?
             ORDER BY sl.created_at DESC
             LIMIT ?"
        );
        $stmt->bind_param('ii', $userId, $limit);
        $stmt->execute();

        $result = $stmt->get_result();
        $notifications = [];
        while ($row = $result->fetch_assoc()) {
            $notifications[] = $row;
        }
        return $notifications;
    }

    /**
     * Count unread (recent) notifications for a user.
     */
    public function countUnread(int $userId): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS cnt
             FROM system_logs
             WHERE user_id = ?"
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return (int) ($row['cnt'] ?? 0);
    }
}
