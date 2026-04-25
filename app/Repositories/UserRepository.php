<?php

require __DIR__ . '/../../config/database.php';

class UserRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByEmail($email) {
        $email = $this->toLower(trim((string) $email));
        $stmt = $this->db->prepare("SELECT * FROM users WHERE LOWER(email) = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result ? $result->fetch_assoc() : null;
    }

    public function findByFullName($fullName) {
        $normalized = $this->normalizeFullName($fullName);
        if ($normalized === '') {
            return null;
        }

        $stmt = $this->db->prepare("SELECT id, full_name, email, role FROM users WHERE LOWER(TRIM(full_name)) = ? LIMIT 1");
        $stmt->bind_param('s', $normalized);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result ? $result->fetch_assoc() : null;
    }

    public function create($data) {
        $full_name = $this->db->real_escape_string($data['full_name']);
        $email = $this->db->real_escape_string($data['email']);
        $password_hash = password_hash($data['password'], PASSWORD_BCRYPT);
        $phone_number = $this->db->real_escape_string($data['phone_number']);
        
        $department = isset($data['department']) ? "'" . $this->db->real_escape_string($data['department']) . "'" : "NULL";
        $specialty = isset($data['specialty']) ? "'" . $this->db->real_escape_string($data['specialty']) . "'" : "NULL";
        $id_front = isset($data['id_front_path']) ? "'" . $this->db->real_escape_string($data['id_front_path']) . "'" : "NULL";
        $id_back = isset($data['id_back_path']) ? "'" . $this->db->real_escape_string($data['id_back_path']) . "'" : "NULL";
        
        $role = $this->db->real_escape_string($data['role'] ?? 'student');

        $sql = "INSERT INTO users (full_name, email, password_hash, phone_number, department, specialty, id_front_path, id_back_path, role) 
                VALUES ('$full_name', '$email', '$password_hash', '$phone_number', $department, $specialty, $id_front, $id_back, '$role')";

        if ($this->db->query($sql)) {
            return $this->db->insert_id;
        } else {
            // Check for specific common errors
            $error = $this->db->error;
            if (strpos($error, 'Unknown column') !== false) {
                throw new Exception("حدث خطأ في بنية قاعدة البيانات: بعض الحقول مفقودة. يرجى تشغيل ملف migration.");
            }
            throw new Exception("حدث خطأ أثناء حفظ البيانات: " . $error);
        }
    }

    public function findById($id) {
        $id = (int)$id;
        $sql = "SELECT * FROM users WHERE id = $id LIMIT 1";
        $result = $this->db->query($sql);
        return $result->fetch_assoc();
    }

    private function normalizeFullName($fullName) {
        $fullName = preg_replace('/\s+/u', ' ', trim((string) $fullName));
        return $this->toLower($fullName);
    }

    private function toLower($value) {
        return function_exists('mb_strtolower')
            ? mb_strtolower($value, 'UTF-8')
            : strtolower($value);
    }
}
