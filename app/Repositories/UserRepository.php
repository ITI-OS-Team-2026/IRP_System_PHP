<?php

require __DIR__ . '/../../config/database.php';

class UserRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }


    public function findByEmail($email) {
        $email = $this->db->real_escape_string($email);
        $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        
        $result = $this->db->query($sql);
        return $result->fetch_assoc();
    }


    public function create($data) {
        $full_name = $this->db->real_escape_string($data['full_name']);
        $email = $this->db->real_escape_string($data['email']);
        $password_hash = password_hash($data['password'], PASSWORD_BCRYPT);
        $phone_number = $this->db->real_escape_string($data['phone_number']);
        $role = $this->db->real_escape_string($data['role'] ?? 'student');

        $sql = "INSERT INTO users (full_name, email, password_hash, phone_number, role) 
                VALUES ('$full_name', '$email', '$password_hash', '$phone_number', '$role')";

        if ($this->db->query($sql)) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function findById($id) {
        $id = (int)$id;
        $sql = "SELECT * FROM users WHERE id = $id LIMIT 1";
        $result = $this->db->query($sql);
        return $result->fetch_assoc();
    }
}
