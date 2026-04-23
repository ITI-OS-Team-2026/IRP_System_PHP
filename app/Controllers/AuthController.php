<?php

require __DIR__ . '/../Services/AuthService.php';

class AuthController {
    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function register() {
        try {
            // Handle multipart/form-data for files
            $data = $_POST;

            // Secure ID image uploads
            if (isset($_FILES['id_front']) && $_FILES['id_front']['error'] === UPLOAD_ERR_OK) {
                $data['id_front_path'] = $this->uploadFile($_FILES['id_front'], 'ids');
            }
            if (isset($_FILES['id_back']) && $_FILES['id_back']['error'] === UPLOAD_ERR_OK) {
                $data['id_back_path'] = $this->uploadFile($_FILES['id_back'], 'ids');
            }

            if (empty($data['email']) || empty($data['password'])) {
                $this->jsonResponse(['error' => 'البريد الإلكتروني وكلمة المرور مطلوبان'], 400);
                return;
            }

            $userId = $this->authService->register($data);
            $this->jsonResponse([
                'message' => 'تم التسجيل بنجاح',
                'user_id' => $userId
            ], 201);
        } catch (Throwable $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    public function login() {
        $json = json_decode(file_get_contents('php://input'), true);
        $data = $json ?? $_POST;

        if (empty($data['email']) || empty($data['password'])) {
            $this->jsonResponse(['error' => 'البريد الإلكتروني وكلمة المرور مطلوبان'], 400);
            return;
        }

        try {
            $user = $this->authService->login($data['email'], $data['password']);

            if (!(bool) $user['is_active']) {
                $this->jsonResponse([
                    'error' => 'حسابك قيد المراجعة من قبل الإدارة. سيتم تفعيل الحساب بعد الموافقة.',
                    'redirect_to' => '/pending-approval'
                ], 403);
                return;
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];

            $this->jsonResponse([
                'message' => 'تم تسجيل الدخول بنجاح',
                'user' => [
                    'id' => $user['id'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role'],
                    'is_active' => (bool) $user['is_active']
                ]
            ]);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 401);
        }
    }

    public function logout() {
        $this->authService->logout();
        $this->jsonResponse(['message' => 'تم تسجيل الخروج بنجاح']);
    }

    private function uploadFile($file, $folder) {
        $uploadDir = __DIR__ . '/../../public/uploads/' . $folder . '/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // 1. Check for errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("فشل في رفع الملف.");
        }

        // 2. Validate File Size (Max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new Exception("حجم الملف كبير جداً. الحد الأقصى هو 5 ميجابايت.");
        }

        // 3. Validate File Type (MIME Type)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $file['tmp_name']);

        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception("نوع الملف غير مسموح. يرجى رفع صورة (JPG, PNG).");
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return '/uploads/' . $folder . '/' . $fileName;
        }
        throw new Exception("فشل في حفظ الملف على الخادم.");
    }

    private function jsonResponse($data, $code = 200) {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
    }
}
