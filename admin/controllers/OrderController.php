<?php
// admin/controllers/OrderController.php

require_once MODELS_PATH . 'Order.php';

class OrderController {
    private $orderModel;
    private $availableStatuses = [
        'Đang chờ xử lý',
        'Đã xác nhận',
        'Đang chuẩn bị hàng', // Thêm trạng thái này
        'Đang giao hàng',
        'Đã giao hàng',
        'Đã hủy'
    ]; // Các trạng thái đơn hàng hợp lệ

    public function __construct($db) {
        $this->orderModel = new Order($db);
    }

    public function index() {
        $orders = $this->orderModel->getAllOrders(); 

        $pageTitle = "Quản lý Đơn hàng";
        $pageIcon = "fas fa-shopping-cart";
        $controllerName = "Order"; 

        include VIEWS_PATH . 'orders' . DIRECTORY_SEPARATOR . 'view_orders.php';
    }

    public function detail($orderId = null) {
        if (!isset($_SESSION['admin_logged_in'])) {
            header("Location: " . BASE_URL . "auth/login");
            exit();
        }

        if ($orderId === null) {
            $_SESSION['message'] = "Không tìm thấy ID đơn hàng.";
            $_SESSION['message_type'] = "error";
            header("Location: " . BASE_URL . "order/index");
            exit();
        }

        $order = $this->orderModel->getOrderDetails($orderId);

        if (!$order) {
            $_SESSION['message'] = "Không tìm thấy đơn hàng này.";
            $_SESSION['message_type'] = "error";
            header("Location: " . BASE_URL . "order/index");
            exit();
        }

        $pageTitle = "Chi tiết Đơn hàng: ";
        $pageIcon = "fas fa-info-circle";
        $orderStatuses = $this->availableStatuses;
        $controllerName = "Order"; 

        include VIEWS_PATH . 'orders' . DIRECTORY_SEPARATOR . 'order_details.php'; 
    }

    // Cập nhật trạng thái đơn hàng
    public function updateStatus($orderId = null) {
        if (!isset($_SESSION['admin_logged_in'])) {
            header("Location: " . BASE_URL . "auth/login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $orderId !== null) {
            $newStatus = $_POST['trang_thai'] ?? '';

            if (!in_array($newStatus, $this->availableStatuses)) {
                $_SESSION['message'] = "Trạng thái đơn hàng không hợp lệ.";
                $_SESSION['message_type'] = "error";
                header("Location: " . BASE_URL . "order/detail/" . $orderId);
                exit();
            }

            if ($this->orderModel->updateOrderStatus($orderId, $newStatus)) {
                $_SESSION['message'] = "Cập nhật trạng thái đơn hàng thành công!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Cập nhật trạng thái đơn hàng thất bại.";
                $_SESSION['message_type'] = "error";
            }
        } else {
            $_SESSION['message'] = "Yêu cầu không hợp lệ.";
            $_SESSION['message_type'] = "error";
        }
        
        header("Location: " . BASE_URL . "order/detail/" . $orderId);
        exit();
    }
}
