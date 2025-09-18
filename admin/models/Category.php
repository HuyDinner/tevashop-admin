<?php
// admin/models/Category.php

class Category {
    private $pdo; // Đây sẽ là đối tượng PDO

    public function __construct(PDO $db) {
        $this->pdo = $db; // Nhận đối tượng PDO
    }

    public function getAllCategories() {
        $sql = "SELECT Id, TenDanhMuc, TrangThai FROM DanhMuc"; 
        $stmt = $this->pdo->prepare($sql); // Sử dụng PDO prepare
        $stmt->execute(); // Thực thi truy vấn
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Lấy tất cả kết quả dưới dạng mảng kết hợp
    }

    public function getCategoryById($id) {
        $query = "SELECT Id, TenDanhMuc, TrangThai FROM DanhMuc WHERE Id = :id LIMIT 1"; // Sử dụng placeholder đã đặt tên
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Liên kết tham số kiểu INT
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Lấy một hàng dưới dạng mảng kết hợp
    }

    public function addCategory($data) {
        $query = "INSERT INTO DanhMuc (TenDanhMuc, TrangThai) VALUES (:tenDanhMuc, :trangThai)";
        $stmt = $this->pdo->prepare($query);
        
        $tenDanhMuc = $data['name'] ?? ''; // Lấy dữ liệu từ mảng $data
        $trangThai = isset($data['status']) ? (int)$data['status'] : 0; // Đảm bảo trạng thái là số nguyên

        $stmt->bindParam(':tenDanhMuc', $tenDanhMuc, PDO::PARAM_STR); // Liên kết kiểu chuỗi
        $stmt->bindParam(':trangThai', $trangThai, PDO::PARAM_INT); // Liên kết kiểu số nguyên
        
        // PDO execute trả về true/false
        if ($stmt->execute()) {
            return $this->pdo->lastInsertId(); // Trả về ID của hàng cuối cùng được chèn (nếu cần)
        }
        
        // Log lỗi chi tiết hơn từ PDO
        error_log("PDO Error (addCategory): " . implode(" | ", $stmt->errorInfo()));
        return false;
    }

    public function updateCategory($id, $data) {
        $query = "UPDATE DanhMuc SET TenDanhMuc = :tenDanhMuc, TrangThai = :trangThai WHERE Id = :id";
        $stmt = $this->pdo->prepare($query);
        
        $tenDanhMuc = $data['name'] ?? '';
        $trangThai = isset($data['status']) ? (int)$data['status'] : 0;

        $stmt->bindParam(':tenDanhMuc', $tenDanhMuc, PDO::PARAM_STR);
        $stmt->bindParam(':trangThai', $trangThai, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Liên kết ID
        
        if ($stmt->execute()) {
            return $stmt->rowCount(); // Trả về số hàng bị ảnh hưởng
        }
        error_log("PDO Error (updateCategory): " . implode(" | ", $stmt->errorInfo()));
        return false;
    }

    public function deleteCategory($id) {
        $query = "DELETE FROM DanhMuc WHERE Id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $stmt->rowCount(); // Trả về số hàng bị ảnh hưởng
        }
        error_log("PDO Error (deleteCategory): " . implode(" | ", $stmt->errorInfo()));
        return false;
    }
}
?>
