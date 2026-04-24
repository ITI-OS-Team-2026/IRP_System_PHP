<?php

require __DIR__ . '/../../config/database.php';

class StudentSettingsController {

    public function show() {
        AuthMiddleware::requireRole('student');
        require __DIR__ . '/../Views/student/settings.php';
    }

    public function updateProfile() {
        AuthMiddleware::requireRole('student');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        $studentId = (int) $_SESSION['user_id'];
        $fullName = trim($_POST['full_name'] ?? '');
        $phoneNumber = trim($_POST['phone_number'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $specialty = trim($_POST['specialty'] ?? '');

        if ($fullName === '' || $phoneNumber === '' || $department === '' || $specialty === '') {
            $_SESSION['settings_profile_error'] = 'يرجى تعبئة جميع الحقول المطلوبة';
            header('Location: ' . BASE_URL . '/student/settings');
            exit;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare(
            "UPDATE users
             SET full_name = ?, phone_number = ?, department = ?, specialty = ?
             WHERE id = ? AND role = 'student'
             LIMIT 1"
        );
        $stmt->bind_param('ssssi', $fullName, $phoneNumber, $department, $specialty, $studentId);
        $stmt->execute();

        if ($stmt->errno !== 0) {
            $_SESSION['settings_profile_error'] = 'حدث خطأ أثناء تحديث البيانات';
            header('Location: ' . BASE_URL . '/student/settings');
            exit;
        }

        $_SESSION['user_name'] = $fullName;
        $_SESSION['settings_profile_success'] = 'تم تحديث البيانات بنجاح';

        header('Location: ' . BASE_URL . '/student/settings');
        exit;
    }

    public function updatePassword() {
        AuthMiddleware::requireRole('student');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('Method not allowed');
        }

        $studentId = (int) $_SESSION['user_id'];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            $_SESSION['settings_password_error'] = 'يرجى تعبئة جميع حقول كلمة المرور';
            header('Location: ' . BASE_URL . '/student/settings');
            exit;
        }

        if (strlen($newPassword) < 8) {
            $_SESSION['settings_password_error'] = 'كلمة المرور الجديدة يجب ألا تقل عن 8 أحرف';
            header('Location: ' . BASE_URL . '/student/settings');
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['settings_password_error'] = 'تأكيد كلمة المرور الجديدة غير متطابق';
            header('Location: ' . BASE_URL . '/student/settings');
            exit;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare(
            "SELECT password_hash
             FROM users
             WHERE id = ? AND role = 'student'
             LIMIT 1"
        );
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            $_SESSION['settings_password_error'] = 'كلمة المرور الحالية غير صحيحة';
            header('Location: ' . BASE_URL . '/student/settings');
            exit;
        }

        $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);
        $updateStmt = $db->prepare(
            "UPDATE users
             SET password_hash = ?
             WHERE id = ? AND role = 'student'
             LIMIT 1"
        );
        $updateStmt->bind_param('si', $newPasswordHash, $studentId);
        $updateStmt->execute();

        if ($updateStmt->errno !== 0) {
            $_SESSION['settings_password_error'] = 'حدث خطأ أثناء تغيير كلمة المرور';
            header('Location: ' . BASE_URL . '/student/settings');
            exit;
        }

        $_SESSION['settings_password_success'] = 'تم تغيير كلمة المرور بنجاح';

        header('Location: ' . BASE_URL . '/student/settings');
        exit;
    }
}
