<?php
// admin/controllers/NotificationsController.php

// Notification Model đã được require trong admin/index.php
// (Đảm bảo Notification.php đã được require trước class này)

class NotificationsController {
    private $pdo_conn; // Đổi tên biến để rõ ràng hơn
    private $notificationModel;

    public function __construct(PDO $db) { // THAY ĐỔI: Type hint thành PDO
        $this->pdo_conn = $db;
        $this->notificationModel = new Notification($this->pdo_conn); 
    }

    public function index() {
        $adminId = $_SESSION['admin_id'] ?? null;
        if ($adminId === null) {
            header("Location: " . BASE_URL . "auth/login");
            exit();
        }

        $pageTitle = "Quản lý Thông báo";
        $pageIcon = "fas fa-bell";

        // Bạn có thể cần gọi các phương thức từ notificationModel ở đây để lấy dữ liệu cho view
        // Ví dụ: $notifications = $this->notificationModel->getAllNotifications($adminId);

        include VIEWS_PATH . 'notifications/index.php';
    }
}
