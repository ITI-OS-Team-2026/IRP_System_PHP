<?php

require __DIR__ . '/../../config/database.php';

class UserRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findByEmail($email) {
        $email = $this->db->real_escape_string($email);
        $result = $this->db->query("SELECT * FROM users WHERE email = '$email' LIMIT 1");
        return $result->fetch_assoc();
    }

    public function create($data) {
        $full_name = $this->db->real_escape_string($data['full_name']);
        $email = $this->db->real_escape_string($data['email']);
        $password_hash = password_hash($data['password'], PASSWORD_BCRYPT);
        $national_id = $this->db->real_escape_string($data['national_id']);
        $phone_number = $this->db->real_escape_string($data['phone_number']);
        $department = $this->db->real_escape_string($data['department']);
        $faculty = $this->db->real_escape_string($data['faculty']);
        $role = $this->db->real_escape_string($data['role']);

        $sql = "INSERT INTO users (full_name, email, password_hash, national_id, phone_number, department, faculty, role) 
                VALUES ('$full_name', '$email', '$password_hash', '$national_id', '$phone_number', '$department', '$faculty', '$role')";

        if ($this->db->query($sql)) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function findById($id) {
        $id = (int)$id;
        $result = $this->db->query("SELECT * FROM users WHERE id = $id LIMIT 1");
        return $result->fetch_assoc();
    }
}
