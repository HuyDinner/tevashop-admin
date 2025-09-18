<?php
// tevashop-admin/admin/controllers/ChatController.php

require_once ROOT_PATH . 'models/ChatModel.php'; // Đảm bảo đường dẫn ROOT_PATH đã đúng

class ChatController {
    private $db;
    private $chatModel;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->chatModel = new ChatModel($db);
    }

    public function index() {
        // Kiểm tra đăng nhập (đã có ở index.php, nhưng kiểm tra lại cũng tốt)
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            header("Location: " . BASE_URL . "auth/login");
            exit();
        }

        // Lấy thông tin admin để hiển thị trên giao diện (ví dụ: tên admin)
        $adminId = $_SESSION['admin_id'] ?? null;
        $adminUsername = $_SESSION['admin_username'] ?? 'Admin'; // Giả định bạn lưu username trong session

        // Render view quản lý chat
        require_once VIEWS_PATH . 'chat/index.php';
    }

    // Các phương thức khác nếu bạn muốn có API riêng cho controller này
    // Tuy nhiên, chúng ta đã có chat_api.php riêng biệt.
}
