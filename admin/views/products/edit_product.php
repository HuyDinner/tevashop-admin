<?php
// admin/views/products/edit_product.php

// Đảm bảo các biến cần thiết được truyền vào view
if (!isset($product) || !isset($categories) || !isset($pageTitle) || !isset($pageIcon) || !isset($controllerName)) {
    // Xử lý lỗi hoặc chuyển hướng nếu các biến không được truyền
    echo "Lỗi: Dữ liệu sản phẩm không đầy đủ để hiển thị form sửa.";
    return;
}
?>
<body>
   <div class="dashboard-container">
        <?php include VIEWS_PATH . 'includes/sidebar.php'; ?>

    <main class="main-content">
        <?php include VIEWS_PATH . 'includes/header.php'; ?>

<div class="container-fluid dashboard-content">
    <div class="card full-width-card">
        <div class="card-top-area">
            <div class="card-header">
                <h3><?= htmlspecialchars($pageTitle) ?></h3>
            </div>
        </div>

        <div class="card-body">
            <form action="<?= BASE_URL ?>product/edit?id=<?= htmlspecialchars($product['Id']) ?>" method="POST" enctype="multipart/form-data" class="product-form">
                <div class="form-group">
                    <label for="TenSanPham">Tên Sản phẩm:</label>
                    <input type="text" id="TenSanPham" name="TenSanPham" class="form-control" value="<?= htmlspecialchars($product['TenSanPham']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="MaDanhMuc">Danh mục:</label>
                    <select id="MaDanhMuc" name="MaDanhMuc" class="form-control" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['Id']) ?>"
                                <?= ($category['Id'] == $product['MaDanhMuc']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['TenDanhMuc']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="GiaGoc">Giá gốc:</label>
                    <input type="number" id="GiaGoc" name="GiaGoc" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars($product['GiaGoc']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="GiaKhuyenMai">Giá khuyến mãi:</label>
                    <input type="number" id="GiaKhuyenMai" name="GiaKhuyenMai" class="form-control" step="0.01" min="0" value="<?= htmlspecialchars($product['GiaKhuyenMai']) ?>">
                </div>

                <div class="form-group">
                    <label for="SoLuong">Số lượng:</label>
                    <input type="number" id="SoLuong" name="SoLuong" class="form-control" min="0" value="<?= htmlspecialchars($product['SoLuong']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="HinhAnh">Hình ảnh hiện tại:</label>
                    <?php if (!empty($product['HinhAnh'])): ?>
                        <div class="current-image-preview">
                            <img src="<?= IMAGES_PATH . 'products/' . htmlspecialchars($product['HinhAnh']) ?>" alt="Ảnh sản phẩm hiện tại" class="img-thumbnail">
                            <p><small>Tên file: <?= htmlspecialchars($product['HinhAnh']) ?></small></p>
                        </div>
                    <?php else: ?>
                        <p>Chưa có hình ảnh nào.</p>
                    <?php endif; ?>
                    <label for="newHinhAnh">Chọn ảnh mới (để thay đổi):</label>
                    <input type="file" id="newHinhAnh" name="HinhAnh" class="form-control-file">
                </div>

                <div class="form-group">
                    <label for="MoTa">Mô tả:</label>
                    <textarea id="MoTa" name="MoTa" class="form-control" rows="5"><?= htmlspecialchars($product['MoTa']) ?></textarea>
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" id="TrangThai" name="TrangThai" class="form-check-input" value="1" <?= $product['TrangThai'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="TrangThai">Hiển thị sản phẩm</label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Cập nhật Sản phẩm</button>
                    <a href="<?= BASE_URL ?>product/index" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
/* CSS cho form sửa sản phẩm */
.product-form {
    max-width: 800px; /* Giới hạn chiều rộng form */
    margin: 0 auto; /* Căn giữa form */
    padding: 20px;
    background-color: var(--card-bg); /* Nền form cùng màu với card */
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-light);
}

.form-control,
.form-select,
.form-control-file {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid var(--border-color);
    border-radius: 5px;
    background-color: var(--sidebar-bg-dark); /* Nền input tối */
    color: var(--text-light);
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-control:focus,
.form-select:focus,
.form-control-file:focus {
    border-color: var(--purple-main); /* Border focus màu tím */
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(138, 72, 240, 0.25); /* Shadow nhẹ khi focus */
}

.form-control-file {
    padding-top: 10px; /* Điều chỉnh padding cho input file */
}

textarea.form-control {
    resize: vertical; /* Cho phép thay đổi chiều cao của textarea */
}

/* Checkbox styling */
.form-check {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.form-check-input {
    width: 20px; /* Kích thước checkbox */
    height: 20px;
    margin-right: 10px;
    cursor: pointer;
    appearance: none; /* Ẩn checkbox mặc định */
    -webkit-appearance: none;
    border: 2px solid var(--border-color);
    border-radius: 4px;
    background-color: var(--sidebar-bg-dark);
    position: relative;
    transition: background-color 0.2s ease, border-color 0.2s ease;
}

.form-check-input:checked {
    background-color: var(--purple-main); /* Màu khi được chọn */
    border-color: var(--purple-main);
}

.form-check-input:checked::after {
    content: '\2713'; /* Dấu tick */
    font-size: 14px;
    color: #fff;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.form-check-label {
    cursor: pointer;
    color: var(--text-light);
    font-weight: normal;
}

/* Image preview */
.current-image-preview {
    margin-top: 10px;
    margin-bottom: 15px;
    text-align: center;
}

.current-image-preview img {
    max-width: 200px;
    height: auto;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.current-image-preview p {
    margin-top: 5px;
    font-size: 0.85rem;
    color: var(--text-dim);
}

/* Form actions (buttons) */
.form-actions {
    display: flex;
    justify-content: flex-end; /* Căn phải các nút */
    gap: 15px; /* Khoảng cách giữa các nút */
    margin-top: 30px;
}

.btn-secondary {
    background-color: #6c757d;
    color: #fff;
    border: none;
}

.btn-secondary:hover {
    background-color: #5a6268;
}
</style>
