<?php
// admin/models/Order.php

class Order {
  private $pdo; 
  private $order_table = "donhang"; // Bảng donhang
  private $order_detail_table = "donhangchitiet"; // Bảng donhangchitiet
  private $customer_table = "KhachHang"; // Tên bảng khách hàng của bạn (Đã sửa lại để nhất quán với Customer.php)
  private $product_table = "SanPham"; // Tên bảng sản phẩm của bạn (Đã sửa lại để nhất quán với Product.php)
  private $table_detail_revenue = 'doanhthuchitiet';

  public function __construct(PDO $db) { // Type hint là PDO
    $this->pdo = $db;
  }

  // Lấy tất cả đơn hàng, kèm thông tin khách hàng
  public function getAllOrders() {
    $query = "SELECT dh.Id AS id, dh.NgayDatHang AS ngay_dat_hang, dh.TongTien AS tong_tien,
            dh.TrangThai AS trang_thai_don_hang,
            dh.TenNguoiNhan AS ten_nguoi_nhan, dh.DiaChiNguoiNhan AS dia_chi_nguoi_nhan,
            dh.SDTNguoiNhan AS sdt_nguoi_nhan, dh.EmailNguoiNhan AS email_nguoi_nhan,
            kh.TenKhachHang AS ten_khachhang, kh.Email AS email_khachhang
         FROM " . $this->order_table . " dh
         LEFT JOIN " . $this->customer_table . " kh ON dh.MaKhachHang = kh.Id -- Giả định MaKhachHang của donhang tham chiếu Id của KhachHang
         ORDER BY dh.NgayDatHang DESC"; 
    
    $stmt = $this->pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Sử dụng PDO fetchAll
  }

  // Lấy chi tiết một đơn hàng
  public function getOrderDetails($orderId) {
    $orderQuery = "SELECT dh.Id AS id, dh.NgayDatHang AS ngay_dat_hang, dh.TongTien AS tong_tien,
               dh.TrangThai AS trang_thai_don_hang,
               dh.TenNguoiNhan AS ten_nguoi_nhan, dh.DiaChiNguoiNhan AS dia_chi_nguoi_nhan,
               dh.SDTNguoiNhan AS sdt_nguoi_nhan, dh.EmailNguoiNhan AS email_nguoi_nhan,
               kh.TenKhachHang AS ten_khachhang, kh.Email AS email_khachhang
           FROM " . $this->order_table . " dh
           LEFT JOIN " . $this->customer_table . " kh ON dh.MaKhachHang = kh.Id -- Giả định MaKhachHang của donhang tham chiếu Id của KhachHang
           WHERE dh.Id = :order_id LIMIT 1";
    
    $stmtOrder = $this->pdo->prepare($orderQuery);
    $stmtOrder->bindParam(':order_id', $orderId, PDO::PARAM_INT);
    $stmtOrder->execute();
    $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

    if ($order) {
      $detailsQuery = "SELECT dhct.Id AS id, dhct.MaSanPham AS id_san_pham, dhct.SoLuong AS so_luong, dhct.Gia AS gia_ban,
                 sp.TenSanPham AS ten_san_pham_hien_tai -- Tên sản phẩm hiện tại từ bảng SanPham
              FROM " . $this->order_detail_table . " dhct
              LEFT JOIN " . $this->product_table . " sp ON dhct.MaSanPham = sp.Id -- Giả định MaSanPham của donhangchitiet tham chiếu Id của SanPham
              WHERE dhct.MaDonHang = :ma_don_hang";
      $stmtDetails = $this->pdo->prepare($detailsQuery);
      $stmtDetails->bindParam(':ma_don_hang', $orderId, PDO::PARAM_INT);
      $stmtDetails->execute();
      $details = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);
      $order['details'] = $details;
    }

    return $order;
  }

  // Cập nhật trạng thái đơn hàng
  public function updateOrderStatus($orderId, $status) {
        try {
            // Bước 1: Bắt đầu một transaction để đảm bảo cả hai thao tác (cập nhật và chèn) đều thành công
            $this->pdo->beginTransaction();

            // Lấy thông tin đơn hàng hiện tại
            $orderQuery = "SELECT TongTien FROM " . $this->order_table . " WHERE id = :orderId";
            $orderStmt = $this->pdo->prepare($orderQuery);
            $orderStmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
            $orderStmt->execute();
            $orderData = $orderStmt->fetch(PDO::FETCH_ASSOC);

            // Kiểm tra xem đơn hàng có tồn tại không
            if (!$orderData) {
                $this->pdo->rollBack();
                error_log("Order with ID " . $orderId . " not found.");
                return false;
            }

            // Bước 2: Chèn dữ liệu vào bảng doanh thu nếu trạng thái là 'Đã giao hàng'
            if ($status === 'Đã giao hàng') {
                $insertQuery = "INSERT INTO " . $this->table_detail_revenue . " (MaDonHang, NgayHoanThanh, TongTien) VALUES (:orderId, NOW(), :tongTien)";
                $insertStmt = $this->pdo->prepare($insertQuery);
                $insertStmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
                $insertStmt->bindParam(':tongTien', $orderData['TongTien'], PDO::PARAM_STR);
                $insertStmt->execute();
            }

            // Bước 3: Cập nhật trạng thái đơn hàng trong bảng đơn hàng chính
            $updateQuery = "UPDATE " . $this->order_table . " SET TrangThai = :status WHERE id = :orderId";
            $updateStmt = $this->pdo->prepare($updateQuery);
            $updateStmt->bindParam(':status', $status, PDO::PARAM_STR);
            $updateStmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
            $updateStmt->execute();

            // Kết thúc transaction
            $this->pdo->commit();

            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("PDO Error in updateOrderStatus: " . $e->getMessage());
            return false;
        }
    }
}
