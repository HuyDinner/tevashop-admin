<?php
// admin/views/includes/sidebar.php
// Định nghĩa URL cơ sở để các liên kết hoạt động đúng
$baseUrl = '/tevashop-admin/admin/'; // Thay đổi nếu đường dẫn gốc khác
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <img src="<?= BASE_URL ?>assets/images/logo/tevashop-logo.png" alt="TEVA Logo" 
        style="display: block; 
               width: 150px;
               max-width: 70%; 
               height: auto; 
               margin-left: 35px;
               margin-top: 5px;">
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li class="<?= (isset($controllerName) && $controllerName == 'Dashboard') ? 'active' : '' ?>">
                <a href="<?= $baseUrl ?>dashboard/index"><i class="fas fa-chart-line"></i> Tổng quan thống kê</a>
            </li>
            <li class="<?= (isset($controllerName) && $controllerName == 'Product') ? 'active' : '' ?>">
                <a href="<?= $baseUrl ?>product/index"><i class="fas fa-box"></i> Quản lý sản phẩm</a>
            </li>
            <li class="<?= (isset($controllerName) && $controllerName == 'Category') ? 'active' : '' ?>">
                <a href="<?= $baseUrl ?>category/index"><i class="fas fa-list-alt"></i> Quản lý danh mục</a>
            </li>
            <li class="<?= (isset($controllerName) && $controllerName == 'Customer') ? 'active' : '' ?>">
                <a href="<?= $baseUrl ?>customer/index"><i class="fas fa-users"></i> Quản lý khách hàng</a>
            </li>
            <li class="<?= (isset($controllerName) && $controllerName == 'Review') ? 'active' : '' ?>">
                <a href="<?= $baseUrl ?>review/index"><i class="fas fa-star"></i> Quản lý đánh giá</a>
            </li>
            <li class="<?= (isset($controllerName) && $controllerName == 'Order') ? 'active' : '' ?>">
                <a href="<?= $baseUrl ?>order/index"><i class="fas fa-clipboard-list"></i> Quản lý đơn hàng</a>
            </li>
            <li class="nav-item">
                <a href="<?= BASE_URL ?>auth/logout" class="nav-link"><i class="fas fa-sign-out-alt"></i>Đăng xuất</a>
            </li>
        </ul>
    </nav>
</aside>
