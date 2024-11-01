document.addEventListener('DOMContentLoaded', function() {
    flatpickr(".datepicker", {
        dateFormat: "d/m/Y",
        allowInput: true
    });

    if (chartData && chartData.length > 0) {  // Check if chartData is available and non-empty
        const dates = chartData.map(item => item.date);
        const dailyOrders = chartData.map(item => item.daily_orders);
        const dailyRevenue = chartData.map(item => item.daily_revenue);
        const dailyQuantity = chartData.map(item => item.daily_quantity);

        // Daily Orders Chart
        new Chart(document.getElementById('orderChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Biểu đồ',
                    data: dailyOrders,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Đặt hàng' } },
                    x: { title: { display: true, text: 'Thời gian' } }
                }
            }
        });

        // Daily Revenue Chart
        new Chart(document.getElementById('revenueChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Biểu đồ tiền thu (VNĐ)',
                    data: dailyRevenue,
                    borderColor: 'rgb(54, 162, 235)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'doanh thu' } },
                    x: { title: { display: true, text: 'Thời gian' } }
                }
            }
        });

        // Daily Quantity Chart
        new Chart(document.getElementById('quantityChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Biểu đồ tổng số lượng hàng đã bán',
                    data: dailyQuantity,
                    borderColor: 'rgb(255, 99, 132)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Số lượng' } },
                    x: { title: { display: true, text: 'Thời gian' } }
                }
            }
        });
    }
});
