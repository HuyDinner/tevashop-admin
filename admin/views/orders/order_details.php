<?php
// admin/views/orders/order_details.php

// Kiểm tra biến $order và $orderStatuses đã được truyền từ controller chưa
if (!isset($order) || !isset($orderStatuses)) {
    // Có thể chuyển hướng hoặc hiển thị lỗi
    echo '<div class="alert alert-danger">Không thể tải chi tiết đơn hàng.</div>';
    return;
}
?>
<body>
   <div class="dashboard-container">
        <?php include VIEWS_PATH . 'includes/sidebar.php'; ?>

    <main class="main-content">
        <?php include VIEWS_PATH . 'includes/header.php'; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <a href="<?= BASE_URL ?>order/index" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Quay lại Danh sách
        </a>
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

        <div class="row">
            <div class="col-md-6">
                <h3>Thông tin Đơn hàng</h3>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Ngày đặt:</strong> <?= htmlspecialchars($order['ngay_dat_hang']) ?></li>
                    <li class="list-group-item"><strong>Tổng tiền:</strong> <span class="text-success fw-bold"><?= number_format($order['tong_tien'], 0, ',', '.') ?> VNĐ</span></li>
                    <li class="list-group-item">
                        <strong>Trạng thái:</strong> <span class="badge bg-info fs-6"><?= htmlspecialchars($order['trang_thai_don_hang']) ?></span>
                        <form action="<?= BASE_URL ?>order/updateStatus/<?= $order['id'] ?>" method="POST" class="d-inline-block ms-3">
                            <div class="input-group input-group-sm">
                                <select name="trang_thai" class="form-select form-select-sm">
                                    <?php foreach ($orderStatuses as $status): ?>
                                        <option value="<?= htmlspecialchars($status) ?>" <?= ($order['trang_thai_don_hang'] == $status) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($status) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">Cập nhật</button>
                            </div>
                        </form>
                    </li>
                    <li class="list-group-item"><strong>Phương thức TT:</strong> <?= htmlspecialchars($order['phuong_thuc_thanh_toan'] ?? 'Chưa xác định') ?></li>
                    <li class="list-group-item"><strong>Ghi chú:</strong> <?= htmlspecialchars($order['ghi_chu'] ?? 'Không có') ?></li>
                </ul>
            </div>
            <div class="col-md-6">
                <h3>Thông tin Khách hàng & Người nhận</h3>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Khách hàng:</strong> <?= htmlspecialchars($order['ten_khachhang'] ?? 'N/A') ?></li>
                    <li class="list-group-item"><strong>Email KH:</strong> <?= htmlspecialchars($order['email_khachhang'] ?? 'N/A') ?></li>
                    <li class="list-group-item"><strong>Người nhận:</strong> <?= htmlspecialchars($order['ten_nguoi_nhan'] ?? 'N/A') ?></li>
                    <li class="list-group-item"><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['dia_chi_nguoi_nhan'] ?? 'N/A') ?></li>
                    <li class="list-group-item"><strong>SĐT:</strong> <?= htmlspecialchars($order['sdt_nguoi_nhan'] ?? 'N/A') ?></li>
                    <li class="list-group-item"><strong>Email người nhận:</strong> <?= htmlspecialchars($order['email_nguoi_nhan'] ?? 'N/A') ?></li>
                </ul>
            </div>
        </div>

        <h4 class="mt-4">Sản phẩm trong Đơn hàng</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tên SP</th>
                        <th>Số lượng</th>
                        <th>Giá bán (tại thời điểm đặt)</th>
                        <th>Tổng tiền SP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($order['details'])): ?>
                        <?php foreach ($order['details'] as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['ten_san_pham_hien_tai'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($item['so_luong']) ?></td>
                                <td><?= number_format($item['gia_ban'], 0, ',', '.') ?> VNĐ</td>
                                <td><?= number_format($item['so_luong'] * $item['gia_ban'], 0, ',', '.') ?> VNĐ</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Không có sản phẩm nào trong đơn hàng này.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('updateStatusForm');
        const select = document.getElementById('trangThaiSelect');

        form.addEventListener('submit', function(e) {
            const selectedStatus = select.options[select.selectedIndex].value;

            // Kiểm tra nếu trạng thái được chọn là "Đã giao hàng"
            if (selectedStatus === "Đã giao hàng") {
                // Không cần ngăn chặn hành vi mặc định của form
                // Form sẽ được gửi đi ngay lập tức
            }
        });
    });
</script>

</body>
<style>

.row {
    display: flex;
    padding-left: 180px;
    margin-bottom: 50px;
}

.col-md-6 {
    flex: 1;
    padding: 10px;
}

.col-md-6>h3 {
    margin-top: 10px;
    margin-bottom: 15px;
    margin-left: -30px;
}

.mt-4 {
    text-decoration: underline;
    text-align: center;
    margin-bottom: 20px;
}

.btn-sm {
    background-color: #50b5af;
    color: #f0f0f0;
}
</style>
