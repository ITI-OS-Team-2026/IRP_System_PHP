<?php

class AuthMiddleware {

    public static function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }


    public static function requireLogin() {
        if (!self::isAuthenticated()) {
            header("Location: /login");
            exit;
        }
    }

    public static function requireRole($allowedRoles) {
        self::requireLogin();

        $userRole = $_SESSION['user_role'] ?? '';
        $allowedRoles = (array) $allowedRoles;

        if (!in_array($userRole, $allowedRoles)) {
            http_response_code(403);
            die("<h1>403 Unauthorized</h1><p>You do not have permission to access this page.</p>");
        }
    }

    public static function hasRole($roles) {
        if (!self::isAuthenticated()) return false;
        $roles = (array) $roles;
        return in_array($_SESSION['user_role'], $roles);
    }

    public static function user() {
        if (!self::isAuthenticated()) return null;
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'role' => $_SESSION['user_role']
        ];
    }
}
