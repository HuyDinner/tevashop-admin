<?php
// admin/models/Product.php

class Product {
    private $pdo; // Biến này sẽ giữ đối tượng PDO
    private $table_name = "SanPham"; // Tên bảng sản phẩm

    public function __construct(PDO $db) {
        $this->pdo = $db;
    }

    // Lấy tất cả sản phẩm cùng tên danh mục, bao gồm các cột có trong DB
    public function getAllProducts() {
        $sql = "SELECT p.Id, p.TenSanPham, p.MaDanhMuc, p.GiaGoc, p.HinhAnh, p.GiaKhuyenMai, 
                        p.SoLuong, p.LuotXem, p.MoTa, p.TrangThai, 
                        c.TenDanhMuc 
                FROM " . $this->table_name . " p 
                LEFT JOIN DanhMuc c ON p.MaDanhMuc = c.Id";
        
        $stmt = $this->pdo->prepare($sql); // Sử dụng PDO prepare
        $stmt->execute(); // Thực thi truy vấn
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Lấy tất cả kết quả dưới dạng mảng kết hợp
    }

    public function getProductById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE Id = :id LIMIT 1"; // Sử dụng placeholder đã đặt tên
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Liên kết tham số kiểu INT
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Lấy một hàng dưới dạng mảng kết hợp
    }

    public function addProduct($data) {
        // Các cột cần chèn dựa trên schema du_an_mau_nhom_5.sql
        // TenSanPham, MaDanhMuc, GiaGoc, HinhAnh, GiaKhuyenMai, SoLuong, MoTa, TrangThai
        // (Bỏ qua LuotXem, NoiBat, MotaNgan nếu không có trong form nhập liệu)
        $query = "INSERT INTO " . $this->table_name . " (TenSanPham, MaDanhMuc, GiaGoc, HinhAnh, GiaKhuyenMai, SoLuong, MoTa, TrangThai) 
                  VALUES (:tenSanPham, :maDanhMuc, :giaGoc, :hinhAnh, :giaKhuyenMai, :soLuong, :moTa, :trangThai)";
        
        $stmt = $this->pdo->prepare($query);
        
        // Gán giá trị mặc định nếu key không tồn tại, để tránh Undefined array key
        $tenSanPham = $data['TenSanPham'] ?? '';
        $maDanhMuc = $data['MaDanhMuc'] ?? 0;
        $giaGoc = $data['GiaGoc'] ?? 0.0;
        $hinhAnh = $data['HinhAnh'] ?? '';
        $giaKhuyenMai = $data['GiaKhuyenMai'] ?? 0.0;
        $soLuong = $data['SoLuong'] ?? 0;
        $moTa = $data['MoTa'] ?? '';
        $trangThai = $data['TrangThai'] ?? 0;

        // Liên kết các tham số đã đặt tên với biến PHP
        $stmt->bindParam(':tenSanPham', $tenSanPham, PDO::PARAM_STR);
        $stmt->bindParam(':maDanhMuc', $maDanhMuc, PDO::PARAM_INT);
        $stmt->bindParam(':giaGoc', $giaGoc); // PDO tự động suy luận kiểu cho số float/double
        $stmt->bindParam(':hinhAnh', $hinhAnh, PDO::PARAM_STR);
        $stmt->bindParam(':giaKhuyenMai', $giaKhuyenMai); // PDO tự động suy luận kiểu
        $stmt->bindParam(':soLuong', $soLuong, PDO::PARAM_INT);
        $stmt->bindParam(':moTa', $moTa, PDO::PARAM_STR);
        $stmt->bindParam(':trangThai', $trangThai, PDO::PARAM_INT);
        
        // PDO execute trả về true/false
        if ($stmt->execute()) {
            return $this->pdo->lastInsertId(); // Trả về ID của hàng cuối cùng được chèn
        }
        
        // Log lỗi chi tiết hơn từ PDO
        error_log("PDO Error (addProduct): " . implode(" | ", $stmt->errorInfo()));
        return false;
    }

    public function updateProduct($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET TenSanPham = :tenSanPham, MaDanhMuc = :maDanhMuc, GiaGoc = :giaGoc, 
                      HinhAnh = :hinhAnh, GiaKhuyenMai = :giaKhuyenMai, SoLuong = :soLuong, 
                      MoTa = :moTa, TrangThai = :trangThai 
                  WHERE Id = :id";
        
        $stmt = $this->pdo->prepare($query);
        
        $tenSanPham = $data['TenSanPham'] ?? '';
        $maDanhMuc = $data['MaDanhMuc'] ?? 0;
        $giaGoc = $data['GiaGoc'] ?? 0.0;
        $hinhAnh = $data['HinhAnh'] ?? '';
        $giaKhuyenMai = $data['GiaKhuyenMai'] ?? 0.0;
        $soLuong = $data['SoLuong'] ?? 0;
        $moTa = $data['MoTa'] ?? '';
        $trangThai = $data['TrangThai'] ?? 0;

        $stmt->bindParam(':tenSanPham', $tenSanPham, PDO::PARAM_STR);
        $stmt->bindParam(':maDanhMuc', $maDanhMuc, PDO::PARAM_INT);
        $stmt->bindParam(':giaGoc', $giaGoc);
        $stmt->bindParam(':hinhAnh', $hinhAnh, PDO::PARAM_STR);
        $stmt->bindParam(':giaKhuyenMai', $giaKhuyenMai);
        $stmt->bindParam(':soLuong', $soLuong, PDO::PARAM_INT);
        $stmt->bindParam(':moTa', $moTa, PDO::PARAM_STR);
        $stmt->bindParam(':trangThai', $trangThai, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Liên kết ID
        
        if ($stmt->execute()) {
            return $stmt->rowCount(); // Trả về số hàng bị ảnh hưởng
        }
        error_log("PDO Error (updateProduct): " . implode(" | ", $stmt->errorInfo()));
        return false;
    }

    public function deleteProduct($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE Id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $stmt->rowCount(); // Trả về số hàng bị ảnh hưởng
        }
        error_log("PDO Error (deleteProduct): " . implode(" | ", $stmt->errorInfo()));
        return false;
    }

    public function getTopSellingProducts($limit = 10) {
        $query = "SELECT p.Id, p.TenSanPham, p.HinhAnh, SUM(o.SoLuong) AS TongSoLuongBan 
                  FROM " . $this->table_name . " p 
                  JOIN DonHangChiTiet o ON p.Id = o.MaSanPham 
                  GROUP BY p.Id 
                  ORDER BY TongSoLuongBan DESC 
                  LIMIT :limit";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
