<?php
// admin/controllers/ProductController.php

require_once APP_PATH . 'models/Product.php';
require_once MODELS_PATH . 'Category.php';

class ProductController {
    private $productModel;
    private $categoryModel;
    private $db;

    public function __construct($db) {
      $this->db = $db;
      $this->productModel = new Product($db);
      $this->categoryModel = new Category($db);
    }

    public function index() {
        // Lấy danh sách sản phẩm từ Product Model
        $products = $this->productModel->getAllProducts();
        
        // Tiêu đề trang
        $pageTitle = "Quản lý Sản phẩm"; 
        $pageIcon = "fas fa-box";

        $controllerName = "Product";

        // Truyền dữ liệu $products vào view
        include VIEWS_PATH . 'products/view_products.php'; 
    }

    public function add() {
        // Lấy danh sách danh mục để hiển thị trong dropdown của form
        $categories = $this->categoryModel->getAllCategories();
        
        // Định nghĩa tiêu đề và icon cho trang này
        $pageTitle = "Thêm Sản phẩm Mới";
        $pageIcon = "fas fa-plus-circle";
        $controllerName = "Product"; // Dùng để highlight menu sidebar

        // Kiểm tra nếu form đã được gửi đi (phương thức POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ form
            $data = [
                'TenSanPham' => $_POST['TenSanPham'] ?? '',
                'MaDanhMuc' => $_POST['MaDanhMuc'] ?? 0,
                'GiaGoc' => $_POST['GiaGoc'] ?? 0,
                'GiaKhuyenMai' => $_POST['GiaKhuyenMai'] ?? 0,
                'SoLuong' => $_POST['SoLuong'] ?? 0,
                'HinhAnh' => '', // Sẽ cập nhật sau khi xử lý upload ảnh
                'MoTa' => $_POST['MoTa'] ?? '',
                'TrangThai' => isset($_POST['TrangThai']) ? 1 : 0 // Nếu checkbox được chọn là 1, ngược lại là 0
            ];

            // Xử lý upload ảnh
            if (isset($_FILES['HinhAnh']) && $_FILES['HinhAnh']['error'] == 0) {
                // Thư mục để lưu ảnh sản phẩm (đặt trong thư mục gốc của dự án web, không phải admin)
                // Ví dụ: D:/xampp/htdocs/tevashop-admin/assets/images/products/
                $upload_dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR;
                
                $image_name = basename($_FILES['HinhAnh']['name']);
                $target_file = $upload_dir . $image_name;

                // Tạo thư mục nếu nó chưa tồn tại
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                // Di chuyển file đã upload vào thư mục đích
                if (move_uploaded_file($_FILES['HinhAnh']['tmp_name'], $target_file)) {
                    $data['HinhAnh'] = $image_name; // Lưu tên ảnh vào database
                } else {
                    // Xử lý lỗi khi upload ảnh
                    echo "<script>alert('Lỗi khi upload ảnh.');</script>";
                }
            }

            // Gọi phương thức addProduct từ ProductModel để lưu dữ liệu vào database
            if ($this->productModel->addProduct($data)) {
                // Chuyển hướng về trang danh sách sản phẩm sau khi thêm thành công
                echo "<script>alert('Thêm sản phẩm thành công!'); window.location.href = '".BASE_URL."product/index';</script>";
                exit();
            } else {
                // Thông báo lỗi nếu thêm sản phẩm thất bại
                echo "<script>alert('Lỗi khi thêm sản phẩm.');</script>";
            }
        }

        include VIEWS_PATH . 'products' . DIRECTORY_SEPARATOR . 'add_product.php'; 

    }

    // Chỉnh sửa định nghĩa hàm để tham số $id có giá trị mặc định là null
    public function edit() {
        $id = $_GET['id'] ?? null;

        $product = $this->productModel->getProductById($id);
        $categories = $this->categoryModel->getAllCategories();
        $pageTitle = "Sửa Sản phẩm";
        $pageIcon = "fas fa-edit";
        $controllerName = "Product";

        if (!$product) {
            echo "Sản phẩm không tồn tại.";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'TenSanPham' => $_POST['TenSanPham'] ?? '',
                'MaDanhMuc' => $_POST['MaDanhMuc'] ?? '',
                'GiaGoc' => $_POST['GiaGoc'] ?? 0,
                'GiaKhuyenMai' => $_POST['GiaKhuyenMai'] ?? 0,
                'SoLuong' => $_POST['SoLuong'] ?? 0,
                'HinhAnh' => $product['HinhAnh'], 
                'MoTa' => $_POST['MoTa'] ?? '',
                'TrangThai' => isset($_POST['TrangThai']) ? 1 : 0
            ];

            if (isset($_FILES['HinhAnh']) && $_FILES['HinhAnh']['error'] == 0) {
                $upload_dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR;
                $image_name = basename($_FILES['HinhAnh']['name']);
                $target_file = $upload_dir . $image_name;

                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                if (move_uploaded_file($_FILES['HinhAnh']['tmp_name'], $target_file)) {
                    $data['HinhAnh'] = $image_name;
                } else {
                    echo "<script>alert('Lỗi khi upload ảnh mới.');</script>";
                }
            }

            if ($this->productModel->updateProduct($id, $data)) {
                echo "<script>alert('Cập nhật sản phẩm thành công!'); window.location.href = '".BASE_URL."product/index';</script>";
                exit();
            } else {
                echo "<script>alert('Lỗi khi cập nhật sản phẩm.');</script>";
            }
        }

        include VIEWS_PATH . 'products' . DIRECTORY_SEPARATOR . 'edit_product.php';
        
    }

    public function delete() {
        $id = $_GET['id'] ?? null;

        // Kiểm tra xem phương thức có được gọi bằng POST hay không (nên có xác nhận)
        // Hiện tại, chúng ta xử lý đơn giản theo yêu cầu
        if ($this->productModel->deleteProduct($id)) {
            echo "<script>alert('Xóa sản phẩm thành công!'); window.location.href = '".BASE_URL."product/index';</script>";
            exit();
        } else {
            echo "<script>alert('Lỗi khi xóa sản phẩm.'); window.location.href = '".BASE_URL."product/index';</script>";
            exit();
        }
    }

    public function top_selling() {
        $topSellingProducts = $this->productModel->getTopSellingProducts();
        $pageTitle = "Top Sản phẩm bán chạy";
        $pageIcon = "fas fa-chart-line";
        $controllerName = "Hot Products";

        include VIEWS_PATH . 'products' . DIRECTORY_SEPARATOR . 'top_selling.php';
    }
}
