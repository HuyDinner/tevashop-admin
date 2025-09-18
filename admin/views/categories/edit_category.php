<?php
// admin/views/categories/edit_category.php

// Đảm bảo các biến cần thiết được truyền vào view
if (!isset($category) || !isset($pageTitle) || !isset($pageIcon) || !isset($controllerName)) {
    echo "Lỗi: Dữ liệu danh mục không đầy đủ để hiển thị form sửa.";
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
            <form action="<?= BASE_URL ?>category/edit?id=<?= htmlspecialchars($category['Id']) ?>" method="POST" class="category-form">
                <div class="form-group">
                    <label for="TenDanhMuc">Tên Danh mục:</label>
                    <input type="text" id="TenDanhMuc" name="TenDanhMuc" class="form-control" value="<?= htmlspecialchars($category['TenDanhMuc']) ?>" required>
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" id="TrangThai" name="TrangThai" class="form-check-input" value="1" <?= $category['TrangThai'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="TrangThai">Hiển thị danh mục</label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Cập nhật Danh mục</button>
                    <a href="<?= BASE_URL ?>category/index" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>
</main>

</body>
