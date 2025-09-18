<?php
// admin/controllers/AuthController.php

require_once MODELS_PATH . 'AdminUser.php';

class AuthController {
    private $adminUserModel;

    public function __construct($db) {
        $this->adminUserModel = new AdminUser($db);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
                echo "<script>alert('Vui lòng điền đầy đủ các trường.');</script>";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "<script>alert('Email không hợp lệ.');</script>";
            } elseif ($password !== $confirmPassword) {
                echo "<script>alert('Mật khẩu xác nhận không khớp.');</script>";
            } else {
                if ($this->adminUserModel->register($username, $email, $password)) {
                    echo "<script>alert('Đăng ký thành công! Vui lòng đăng nhập.'); window.location.href = '".BASE_URL."auth/login';</script>";
                    exit();
                } else {
                    echo "<script>alert('Đăng ký thất bại. Tên người dùng hoặc Email có thể đã tồn tại.');</script>";
                }
            }
        }
        // Hiển thị form đăng ký
        $pageTitle = "Đăng ký Admin";
        include VIEWS_PATH . 'auth' . DIRECTORY_SEPARATOR . 'register.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usernameOrEmail = $_POST['username_or_email'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($usernameOrEmail) || empty($password)) {
                echo "<script>alert('Vui lòng điền đầy đủ tên người dùng/email và mật khẩu.');</script>";
            } else {
                $user = $this->adminUserModel->login($usernameOrEmail, $password);
                if ($user) {
                    // Đăng nhập thành công, khởi tạo session
                    session_start();
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['admin_email'] = $user['email'];

                    echo "<script>alert('Đăng nhập thành công!'); window.location.href = '".BASE_URL."dashboard/index';</script>";
                    exit();
                } else {
                    echo "<script>alert('Tên người dùng/Email hoặc mật khẩu không đúng.');</script>";
                }
            }
        }
        // Hiển thị form đăng nhập
        $pageTitle = "Đăng nhập Admin";
        include VIEWS_PATH . 'auth' . DIRECTORY_SEPARATOR . 'login.php';
    }

    public function logout() {
        session_start();
        session_unset(); // Xóa tất cả các biến session
        session_destroy(); // Hủy session
        echo "<script>alert('Bạn đã đăng xuất!'); window.location.href = '".BASE_URL."auth/login';</script>";
        exit();
    }
}
