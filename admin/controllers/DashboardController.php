<?php
require_once APP_PATH . 'models/Dashboard.php';

class DashboardController {
    private $dashboard;

    public function __construct() {
        $this->dashboard = new Dashboard(); // Khởi tạo model Dashboard
    }

    public function index() {
        // Lấy dữ liệu từ Model
        $totalOrders = $this->dashboard->getTotalOrders();
        $totalCustomers = $this->dashboard->getTotalCustomers();
        $totalRevenue = $this->dashboard->getTotalRevenue();
        $totalProducts = $this->dashboard->getTotalProducts();
        $totalWorkers = $this->dashboard->getTotalWorkers();
        $orderStatusCounts = $this->dashboard->getOrderStatusCounts();
        $topSellingItems = $this->dashboard->getTopSellingItems();
        $mealTimeData = $this->dashboard->getOrderTimeDistribution();
        $customerChartData = $this->dashboard->getCustomerGrowthData();
        $revenueChartData = $this->dashboard->getMonthlyRevenueData();
        $customerChartData = $this->dashboard->getCustomerGrowthData(); 
        $revenueChartData = $this->dashboard->getMonthlyRevenueData(); 
        $pageTitle = "Tổng quan Thống kê";
        $pageIcon = "fas fa-th-large";

        $controllerName = "Dashboard";

        // Truyền dữ liệu đến View
        $data = [
            'totalOrders' => $totalOrders,
            'totalCustomers' => $totalCustomers,
            'totalRevenue' => $totalRevenue,
            'totalProducts' => $totalProducts,
            'totalWorkers' => $totalWorkers,
            'orderStatusCounts' => $orderStatusCounts,
            'topSellingItems' => $topSellingItems,
            'mealTimeData' => $mealTimeData,
            'customerChartData' => $customerChartData,
            'revenueChartData' => $revenueChartData,
        ];
        extract($data);
        require_once VIEWS_PATH . 'dashboard.php';
    }
}
