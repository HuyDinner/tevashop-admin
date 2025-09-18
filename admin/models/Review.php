<?php
// admin/models/Review.php

class Review {
    private $pdo; // Đây sẽ là đối tượng PDO
    private $table_name = "danhgia";      // Tên bảng đánh giá của bạn
    private $product_table = "SanPham"; // Sửa lại theo tên bảng đã dùng ở Product.php (TenSanPham, MaSanPham)
    private $customer_table = "KhachHang"; // Sửa lại theo tên bảng đã dùng ở Customers.php (TenKhachHang, MaKhachHang, Email)

    public function __construct(PDO $db) {
        $this->pdo = $db; // Nhận đối tượng PDO
    }

    // Lấy tất cả đánh giá, kèm thông tin sản phẩm và khách hàng
    public function getAllReviews() {
        $query = "SELECT dg.Id AS id, 
                         dg.SoLuong AS so_sao, 
                         dg.BinhLuan AS noi_dung, 
                         dg.NgayTao AS ngay_tao,
                         sp.TenSanPham AS ten_sanpham,    -- Giả định cột tên sản phẩm là TenSanPham
                         kh.TenKhachHang AS ten_khachhang, -- Giả định cột tên khách hàng là TenKhachHang
                         kh.Email AS email_khachhang
                  FROM " . $this->table_name . " dg
                  LEFT JOIN " . $this->product_table . " sp ON dg.MaSanPham = sp.Id -- Sửa từ MaSanPham sang Id nếu MaSanPham của danhgia tham chiếu Id của SanPham
                  LEFT JOIN " . $this->customer_table . " kh ON dg.MaKhachHang = kh.Id -- Sửa từ MaKhachHang sang Id nếu MaKhachHang của danhgia tham chiếu Id của KhachHang
                  ORDER BY dg.NgayTao DESC"; 

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy một đánh giá theo ID
    public function getReviewById($id) {
        $query = "SELECT dg.Id AS id, dg.SoLuong AS so_sao, dg.BinhLuan AS noi_dung, dg.NgayTao AS ngay_tao,
                         sp.TenSanPham AS ten_sanpham,
                         kh.TenKhachHang AS ten_khachhang,
                         kh.Email AS email_khachhang,
                         dg.MaSanPham AS id_sanpham, dg.MaKhachHang AS id_khachhang
                  FROM " . $this->table_name . " dg
                  LEFT JOIN " . $this->product_table . " sp ON dg.MaSanPham = sp.Id -- Sửa từ MaSanPham sang Id nếu MaSanPham của danhgia tham chiếu Id của SanPham
                  LEFT JOIN " . $this->customer_table . " kh ON dg.MaKhachHang = kh.Id -- Sửa từ MaKhachHang sang Id nếu MaKhachHang của danhgia tham chiếu Id của KhachHang
                  WHERE dg.Id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Xóa đánh giá
    public function deleteReview($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE Id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $stmt->rowCount(); // Trả về số hàng bị ảnh hưởng
        }
        error_log("PDO Error (deleteReview): " . implode(" | ", $stmt->errorInfo()));
        return false;
    }

    // Thêm các phương thức khác nếu cần, ví dụ: cập nhật đánh giá (dù thường không cho phép), thêm đánh giá mới từ admin, vv.
}
