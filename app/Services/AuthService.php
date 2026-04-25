<?php

require __DIR__ . '/../Repositories/UserRepository.php';

class AuthService {
    private $userRepo;

    public function __construct() {
        $this->userRepo = new UserRepository();
    }


    public function register($data) {
        $fullName = preg_replace('/\s+/u', ' ', trim((string) ($data['full_name'] ?? '')));
        $emailRaw = trim((string) ($data['email'] ?? ''));
        $email = function_exists('mb_strtolower') ? mb_strtolower($emailRaw, 'UTF-8') : strtolower($emailRaw);
        $password = (string) ($data['password'] ?? '');

        if ($fullName === '') {
            throw new Exception("الاسم الكامل مطلوب.");
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("البريد الإلكتروني غير صالح.");
        }

        if (!$this->isStrongPassword($password)) {
            throw new Exception("كلمة المرور يجب أن تحتوي على 8 أحرف على الأقل وحرف كبير وحرف صغير ورقم ورمز خاص.");
        }

        if ($this->userRepo->findByFullName($fullName)) {
            throw new Exception("الاسم الكامل مستخدم بالفعل.");
        }

        if ($this->userRepo->findByEmail($email)) {
            throw new Exception("هذا البريد الإلكتروني مسجل بالفعل.");
        }

        $data['full_name'] = $fullName;
        $data['email'] = $email;

        $userId = $this->userRepo->create($data);
        if (!$userId) {
            throw new Exception("Registration failed.");
        }

        return $userId;
    }


    public function login($email, $password) {
        $user = $this->userRepo->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            throw new Exception("البريد الإلكتروني أو كلمة المرور غير صحيحة.");
        }

        return $user;
    }

    private function isStrongPassword($password) {
        if (strlen($password) < 8) {
            return false;
        }

        return preg_match('/[A-Z]/', $password)
            && preg_match('/[a-z]/', $password)
            && preg_match('/[0-9]/', $password)
            && preg_match('/[^A-Za-z0-9]/', $password);
    }


    public function logout() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 3600,
                $params['path'] ?? '/',
                $params['domain'] ?? '',
                (bool) ($params['secure'] ?? false),
                (bool) ($params['httponly'] ?? true)
            );
        }

        session_unset();
        session_destroy();
    }
}
