<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TEVA - Tổng quan thống kê</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?= CSS_PATH ?>styles.css"> <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <?php include VIEWS_PATH . 'includes/sidebar.php'; ?>

        <main class="main-content">
            <?php include VIEWS_PATH . 'includes/header.php'; ?>

            <section class="overview-cards">
                <div class="card">
                    <div class="card-icon"><i class="fas fa-clipboard-list"></i></div>
                    <div class="card-content">
                        <h3><?= htmlspecialchars($totalOrders) ?></h3>
                        <p>Tổng đơn hàng</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-icon"><i class="fas fa-users"></i></div>
                    <div class="card-content">
                        <h3><?= htmlspecialchars($totalCustomers) ?></h3>
                        <p>Tổng khách hàng</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-icon"><i class="fas fa-dollar-sign"></i></div>
                    <div class="card-content">
                        <h3>$ <?= htmlspecialchars($totalRevenue) ?></h3>
                        <p>Tổng doanh thu</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-icon"><i class="fas fa-hamburger"></i></div>
                    <div class="card-content">
                        <h3><?= htmlspecialchars($totalProducts) ?></h3>
                        <p>Tổng sản phẩm</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-icon"><i class="fas fa-user-tie"></i></div>
                    <div class="card-content">
                        <h3><?= htmlspecialchars($totalWorkers) ?></h3>
                        <p>Tổng nhân viên</p>
                    </div>
                </div>
            </section>

            <section class="dashboard-widgets">
                <div class="widget order-summary">
                    <h4>Tổng quan đơn hàng</h4>
                    <div class="time-filter">Hôm nay <i class="fas fa-chevron-down"></i></div>
                    <div class="charts">
                        <div class="chart-item">
                            <canvas id="onDeliveryChart"></canvas>
                            <p>Đang giao</p>
                        </div>
                        <div class="chart-item">
                            <canvas id="deliveredChart"></canvas>
                            <p>Đã giao</p>
                        </div>
                        <div class="chart-item">
                            <canvas id="cancelledChart"></canvas>
                            <p>Đã hủy</p>
                        </div>
                    </div>
                </div>

                <div class="widget overview">
                    <h4>Tổng quan</h4>
                    <div class="time-filter">Xem tất cả</div>
                    <div class="overview-content">
                        <canvas id="mealTimeChart"></canvas>
                        <ul class="legend">
                            <li><span class="color-box morning"></span>Sáng <span><?= htmlspecialchars($mealTimeData['morning']) ?>%</span></li>
                            <li><span class="color-box afternoon"></span>Chiều <span><?= htmlspecialchars($mealTimeData['afternoon']) ?>%</span></li>
                            <li><span class="color-box evening"></span>Tối <span><?= htmlspecialchars($mealTimeData['evening']) ?>%</span></li>
                            <li><span class="color-box night"></span>Đêm <span><?= htmlspecialchars($mealTimeData['night']) ?>%</span></li>
                        </ul>
                    </div>
                </div>

                <div class="widget top-selling-items">
                    <h4>Món bán chạy nhất</h4>
                    <div class="time-filter"><a href="<?= BASE_URL ?>product/top_selling">Xem tất cả</a></div>
                    <ul>
                        <?php foreach ($topSellingItems as $item): ?>
                            <li>
                                <span><?= htmlspecialchars($item['TenSanPham'] ?? 'N/A') ?></span>
                            </li>
                        <?php endforeach; ?>
                        <?php if (empty($topSellingItems)): ?>
                            <li>Không có món nào bán chạy.</li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="widget customer-map">
                    <h4>Bản đồ khách hàng</h4>
                    <div class="time-filter">Hàng tuần <i class="fas fa-chevron-down"></i></div>
                    <canvas id="customerChart"></canvas>
                    <div class="chart-legend">
                        <span class="this-week-legend"></span> Tuần này
                        <span class="last-week-legend"></span> Tuần trước
                    </div>
                </div>

                <div class="widget total-revenue">
                    <h4>Tổng doanh thu</h4>
                    <canvas id="revenueChart"></canvas>
                </div>
            </section>
        </main>
    </div>

    <script>
        // Truyền dữ liệu PHP vào JavaScript
        const orderStatusCounts = <?= json_encode($orderStatusCounts) ?>;
        const mealTimeData = <?= json_encode($mealTimeData) ?>;
        const customerChartData = <?= json_encode($customerChartData) ?>;
        const revenueChartData = <?= json_encode($revenueChartData) ?>;

        const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: Object.values(revenueChartData),
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Doanh thu'
                    },
                    ticks: {
                        callback: function(value, index, ticks) {
                            return new Intl.NumberFormat('vi-VN').format(value);
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Tháng'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' VNĐ';
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
    </script>
    <script src="<?= JS_PATH ?>chart.js"></script> </body>
</html>
