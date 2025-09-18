<?php
// admin/models/Customers.php

class Customer {
    private $pdo; // Đây sẽ là đối tượng PDO
    private $table_name = "KhachHang";

    public function __construct(PDO $db) {
        $this->pdo = $db; // Nhận đối tượng PDO
    }

    // Lấy tất cả khách hàng
    public function getAllCustomers() {
        $query = "SELECT Id, TenKhachHang, Email, SoDienThoai, DiaChi, NgayDangKy, TrangThai FROM " . $this->table_name;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Sử dụng fetchAll của PDO
    }

    // Lấy khách hàng theo ID
    public function getCustomerById($id) {
        $query = "SELECT Id, TenKhachHang, Email, SoDienThoai, DiaChi, NgayDangKy, TrangThai FROM " . $this->table_name . " WHERE Id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Liên kết tham số kiểu INT
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Lấy một hàng
    }

    // Thêm khách hàng mới
    public function addCustomer($data) {
        $hashedPassword = password_hash($data['Password'], PASSWORD_DEFAULT);

        $query = "INSERT INTO " . $this->table_name . " (TenKhachHang, Email, Password, SoDienThoai, DiaChi, TrangThai) VALUES (:tenKhachHang, :email, :password, :soDienThoai, :diaChi, :trangThai)";
        $stmt = $this->pdo->prepare($query);
        
        $tenKhachHang = $data['TenKhachHang'] ?? '';
        $email = $data['Email'] ?? '';
        $soDienThoai = $data['SoDienThoai'] ?? '';
        $diaChi = $data['DiaChi'] ?? '';
        $trangThai = $data['TrangThai'] ?? 0;

        $stmt->bindParam(':tenKhachHang', $tenKhachHang, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':soDienThoai', $soDienThoai, PDO::PARAM_STR);
        $stmt->bindParam(':diaChi', $diaChi, PDO::PARAM_STR);
        $stmt->bindParam(':trangThai', $trangThai, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $this->pdo->lastInsertId(); // Trả về ID của hàng cuối cùng được chèn
        }
        error_log("PDO Error (addCustomer): " . implode(" | ", $stmt->errorInfo()));
        return false;
    }

    // Cập nhật thông tin khách hàng
    public function updateCustomer($id, $data) {
        $query = "UPDATE " . $this->table_name . " SET TenKhachHang = :tenKhachHang, Email = :email, SoDienThoai = :soDienThoai, DiaChi = :diaChi, TrangThai = :trangThai ";
        $params = [
            ':tenKhachHang' => $data['TenKhachHang'] ?? '',
            ':email' => $data['Email'] ?? '',
            ':soDienThoai' => $data['SoDienThoai'] ?? '',
            ':diaChi' => $data['DiaChi'] ?? '',
            ':trangThai' => $data['TrangThai'] ?? 0
        ];

        if (!empty($data['Password'])) {
            $hashedPassword = password_hash($data['Password'], PASSWORD_DEFAULT);
            $query .= ", Password = :password ";
            $params[':password'] = $hashedPassword;
        }
        
        $query .= " WHERE Id = :id";
        $params[':id'] = $id;

        $stmt = $this->pdo->prepare($query);
        
        // Liên kết các tham số
        foreach ($params as $key => &$val) {
            if ($key === ':trangThai' || $key === ':id') {
                $stmt->bindParam($key, $val, PDO::PARAM_INT);
            } elseif ($key === ':password') {
                 $stmt->bindParam($key, $val, PDO::PARAM_STR);
            } else {
                $stmt->bindParam($key, $val, PDO::PARAM_STR);
            }
        }
        
        if ($stmt->execute()) {
            return $stmt->rowCount(); // Trả về số hàng bị ảnh hưởng
        }
        error_log("PDO Error (updateCustomer): " . implode(" | ", $stmt->errorInfo()));
        return false;
    }

    // Xóa khách hàng
    public function deleteCustomer($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE Id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
        
        if ($stmt->execute()) {
            return $stmt->rowCount(); // Trả về số hàng bị ảnh hưởng
        }
        error_log("PDO Error (deleteCustomer): " . implode(" | ", $stmt->errorInfo()));
        return false;
    }
}
?>
