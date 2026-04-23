<?php

require __DIR__ . '/../Repositories/UserRepository.php';

class AuthService {
    private $userRepo;

    public function __construct() {
        $this->userRepo = new UserRepository();
    }


    public function register($data) {
        if ($this->userRepo->findByEmail($data['email'])) {
            throw new Exception("هذا البريد الإلكتروني مسجل بالفعل.");
        }

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

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];

        return $user;
    }


    public function logout() {
        session_unset();
        session_destroy();
    }
}
