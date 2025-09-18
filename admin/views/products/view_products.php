<?php
// admin/views/products/view_products.php

// Dữ liệu $products đã được truyền từ ProductController
// Không cần require_once db_config.php hay khởi tạo kết nối database ở đây nữa

// Các CSS inline trong file gốc của bạn, chúng ta sẽ chuyển sang styles.css sau
// hoặc giữ lại nếu chỉ dùng cho trang này
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
                    <h3>Danh sách Sản phẩm</h3>
                </div>
                <div class="add-button">
                    <a href="<?= BASE_URL ?>product/add" class="btn btn-primary">Thêm Sản phẩm Mới</a>
                </div>
            </div>
            
            <div class="card-body"> 
                <?php if (!empty($products)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên Sản phẩm</th>
                                    <th>Danh mục</th>
                                    <th>Giá Gốc</th>
                                    <th>Số Lượng</th>
                                    <th>Trạng Thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['Id']); ?></td>
                                        <td><?php echo htmlspecialchars($product['TenSanPham']); ?></td>
                                        <td><?php echo htmlspecialchars($product['TenDanhMuc'] ?? 'N/A'); ?></td>
                                        <td><?php echo number_format($product['GiaGoc'], 0, ',', '.'); ?> VNĐ</td>
                                        <td><?php echo htmlspecialchars($product['SoLuong']); ?></td>
                                        <td><?php echo $product['TrangThai'] ? '<span class="badge bg-success">Hiển thị</span>' : '<span class="badge bg-danger">Ẩn</span>'; ?></td>
                                        <td>
                                            <a href="<?= BASE_URL ?>product/edit?id=<?php echo $product['Id']; ?>" class="btn btn-sm btn-info">Sửa</a>
                                            <a href="<?= BASE_URL ?>product/delete?id=<?php echo $product['Id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');">Xóa</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center">Không có sản phẩm nào.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
<style>
/* --- CONTAINER FLUID (Nội dung chính) --- */
.container-fluid.dashboard-content {
    padding: 0;
}

/* --- CARD STYLING (Hộp chứa nội dung) --- */
.card.full-width-card {
    background-color: #28243D;
    color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    padding: 20px; /* Thêm padding bên trong card */
}

/* --- MỚI: Wrapper cho tiêu đề và nút thêm sản phẩm --- */
.card-top-controls {
    display: flex; /* Kích hoạt Flexbox */
    justify-content: space-between; /* Đẩy tiêu đề sang trái, nút sang phải */
    align-items: center; /* Căn giữa theo chiều dọc */
    margin-bottom: 20px; /* Khoảng cách giữa phần đầu và bảng */
    padding-bottom: 15px; /* Padding dưới cho đường kẻ */
    border-bottom: 1px solid #444; /* Đường kẻ phân cách */
}

/* --- CARD HEADER (chỉ chứa h3) --- */
.card-header {
    /* Loại bỏ các thuộc tính flexbox ở đây vì chúng ta đã dùng card-top-controls */
    display: block; /* Đảm bảo nó không còn là flex container con nữa */
    margin: 0; /* Đảm bảo không có margin thừa đẩy ra */
    padding: 0; /* Bỏ padding nếu nó đã được xử lý bởi card-top-controls */
    border-bottom: none; /* Bỏ border-bottom ở đây nếu đã có ở card-top-controls */
}

.card-header h3 {
    margin: 0; /* Quan trọng: Bỏ margin mặc định của h3 */
    font-size: 1.5rem;
    color: #fff;
}

/* --- CARD BODY (Nội dung chính của card) --- */
.card-body {
    padding: 0; /* Bỏ padding mặc định của card-body để bảng không bị thừa padding */
}

/* --- TABLE STYLING --- */
.table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 0;
    color: #fff;
}

.table th,
.table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #444;
}

.table thead th {
    background-color: #201E35;
    color: #586fe4ff;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 0.9rem;
}

.table tbody tr:hover {
    background-color: #323048;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: #28243D;
}

.table-responsive {
    overflow-x: auto;
}

/* --- BUTTON STYLING --- */
.btn {
    padding: 8px 15px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease, color 0.3s ease;
    display: inline-block;
}

.btn-primary {
    background-color: #17a2b8;
    color: #fff;
    border: none;
}

.btn-primary:hover {
    background-color: #138496;
}

.btn-info {
    background-color: #17a2b8;
    color: #fff;
    border: none;
}
.btn-info:hover {
    background-color: #138496;
}

.btn-danger {
    background-color: #dc3545;
    color: #fff;
    border: none;
}

.btn-danger:hover {
    background-color: #c82333;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 0.8rem;
}

/* --- BADGES FOR STATUS --- */
.badge {
    padding: 0.35em 0.65em;
    font-size: 0.75em;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25rem;
    display: inline-block;
}

.bg-success {
    background-color: #28a745 !important;
    color: #fff;
}

.bg-danger {
    background-color: #dc3545 !important;
    color: #fff;
}

/* --- TEXT ALIGNMENT --- */
.text-center {
    text-align: center;
    color: #bbb;
    padding: 20px;
}
</style>
