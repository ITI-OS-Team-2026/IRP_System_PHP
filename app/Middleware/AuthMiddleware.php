<?php

class AuthMiddleware {

    public static function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }


    public static function requireLogin() {
        if (!self::isAuthenticated()) {
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        // Check if user is active (Status from session)
        if (isset($_SESSION['user_is_active']) && !((bool)$_SESSION['user_is_active'])) {
            // Check if we are not already on the pending page to avoid loops
            $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $pendingPath = parse_url(BASE_URL . '/pending-approval', PHP_URL_PATH);
            
            if ($currentPath !== $pendingPath) {
                header("Location: " . BASE_URL . "/pending-approval");
                exit;
            }
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
