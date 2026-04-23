<?php

require __DIR__ . '/../Services/AuthService.php';

class AuthController {
    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function register() {

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            $this->jsonResponse(['error' => 'Invalid input'], 400);
            return;
        }

        try {

            $userId = $this->authService->register($input);
            $this->jsonResponse([
                'message' => 'Registration successful',
                'user_id' => $userId
            ], 201);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    public function login() {

        $json = json_decode(file_get_contents('php://input'), true);
        $data = $json ?? $_POST;

        if (empty($data['email']) || empty($data['password'])) {
            $this->jsonResponse(['error' => 'Email and password are required'], 400);
            return;
        }

        try {
            $user = $this->authService->login($data['email'], $data['password']);
            $this->jsonResponse([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role']
                ]
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 401);
        }
    }

    public function logout() {
        $this->authService->logout();
        $this->jsonResponse(['message' => 'Logged out successfully']);
    }

    private function uploadFile($file, $folder) {
        $uploadDir = __DIR__ . '/../../public/uploads/' . $folder . '/';
        
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return '/uploads/' . $folder . '/' . $fileName;
        }

        throw new Exception("Failed to upload file.");
    }

    private function jsonResponse($data, $code = 200) {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
    }
}
