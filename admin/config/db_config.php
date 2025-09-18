<?php
// admin/config/db_config.php

class Database {
    private static $instance = null;
    private $connection; // Sẽ là đối tượng PDO

    // Định nghĩa các hằng số kết nối
    private $db_server = 'localhost';
    private $db_username = 'root';
    private $db_port = 3306; // Đảm bảo cổng này khớp với cài đặt XAMPP của bạn
    private $db_password = '';
    private $db_name = 'tevashop_db';
    private $charset = 'utf8mb4'; // Thêm charset cho PDO

    private function __construct() {
        // Kết nối MYSQL DATABASE sử dụng PDO
        $dsn = "mysql:host={$this->db_server};port={$this->db_port};dbname={$this->db_name};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,     // Báo lỗi dưới dạng ngoại lệ
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // Mặc định trả về mảng kết hợp
            PDO::ATTR_EMULATE_PREPARES   => false,                     // Tắt giả lập prepared statements
        ];

        try {
            $this->connection = new PDO($dsn, $this->db_username, $this->db_password, $options);
        } catch (PDOException $e) {
            error_log("Lỗi kết nối CSDL (PDO): " . $e->getMessage());
            die("Không thể kết nối đến cơ sở dữ liệu. Vui lòng thử lại sau hoặc liên hệ quản trị viên.");
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}
