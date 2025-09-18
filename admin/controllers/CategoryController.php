<?php
// D:/xampp/htdocs/tevashop-admin/admin/controllers/CategoryController.php

require_once MODELS_PATH . 'Category.php'; // Nhúng Category Model

class CategoryController {
    private $categoryModel;
    private $db; // Thêm biến $db

    public function __construct($db) {
        $this->db = $db; // Gán đối tượng $db vào thuộc tính
        $this->categoryModel = new Category($db);
    }

    public function index() {
        $categories = $this->categoryModel->getAllCategories();

        // Định nghĩa tiêu đề và icon cho trang này
        $pageTitle = "Quản lý Danh mục";
        $pageIcon = "fas fa-list-alt"; // Icon cho danh mục

        // Định nghĩa controllerName cho sidebar active
        $controllerName = "Category";
        
        // Load View chính của trang (Nó sẽ nằm trong main-content-wrapper)
        include VIEWS_PATH . 'categories' . DIRECTORY_SEPARATOR . 'view_categories.php'; 

    }

    // Thêm các phương thức khác như add, edit, delete
    public function add() {
        $pageTitle = "Thêm Danh mục Mới";
        $pageIcon = "fas fa-plus-circle";
        $controllerName = "Category";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['TenDanhMuc'],
                'description' => $_POST['MoTa'],
                'status' => isset($_POST['TrangThai']) ? 1 : 0
            ];
            if ($this->categoryModel->addCategory($data)) {
                header('Location: ' . BASE_URL . 'category/index');
                exit();
            } else {
                $errorMessage = "Có lỗi khi thêm danh mục.";
            }
        }

        include VIEWS_PATH . 'categories' . DIRECTORY_SEPARATOR . 'add_category.php'; // Tạo file này nếu chưa có
    }

    public function edit() {
        $pageTitle = "Sửa Danh mục";
        $pageIcon = "fas fa-edit";
        $controllerName = "Category";

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . 'category/index');
            exit();
        }

        $category = $this->categoryModel->getCategoryById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['TenDanhMuc'],
                'description' => $_POST['MoTa'],
                'status' => isset($_POST['TrangThai']) ? 1 : 0
            ];
            if ($this->categoryModel->updateCategory($id, $data)) {
                header('Location: ' . BASE_URL . 'category/index');
                exit();
            } else {
                $errorMessage = "Có lỗi khi cập nhật danh mục.";
            }
        }

        include VIEWS_PATH . 'categories' . DIRECTORY_SEPARATOR . 'edit_category.php'; // Tạo file này nếu chưa có
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id && $this->categoryModel->deleteCategory($id)) {
            // Xử lý thông báo thành công nếu cần
        } else {
            // Xử lý thông báo lỗi nếu cần
        }
        header('Location: ' . BASE_URL . 'category/index');
        exit();
    }
}
?>
