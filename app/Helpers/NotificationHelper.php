<?php

require_once __DIR__ . '/../Repositories/NotificationRepository.php';

class NotificationHelper {
    public static function handleStatusChange($db, $submissionId, $oldStatus, $newStatus, $event = null) {
        // Prevent duplicate notification if neither status nor event changed
        if ($oldStatus === $newStatus && $event === null) {
            return;
        }

        // Single source of truth for all messages
        $messages = [
            'status' => [
                'admin_reviewed' => 'تمت مراجعة طلبك من الإدارة',
                'initial_paid' => 'تم استلام رسوم التقديم',
                'sample_sized' => 'تم تحديد حجم العينة',
                'fully_paid' => 'تم سداد جميع الرسوم',
                'under_review' => 'طلبك قيد المراجعة الآن',
                'revision_requested' => 'تم طلب تعديل على البحث الخاص بك',
                'approved' => 'تمت الموافقة على البحث الخاص بك',
                'rejected' => 'تم رفض البحث الخاص بك',
            ],
            'event' => [
                'review_started' => 'تم إرسال البحث إلى المراجعة',
                'revision_submitted' => 'تم إعادة إرسال البحث بعد التعديل',
                'payment_failed' => 'فشلت عملية الدفع الخاصة بالبحث'
            ]
        ];

        $message = '';
        $type = 'submission_status';

        if ($event !== null && isset($messages['event'][$event])) {
            $message = $messages['event'][$event];
            $type = ($event === 'payment_failed') ? 'payment_failed' : 'submission_event';
        } elseif ($oldStatus !== $newStatus && isset($messages['status'][$newStatus])) {
            $message = $messages['status'][$newStatus];
        } else {
            return; // No valid message found
        }

        $stmt = $db->prepare("SELECT student_id, serial_number, title FROM research_submissions WHERE id = ?");
        $stmt->bind_param('i', $submissionId);
        $stmt->execute();
        $sub = $stmt->get_result()->fetch_assoc();
        if (!$sub) {
            return;
        }

        // Add Context
        $context = [];
        if (!empty($sub['serial_number'])) {
            $context[] = 'رقم البحث: ' . $sub['serial_number'];
        }
        if (!empty($sub['title'])) {
            $context[] = $sub['title'];
        }
        
        if (!empty($context)) {
            $message .= ' - ' . implode(' - ', $context);
        }

        $priorityMap = [
            'approved' => 'success',
            'rejected' => 'danger',
            'revision_requested' => 'warning',
            'default' => 'info'
        ];
        $eventOrStatus = $event ?? $newStatus;
        $priority = $priorityMap[$eventOrStatus] ?? $priorityMap['default'];

        $notifRepo = new NotificationRepository($db);
        try {
            // Idempotency / Duplicate Check
            if ($notifRepo->existsRecent($sub['student_id'], $type, $eventOrStatus, $submissionId, 5)) {
                return; // Skip, already notified recently
            }

            $notifRepo->create([
                'user_id' => $sub['student_id'],
                'type' => $type,
                'message' => $message,
                'related_id' => $submissionId,
                'priority' => $priority
            ]);
        } catch (Exception $e) {
            error_log('Notification error: ' . $e->getMessage());
        }
    }
}
