<?php

class PaymentHelpers {
    public static function formatAmount($amount) {
        return number_format((float) $amount, 2) . ' ج.م';
    }

    public static function formatDate($date) {
        if (empty($date)) {
            return 'غير متوفر';
        }

        return date('d/m/Y H:i', strtotime($date));
    }

    public static function getStatusLabel($status) {
        $labels = [
            'pending' => 'قيد الانتظار',
            'completed' => 'مكتملة',
            'failed' => 'فشلت',
            'cancelled' => 'ملغاة',
        ];

        return $labels[$status] ?? $status;
    }

    public static function getStatusBadgeClass($status) {
        $classes = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-slate-100 text-slate-700',
        ];

        return $classes[$status] ?? 'bg-slate-100 text-slate-700';
    }
}
