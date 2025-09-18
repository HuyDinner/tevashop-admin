<?php
// admin/views/reviews/index.php

// Kiểm tra biến $reviews đã được truyền từ controller chưa
if (!isset($reviews)) {
    $reviews = []; // Khởi tạo mảng rỗng nếu không có dữ liệu
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
                  <h3>Danh sách Đánh giá</h3>
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
                        <th>Sản phẩm</th>
                        <th>Khách hàng</th>
                        <th>Email KH</th>
                        <th>Số sao</th>
                        <th>Nội dung</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td><?= htmlspecialchars($review['id']) ?></td>
                                <td><?= htmlspecialchars($review['ma_sanpham'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($review['ma_khachhang'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($review['email_khachhang'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($review['so_sao'] ?? 'N/A') ?> <i class="fas fa-star text-warning"></i></td>
                                <td><?= htmlspecialchars(mb_strimwidth($review['noi_dung'] ?? 'Không có nội dung', 0, 100, "...")) ?></td>
                                <td><?= htmlspecialchars($review['ngay_tao'] ?? 'N/A') ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>review/delete/<?= $review['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa đánh giá này không?');" title="Xóa đánh giá">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Không có đánh giá nào.</td> </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</main>

</body>
