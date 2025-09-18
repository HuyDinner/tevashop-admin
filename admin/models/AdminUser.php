<?php
// admin/models/AdminUser.php

class AdminUser {
    private $db;
    private $table_name = "admin_users"; // Đảm bảo tên bảng này khớp với CSDL của bạn

    public function __construct($db) {
        $this->db = $db;
    }

    // Đăng ký admin mới
    public function register($username, $email, $password) {
        // Kiểm tra xem username hoặc email đã tồn tại chưa
        if ($this->getUserByUsername($username) || $this->getUserByEmail($email)) {
            return false; // User hoặc email đã tồn tại
        }

        // Hash mật khẩu trước khi lưu vào cơ sở dữ liệu
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $this->db->prepare("INSERT INTO " . $this->table_name . " (username, email, password) VALUES (:username, :email, :password)");
            // Sử dụng bindParam cho PDO
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $hashedPassword);

            if ($stmt->execute()) {
                return true;
            }
        } catch (PDOException $e) {
            error_log("PDO Error (register AdminUser): " . $e->getMessage());
        }
        return false;
    }

    // Đăng nhập admin
    public function login($usernameOrEmail, $password) {
        // Tìm người dùng theo username hoặc email
        $user = $this->getUserByUsername($usernameOrEmail);
        if (!$user) {
            $user = $this->getUserByEmail($usernameOrEmail);
        }

        if ($user && password_verify($password, $user['password'])) {
            return $user; // Trả về thông tin người dùng nếu đăng nhập thành công
        }
        return false; // Đăng nhập thất bại
    }

    // Lấy thông tin người dùng theo username
    public function getUserByUsername($username) {
        try {
            $stmt = $this->db->prepare("SELECT id, username, email, password FROM " . $this->table_name . " WHERE username = :username LIMIT 1");
            // Sử dụng bindParam cho PDO
            $stmt->bindParam(":username", $username);
            $stmt->execute();
            // Sử dụng fetch(PDO::FETCH_ASSOC) để lấy kết quả
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("PDO Error (getUserByUsername AdminUser): " . $e->getMessage());
            return false;
        }
    }

    // Lấy thông tin người dùng theo email
    public function getUserByEmail($email) {
        try {
            $stmt = $this->db->prepare("SELECT id, username, email, password FROM " . $this->table_name . " WHERE email = :email LIMIT 1");
            // Sử dụng bindParam cho PDO
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            // Sử dụng fetch(PDO::FETCH_ASSOC) để lấy kết quả
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("PDO Error (getUserByEmail AdminUser): " . $e->getMessage());
            return false;
        }
    }
}
