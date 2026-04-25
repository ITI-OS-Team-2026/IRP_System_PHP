<?php

class StatusHelper {
    public static function translateStatus($status) {
        return [
            'submitted' => 'تم التقديم',
            'admin_reviewed' => 'تمت مراجعة الإدارة',
            'initial_paid' => 'تم سداد الرسوم الأولية',
            'sample_sized' => 'تم حساب حجم العينة',
            'fully_paid' => 'تم السداد بالكامل',
            'under_review' => 'قيد المراجعة',
            'revision_requested' => 'مطلوب تعديل',
            'approved' => 'تمت الموافقة',
            'rejected' => 'مرفوض',
        ][$status] ?? $status;
    }
}
