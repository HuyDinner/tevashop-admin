<?php
// D:\xampp\htdocs\tevashop-admin\admin\views\categories\add_category.php

// Các biến $pageTitle, $pageIcon, $controllerName sẽ được truyền từ CategoryController
?>
<body>
    <div class="dashboard-container">
           <?php include VIEWS_PATH . 'includes/sidebar.php'; ?>
    <main class="content">
            <?php include VIEWS_PATH . 'includes/header.php'; ?>
    <div class="container-fluid dashboard-content">
        <div class="card full-width-card">
            <div class="card-header">
                <h3>Form Thêm Danh mục</h3>
            </div>
            <div class="card-body">
                <form action="<?= BASE_URL ?>category/add" method="POST">
                    <div class="mb-3">
                        <label for="TenDanhMuc" class="form-label">Tên Danh mục:</label>
                        <input type="text" class="form-control" id="TenDanhMuc" name="TenDanhMuc" required>
                    </div>
                    <div class="mb-3">
                        <label for="MoTa" class="form-label">Mô tả:</label>
                        <textarea class="form-control" id="MoTa" name="MoTa" rows="3"></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="TrangThai" name="TrangThai" value="1" checked>
                        <label class="form-check-label" for="TrangThai">Hiển thị</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Thêm Danh mục</button>
                    <a href="<?= BASE_URL ?>category/index" class="btn btn-secondary">Quay lại</a>
                </form>
            </div>
        </div>
    </div>
</main>

<style>
    /* CSS cho phần dashboard-header */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding: 20px;
        background-color: #2b2b3b; /* Màu nền tối */
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .dashboard-header h2 {
        color: #fff; /* Chữ trắng */
        margin: 0;
        font-size: 1.8rem;
        display: flex;
        align-items: center;
    }

    .dashboard-header h2 i {
        margin-right: 10px;
        color: #9370db; /* Màu tím cho icon */
    }

    .header-icons {
        display: flex;
        gap: 20px;
    }

    .header-icons i {
        color: #fff; /* Màu trắng cho icon */
        font-size: 1.2rem;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .header-icons i:hover {
        color: #9370db; /* Đổi màu khi hover */
    }

    .user-profile-icon {
        font-size: 1.5rem !important; /* Lớn hơn một chút */
    }

    /* CSS cho phần container-fluid */
    .container-fluid.dashboard-content {
        padding: 0 20px 20px 20px; /* Bỏ padding trái phải để full-width card tràn ra */
    }

    /* CSS cho card toàn chiều rộng */
    .card.full-width-card {
        background-color: #2b2b3b; /* Màu nền tối */
        color: #fff; /* Chữ trắng */
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        padding: 20px; /* Thêm padding bên trong card */
    }

    .card-header {
        display: flex; /* Quan trọng: Sử dụng Flexbox */
        justify-content: space-between; /* Đẩy các phần tử ra hai phía */
        align-items: center; /* Căn giữa theo chiều dọc */
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #444; /* Đường kẻ phân cách */
    }

    .card-header h3 {
        margin: 0;
        font-size: 1.5rem;
        color: #fff;
    }

    .card-body {
        padding: 0; /* Bỏ padding mặc định của card-body để bảng không bị thừa padding */
    }

    /* CSS cho form */
    .form-label {
        color: #fff; /* Màu chữ trắng cho label */
        margin-bottom: 5px;
    }

    .form-control, .form-select {
        background-color: #3a3a4c; /* Nền input tối */
        color: #fff; /* Chữ input trắng */
        border: 1px solid #555;
        padding: 10px 15px;
        border-radius: 5px;
        width: 100%;
        box-sizing: border-box; /* Đảm bảo padding không làm tăng kích thước */
    }

    .form-control:focus, .form-select:focus {
        border-color: #9370db; /* Viền tím khi focus */
        box-shadow: 0 0 0 0.25rem rgba(147, 112, 219, 0.25); /* Hiệu ứng focus */
        outline: none;
    }

    .mb-3 {
        margin-bottom: 1rem; /* Khoảng cách giữa các trường form */
    }

    .form-check {
        margin-top: 15px;
        margin-bottom: 15px;
    }

    .form-check-input {
        margin-right: 5px;
    }

    .form-check-label {
        color: #fff;
    }

    /* CSS cho nút */
    .btn {
        padding: 8px 15px;
        border-radius: 5px;
        text-decoration: none; /* Bỏ gạch chân của link */
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .btn-primary {
        background-color: #9370db; /* Màu tím */
        color: #fff; /* Đặt màu chữ trắng cho nút */
        border: none;
    }

    .btn-primary:hover {
        background-color: #7b58cb; /* Màu tím đậm hơn khi hover */
    }

    .btn-secondary {
        background-color: #6c757d; /* Màu xám */
        color: #fff;
        border: none;
        margin-left: 10px; /* Khoảng cách với nút primary */
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }
</style>
