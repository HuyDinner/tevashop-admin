<?php
// admin/controllers/CustomerController.php

require_once MODELS_PATH . 'Customers.php';

class CustomerController {
    private $customerModel;

    public function __construct($db) {
        $this->customerModel = new Customer($db);
    }

    // Phương thức hiển thị danh sách tất cả khách hàng
    public function index() {
        $customers = $this->customerModel->getAllCustomers();
        $pageTitle = "Quản lý Khách hàng";
        $pageIcon = "fas fa-users";
        $controllerName = "Customer";

        include VIEWS_PATH . 'customers' . DIRECTORY_SEPARATOR . 'view_customers.php';

    }

    // Phương thức xóa khách hàng
    public function delete($id) {
        $customer = $this->customerModel->getCustomerById($id);
        if (!$customer) {
            echo "<script>alert('Khách hàng không tồn tại!'); window.location.href = '".BASE_URL."customer/index';</script>";
            exit();
        }

        if ($this->customerModel->deleteCustomer($id)) {
            echo "<script>alert('Xóa khách hàng thành công!'); window.location.href = '".BASE_URL."customer/index';</script>";
            exit();
        } else {
            echo "<script>alert('Lỗi khi xóa khách hàng. Có thể khách hàng này có liên kết với dữ liệu khác (ví dụ: đơn hàng)!');</script>";
            exit();
        }
    }
}
