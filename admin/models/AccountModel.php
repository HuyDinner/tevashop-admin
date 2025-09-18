<?php

// Đường dẫn tương đối từ file model đến config
// Đảm bảo đường dẫn này đúng với cấu trúc dự án của bạn
require_once CONFIG_PATH . 'db_config.php';

class AccountModel {
    private $conn;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function getAdminAccount($adminId) {
        $query = "SELECT id, username, email FROM admin_users WHERE id = :admin_id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":admin_id", $adminId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function verifyAdminPassword($adminId, $password) {
        $query = "SELECT mat_khau FROM admin WHERE id_admin = :admin_id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":admin_id", $adminId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password, $row['mat_khau'])) {
            return true;
        }
        return false;
    }

    public function updateAdminAccount($adminId, $data) {
        $query = "UPDATE admin SET ten_dang_nhap = :username, email = :email";
        if (isset($data['password'])) {
            $query .= ", mat_khau = :password";
        }
        $query .= " WHERE id_admin = :admin_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $data['username']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":admin_id", $adminId);

        if (isset($data['password'])) {
            $stmt->bindParam(":password", $data['password']);
        }

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Lỗi khi cập nhật tài khoản admin: " . $e->getMessage());
            return false;
        }
    }
}
