<?php
require_once ROOT_PATH . 'config/db_config.php';

require_once MODELS_PATH . 'AccountModel.php';

class AccountController {
    private $accountModel;

    public function __construct() {
        $this->accountModel = new AccountModel();
    }

    public function index() {
        // Lấy thông tin tài khoản của admin hiện tại
        // Giả sử bạn có một session biến lưu ID của admin đã đăng nhập
        $adminId = $_SESSION['admin_id'] ?? null; // Thay bằng cách lấy admin_id thực tế của bạn

        if ($adminId) {
            $accountInfo = $this->accountModel->getAdminAccount($adminId);
            if ($accountInfo) {
                // Tải view và truyền dữ liệu
                include VIEWS_PATH . 'account/index.php';
            } else {
                // Xử lý trường hợp không tìm thấy tài khoản
                $error = "Không tìm thấy thông tin tài khoản.";
                include VIEWS_PATH . 'account/index.php'; // Vẫn load view để hiển thị lỗi
            }
        } else {
            // Xử lý trường hợp admin chưa đăng nhập
            header('Location: ' . BASE_URL . 'auth/login'); // Chuyển hướng về trang đăng nhập
            exit();
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $adminId = $_SESSION['admin_id'] ?? null;
            if (!$adminId) {
                echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập.']);
                return;
            }

            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $oldPassword = $_POST['old_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmNewPassword = $_POST['confirm_new_password'] ?? '';

            $errors = [];

            // Xác thực dữ liệu
            if (empty($username)) {
                $errors[] = "Tên đăng nhập không được để trống.";
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email không hợp lệ.";
            }

            // Nếu có mật khẩu mới, xác thực nó
            if (!empty($newPassword)) {
                if ($newPassword !== $confirmNewPassword) {
                    $errors[] = "Mật khẩu mới và xác nhận mật khẩu không khớp.";
                }
                if (strlen($newPassword) < 6) {
                    $errors[] = "Mật khẩu mới phải có ít nhất 6 ký tự.";
                }
            }

            if (empty($errors)) {
                $updateData = [
                    'username' => $username,
                    'email' => $email
                ];

                $updatePasswordSuccess = true;
                if (!empty($newPassword)) {
                    // Cần xác minh mật khẩu cũ trước khi cập nhật mật khẩu mới
                    $verifyOldPassword = $this->accountModel->verifyAdminPassword($adminId, $oldPassword);
                    if ($verifyOldPassword) {
                        $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                    } else {
                        $updatePasswordSuccess = false;
                        $errors[] = "Mật khẩu cũ không đúng.";
                    }
                }

                if ($updatePasswordSuccess && $this->accountModel->updateAdminAccount($adminId, $updateData)) {
                    // Cập nhật session nếu username/email thay đổi
                    $_SESSION['admin_username'] = $username;
                    echo json_encode(['success' => true, 'message' => 'Cập nhật tài khoản thành công!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Không thể cập nhật tài khoản. ' . implode(', ', $errors)]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
            }
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['success' => false, 'message' => 'Phương thức yêu cầu không hợp lệ.']);
        }
    }
}

// Đây là phần định tuyến đơn giản (ví dụ)
$action = $_GET['action'] ?? 'index';
$controller = new AccountController();

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    // Xử lý lỗi 404 cho action không tồn tại
    http_response_code(404);
    echo "404 Not Found: Action '{$action}' not found in AccountController.";
}
