document.addEventListener('DOMContentLoaded', function() {

    // --- Biểu đồ Tổng quan đơn hàng (Order Summary) ---
    const orderColors = ['#8A48F0', '#34E0C0', '#F0A500']; // Purple, Teal, Orange

    function createPieChart(ctx, dataValue, label, color) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [label, ''], // Label cho phần dữ liệu, và phần còn lại
                datasets: [{
                    data: [dataValue, 100 - dataValue],
                    backgroundColor: [color, 'rgba(255, 255, 255, 0.1)'], // Màu dữ liệu và màu nền mờ
                    borderColor: 'transparent',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '80%', // Độ dày của donut
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false // Tắt tooltip mặc định
                    }
                },
                elements: {
                    arc: {
                        borderWidth: 0 // Bỏ border giữa các slice
                    }
                }
            },
            plugins: [{ // Plugin để hiển thị giá trị phần trăm ở giữa
                id: 'textCenter',
                beforeDraw: function(chart) {
                    const width = chart.width;
                    const height = chart.height;
                    const ctx = chart.ctx;
                    ctx.restore();
                    const fontSize = (height / 114).toFixed(2);
                    ctx.font = fontSize + "em Poppins";
                    ctx.textBaseline = "middle";
                    const text = dataValue + "%";
                    const textX = Math.round((width - ctx.measureText(text).width) / 2);
                    const textY = height / 2;
                    ctx.fillStyle = "#E0E0E0"; // Màu chữ
                    ctx.fillText(text, textX, textY);
                    ctx.save();
                }
            }]
        });
    }

    // Đảm bảo `orderStatusCounts` được truyền từ PHP
    if (typeof orderStatusCounts !== 'undefined') {
        const onDeliveryCtx = document.getElementById('onDeliveryChart').getContext('2d');
        createPieChart(onDeliveryCtx, orderStatusCounts.on_delivery, 'Đang giao', orderColors[0]);

        const deliveredCtx = document.getElementById('deliveredChart').getContext('2d');
        createPieChart(deliveredCtx, orderStatusCounts.delivered, 'Đã giao', orderColors[1]);

        const cancelledCtx = document.getElementById('cancelledChart').getContext('2d');
        createPieChart(cancelledCtx, orderStatusCounts.cancelled, 'Đã hủy', orderColors[2]);
    }


    // --- Biểu đồ Tổng quan (Meal Time Overview) ---
    if (typeof mealTimeData !== 'undefined') {
        const mealTimeCtx = document.getElementById('mealTimeChart').getContext('2d');
        new Chart(mealTimeCtx, {
            type: 'doughnut',
            data: {
                labels: ['Sáng', 'Chiều', 'Tối', 'Đêm'],
                datasets: [{
                    data: [mealTimeData.morning, mealTimeData.afternoon, mealTimeData.evening, mealTimeData.night],
                    backgroundColor: [
                        '#8A48F0', // Morning - Purple
                        '#34E0C0', // Afternoon - Teal
                        '#F0A500', // Evening - Orange
                        '#5C5B5D'  // Night - Grey
                    ],
                    borderColor: 'transparent',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false // Ẩn legend mặc định, sẽ dùng legend HTML custom
                    }
                }
            }
        });
    }

    // --- Biểu đồ Bản đồ khách hàng (Customer Map) ---
    if (typeof customerChartData !== 'undefined') {
        const customerCtx = document.getElementById('customerChart').getContext('2d');
        new Chart(customerCtx, {
            type: 'bar',
            data: {
                labels: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'], // Thứ trong tuần
                datasets: [{
                    label: 'Tuần này',
                    data: customerChartData.thisWeek,
                    backgroundColor: '#8A48F0', // Purple
                    borderRadius: 5,
                    barThickness: 10, // Độ rộng cột
                    maxBarThickness: 12
                }, {
                    label: 'Tuần trước',
                    data: customerChartData.lastWeek,
                    backgroundColor: '#B48DFF', // Light purple
                    borderRadius: 5,
                    barThickness: 10,
                    maxBarThickness: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // Ẩn legend mặc định, sẽ dùng legend HTML custom
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false // Ẩn đường lưới trục X
                        },
                        ticks: {
                            color: '#A0A0A0'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)' // Màu đường lưới mờ
                        },
                        ticks: {
                            color: '#A0A0A0',
                            callback: function(value) {
                                return value;
                            }
                        }
                    }
                }
            }
        });
    }

    // --- Biểu đồ Tổng doanh thu (Total Revenue) ---
    if (typeof revenueChartData !== 'undefined') {
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: revenueChartData.labels, // Tháng
                datasets: [{
                    label: 'Doanh thu ($)',
                    data: revenueChartData.data,
                    backgroundColor: (context) => {
                        const chart = context.chart;
                        const { ctx, chartArea } = chart;
                        if (!chartArea) {
                            return null;
                        }
                        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                        gradient.addColorStop(0, 'rgba(138, 72, 240, 0)');
                        gradient.addColorStop(1, 'rgba(138, 72, 240, 0.4)');
                        return gradient;
                    },
                    borderColor: '#8A48F0', // Màu đường line tím
                    fill: true,
                    tension: 0.4, // Đường cong mượt mà
                    pointBackgroundColor: '#8A48F0',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#8A48F0',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#A0A0A0'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#A0A0A0',
                            callback: function(value) {
                                return '$' + (value / 1000).toFixed(0) + 'K';
                            }
                        }
                    }
                }
            }
        });
    }
});
