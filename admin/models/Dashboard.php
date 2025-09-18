<?php
// admin/models/Dashboard.php

// Đảm bảo APP_PATH được định nghĩa ở index.php hoặc file config chung
require_once APP_PATH . 'config/db_config.php';

class Dashboard {
    private $pdo; // Bây giờ đây là đối tượng PDO

    // Điều chỉnh tên bảng cho phù hợp với các Models khác và CSDL của bạn
    private $table_detail_revenue = 'doanhthuchitiet'; // Bảng doanh thu chi tiết
    private $table_orders = 'donhang';          // Bảng đơn hàng
    private $table_customers = 'KhachHang';     // Bảng khách hàng
    private $table_products = 'SanPham';        // Bảng sản phẩm
    private $table_order_items = 'donhangchitiet'; // Bảng chi tiết đơn hàng (nếu bạn có)
    private $table_admin_users = 'admin_users'; // Bảng người dùng admin/workers

    public function __construct() {
        // Lấy đối tượng PDO từ Database::getInstance()
        $db = Database::getInstance();
        $this->pdo = $db->getConnection(); // Lấy đối tượng PDO
    }

    public function getTotalOrders() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_orders;
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] ?? 0; // Sử dụng null coalescing operator
        } catch (PDOException $e) {
            error_log("PDO Error in getTotalOrders: " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalCustomers() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_customers;
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("PDO Error in getTotalCustomers: " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalRevenue() {
        try {
            // Giả định cột là 'TongTien' và 'TrangThai'
            $query = "SELECT SUM(TongTien) as total FROM " . $this->table_detail_revenue;
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // Sử dụng intval để đảm bảo là số, sau đó định dạng. Kiểm tra null trước.
            return $row['total'] ? number_format(intval($row['total']), 0, ',', '.') : '0';
        } catch (PDOException $e) {
            error_log("PDO Error in getTotalRevenue: " . $e->getMessage());
            return '0';
        }
    }

    public function getTotalProducts() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_products;
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("PDO Error in getTotalProducts: " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalWorkers() {
        try {
            // Giả định bảng admin_users có cột 'role' và giá trị 'worker'
            $query = "SELECT COUNT(*) as total FROM " . $this->table_admin_users ;
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("PDO Error in getTotalWorkers: " . $e->getMessage());
            return 0;
        }
    }

    public function getOrderStatusCounts() {
        try {
            // Giả định cột trạng thái là 'TrangThai'
            $query = "SELECT TrangThai, COUNT(*) as count FROM " . $this->table_orders . " GROUP BY TrangThai";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $data = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[$row['TrangThai']] = $row['count'];
            }

            // Lấy tổng số đơn hàng để tính phần trăm (hoặc chỉ hiển thị số lượng)
            $totalOrders = $this->getTotalOrders();
            if ($totalOrders == 0) {
                return [
                    'on_delivery' => 0, // Đang giao
                    'delivered' => 0,   // Đã giao
                    'cancelled' => 0,   // Đã hủy
                    'pending' => 0      // Chờ xử lý
                ];
            }
            // Trả về số lượng, không phải phần trăm, như bạn đã setup trong HTML/JS cho biểu đồ doughnut
            return [
                'on_delivery' => isset($data['on_delivery']) ? $data['on_delivery'] : 0,
                'delivered' => isset($data['delivered']) ? $data['delivered'] : 0,
                'cancelled' => isset($data['cancelled']) ? $data['cancelled'] : 0,
                'pending' => isset($data['pending']) ? $data['pending'] : 0
            ];
        } catch (PDOException $e) {
            error_log("PDO Error in getOrderStatusCounts: " . $e->getMessage());
            return [
                'on_delivery' => 0,
                'delivered' => 0,
                'cancelled' => 0,
                'pending' => 0
            ];
        }
    }

    public function getTopSellingItems($limit = 3) {
        try {
            // Giả định: bảng donhangchitiet có MaSanPham, SoLuong. Bảng SanPham có TenSanPham, HinhAnh.
            $stmt = $this->pdo->prepare("SELECT p.TenSanPham, SUM(oi.SoLuong) as total_sold, p.HinhAnh
                                          FROM " . $this->table_order_items . " oi
                                          JOIN " . $this->table_products . " p ON oi.MaSanPham = p.Id
                                          GROUP BY p.Id, p.TenSanPham, p.HinhAnh
                                          ORDER BY total_sold DESC
                                          LIMIT :limit");
            $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("PDO Error in getTopSellingItems: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Lấy dữ liệu doanh thu hàng tháng trong năm hiện tại.
     * Cần cột `NgayDatHang` (DATETIME/DATE) và `TongTien` trong bảng `donhang`.
     */
    public function getMonthlyRevenueData() {
        try {
            $currentYear = date('Y');
            
            // Sửa tên bảng để truy vấn bảng doanh thu chi tiết
            // Loại bỏ điều kiện 'TrangThai' vì bảng này chỉ lưu đơn hàng đã hoàn thành
            $query = "SELECT
                          MONTH(NgayHoanThanh) as month,
                          SUM(TongTien) as total_revenue
                      FROM " . $this->table_detail_revenue . "
                      WHERE YEAR(NgayHoanThanh) = :currentYear
                      GROUP BY MONTH(NgayHoanThanh)
                      ORDER BY MONTH(NgayHoanThanh)";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(":currentYear", $currentYear, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $monthlyData = array_fill(1, 12, 0); // Khởi tạo mảng 12 tháng với giá trị 0

            foreach ($result as $row) {
                // Kiểm tra và làm tròn dữ liệu doanh thu, chia cho 1 triệu
                $monthlyData[$row['month']] = round((float)$row['total_revenue'] / 1000000, 2);
            }

            $labels = [];
            for ($i = 1; $i <= 12; $i++) {
                $labels[] = 'Th' . $i;
            }

            return [
                'labels' => $labels,
                'data' => array_values($monthlyData) // Lấy các giá trị theo thứ tự tháng
            ];

        } catch (PDOException $e) {
            error_log("PDO Error in getMonthlyRevenueData: " . $e->getMessage());
            return [
                'labels' => ['Th1', 'Th2', 'Th3', 'Th4', 'Th5', 'Th6', 'Th7', 'Th8', 'Th9', 'Th10', 'Th11', 'Th12'],
                'data' => array_fill(0, 12, 0)
            ];
        }
    }

    /**
     * Lấy dữ liệu khách hàng theo ngày trong tuần hiện tại và tuần trước.
     * Cần cột `NgayDangKy` (DATETIME/DATE) trong bảng `KhachHang`.
     */
    public function getCustomerGrowthData() {
        try {
            $data = [
                'thisWeek' => array_fill(0, 7, 0), // 0:CN, 1:T2, ..., 6:T7
                'lastWeek' => array_fill(0, 7, 0)
            ];

            // Ngày bắt đầu tuần hiện tại (Chủ Nhật)
            $startOfWeek = date('Y-m-d', strtotime('last sunday'));
            // Ngày bắt đầu tuần trước (Chủ Nhật của tuần trước đó)
            $startOfLastWeek = date('Y-m-d', strtotime('last sunday', strtotime('-1 week')));

            // Lấy dữ liệu tuần hiện tại
            $queryThisWeek = "SELECT DAYOFWEEK(NgayDangKy) as day_of_week, COUNT(*) as count
                              FROM " . $this->table_customers . "
                              WHERE NgayDangKy >= :startOfWeek AND NgayDangKy < DATE_ADD(:startOfWeek, INTERVAL 7 DAY)
                              GROUP BY DAYOFWEEK(NgayDangKy)";
            $stmtThisWeek = $this->pdo->prepare($queryThisWeek);
            $stmtThisWeek->bindParam(":startOfWeek", $startOfWeek);
            $stmtThisWeek->execute();
            while ($row = $stmtThisWeek->fetch(PDO::FETCH_ASSOC)) {
                // MySQL DAYOFWEEK: 1=Sunday, 2=Monday, ..., 7=Saturday
                // Chuyển về 0=CN, 1=T2,...
                $data['thisWeek'][($row['day_of_week'] - 1)] = $row['count'];
            }

            // Lấy dữ liệu tuần trước
            $queryLastWeek = "SELECT DAYOFWEEK(NgayDangKy) as day_of_week, COUNT(*) as count
                              FROM " . $this->table_customers . "
                              WHERE NgayDangKy >= :startOfLastWeek AND NgayDangKy < DATE_ADD(:startOfLastWeek, INTERVAL 7 DAY)
                              GROUP BY DAYOFWEEK(NgayDangKy)";
            $stmtLastWeek = $this->pdo->prepare($queryLastWeek);
            $stmtLastWeek->bindParam(":startOfLastWeek", $startOfLastWeek);
            $stmtLastWeek->execute();
            while ($row = $stmtLastWeek->fetch(PDO::FETCH_ASSOC)) {
                $data['lastWeek'][($row['day_of_week'] - 1)] = $row['count'];
            }

            return $data;

        } catch (PDOException $e) {
            error_log("PDO Error in getCustomerGrowthData: " . $e->getMessage());
            return [
                'thisWeek' => array_fill(0, 7, 0),
                'lastWeek' => array_fill(0, 7, 0)
            ];
        }
    }

    /**
     * Lấy dữ liệu tỷ lệ đơn hàng theo khung giờ.
     * Cần cột `NgayDatHang` (DATETIME) trong bảng `donhang`.
     * Các khung giờ: Morning (5-11), Afternoon (12-17), Evening (18-22), Night (23-4).
     */
    public function getOrderTimeDistribution() {
        try {
            $query = "SELECT
                          CASE
                              WHEN HOUR(NgayDatHang) BETWEEN 5 AND 11 THEN 'morning'
                              WHEN HOUR(NgayDatHang) BETWEEN 12 AND 17 THEN 'afternoon'
                              WHEN HOUR(NgayDatHang) BETWEEN 18 AND 22 THEN 'evening'
                              ELSE 'night'
                          END as time_of_day,
                          COUNT(*) as count
                      FROM " . $this->table_orders . "
                      GROUP BY time_of_day";

            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $distribution = [
                'morning' => 0,
                'afternoon' => 0,
                'evening' => 0,
                'night' => 0
            ];
            $totalOrders = 0;

            foreach ($result as $row) {
                $distribution[$row['time_of_day']] = $row['count'];
                $totalOrders += $row['count'];
            }

            if ($totalOrders == 0) {
                return [
                    'morning' => 0,
                    'afternoon' => 0,
                    'evening' => 0,
                    'night' => 0
                ];
            }

            // Chuyển đổi sang phần trăm
            return [
                'morning' => round(($distribution['morning'] / $totalOrders) * 100),
                'afternoon' => round(($distribution['afternoon'] / $totalOrders) * 100),
                'evening' => round(($distribution['evening'] / $totalOrders) * 100),
                'night' => round(($distribution['night'] / $totalOrders) * 100)
            ];

        } catch (PDOException $e) {
            error_log("PDO Error in getOrderTimeDistribution: " . $e->getMessage());
            return [
                'morning' => 0,
                'afternoon' => 0,
                'evening' => 0,
                'night' => 0
            ];
        }
    }
}
?>
