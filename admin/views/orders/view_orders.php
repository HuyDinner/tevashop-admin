<?php
// admin/views/orders/view_orders.php

// Kiểm tra biến $orders đã được truyền từ controller chưa
if (!isset($orders)) {
    $orders = []; // Khởi tạo mảng rỗng nếu không có dữ liệu
}
?>
<body>
   <div class="dashboard-container">
        <?php include VIEWS_PATH . 'includes/sidebar.php'; ?>

    <main class="main-content">
        <?php include VIEWS_PATH . 'includes/header.php'; ?>

<div class="card full-width-card">
            <div class="card-top-area">
                <div class="card-header">
                    <h3>Danh sách Đơn hàng</h3>
                </div>
            </div>
    <div class="card-body">
        <?php
        // Hiển thị flash message (nếu có)
        if (isset($_SESSION['message'])) {
            $message_type = $_SESSION['message_type'] ?? 'info';
            echo '<div class="alert alert-' . $message_type . ' alert-dismissible fade show" role="alert">';
            echo $_SESSION['message'];
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Người nhận</th>
                        <th>SĐT người nhận</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['id']) ?></td>
                                <td><?= htmlspecialchars($order['ten_nguoi_nhan'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($order['sdt_nguoi_nhan'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($order['ngay_dat_hang']) ?></td>
                                <td><?= number_format($order['tong_tien'], 0, ',', '.') ?> VNĐ</td>
                                <td>
                                    <span class="badge bg-info"><?= htmlspecialchars($order['trang_thai_don_hang']) ?></span>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>order/detail/<?= $order['id'] ?>" class="btn btn-sm btn-info" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center">Không có đơn hàng nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</main>

</body>
