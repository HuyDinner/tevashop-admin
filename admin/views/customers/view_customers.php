<?php
// admin/views/customers/view_customers.php

if (!isset($customers) || !isset($pageTitle) || !isset($pageIcon) || !isset($controllerName)) {
    echo "Lỗi: Dữ liệu khách hàng không đầy đủ để hiển thị.";
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
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Khách hàng</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Địa chỉ</th>
                            <th>Ngày đăng ký</th>
                            <th>Trạng Thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($customers)): ?>
                            <?php foreach ($customers as $customer): ?>
                                <tr>
                                    <td><?= htmlspecialchars($customer['Id']) ?></td>
                                    <td><?= htmlspecialchars($customer['TenKhachHang']) ?></td>
                                    <td><?= htmlspecialchars($customer['Email']) ?></td>
                                    <td><?= htmlspecialchars($customer['SoDienThoai']) ?></td>
                                    <td><?= htmlspecialchars($customer['DiaChi']) ?></td>
                                    <td><?= htmlspecialchars($customer['NgayDangKy']) ?></td>
                                    <td>
                                        <?php if ($customer['TrangThai']): ?>
                                            <span class="badge bg-success">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Khóa</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= BASE_URL ?>customer/delete?id=<?= htmlspecialchars($customer['Id']) ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa khách hàng này không?');">Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Không có khách hàng nào.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</main>

</body>
