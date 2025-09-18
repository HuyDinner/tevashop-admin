<?php

?>
<body>
   <div class="dashboard-container">
        <?php include VIEWS_PATH . 'includes/sidebar.php'; ?>

    <main class="main-content">
        <?php include VIEWS_PATH . 'includes/header.php'; ?>
  
        
        <div class="product-list-container">
            <?php if (!empty($topSellingProducts)): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>HÌNH ẢNH</th>
                        <th>TÊN SẢN PHẨM</th>
                        <th>TỔNG SỐ LƯỢNG ĐÃ BÁN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topSellingProducts as $product): ?>
                        <tr>
                            <td>
                                <!-- Giả định IMAGE_PATH đã được định nghĩa để trỏ đến thư mục chứa hình ảnh -->
                                <img src="<?= htmlspecialchars(IMAGES_PATH . $product['HinhAnh']) ?>" alt="<?= htmlspecialchars($product['TenSanPham']) ?>" class="product-image">
                            </td>
                            <td><?= htmlspecialchars($product['TenSanPham']) ?></td>
                            <td><?= htmlspecialchars($product['total_sold'] ?? '0') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>Không có sản phẩm nào bán chạy.</p>
            <?php endif; ?>
        </div>
    </div>
</main>
<style>
    .product-list-container {
        background-color: #28243D;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .data-table th, .data-table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    .data-table th {
        background-color: #201E35;
        font-weight: bold;
        color: #586fe4ff;
    }
    .data-table tbody tr:hover {
        background-color: #323048;
    }
    .product-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
    }
    td:nth-child(2) {
        font-weight: 500;
    }
</style>
