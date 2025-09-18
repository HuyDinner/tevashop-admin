<?php
// admin/views/categories/view_categories.php

// Dữ liệu $categories sẽ được truyền từ CategoryController
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
                    <h3>Danh sách Danh mục</h3>
                </div>
            <div class="add-button">
                <a href="<?= BASE_URL ?>category/add" class="btn btn-primary">Thêm Danh mục Mới</a>
            </div>
            </div>
            <div class="card-body">
                <?php if (!empty($categories)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên Danh mục</th>
                                    <th>Trạng Thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($category['Id']); ?></td>
                                        <td><?php echo htmlspecialchars($category['TenDanhMuc']); ?></td>
                                        <td><?php echo $category['TrangThai'] ? 'Hiển thị' : 'Ẩn'; ?></td>
                                        <td>
                                            <a href="<?= BASE_URL ?>category/edit?id=<?php echo $category['Id']; ?>" class="btn btn-sm btn-warning">Sửa</a>
                                            <a href="<?= BASE_URL ?>category/delete?id=<?php echo $category['Id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này không?');">Xóa</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center">Không có danh mục nào.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

</body>
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
    display: block; 
    margin: 0; 
    padding: 0; 
    border-bottom: none; 
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
    background-color: #353545;
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

.btn-warning {
    background-color: #17a2b8;
    color: #fff;
    border: none;
}
.btn-warning:hover {
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
