<?php require_once('header.php'); ?>
<?php
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';

$stats = null;
$chartData = null;
if (!empty($startDate) && !empty($endDate)) {
    try {
        $startDateTime = DateTime::createFromFormat('d/m/Y', $startDate);
        $endDateTime = DateTime::createFromFormat('d/m/Y', $endDate);

        if ($startDateTime && $endDateTime) {
            $startDateFormatted = $startDateTime->format('Y-m-d');
            $endDateFormatted = $endDateTime->format('Y-m-d');

            // Query for overall statistics
            $sql = "
                SELECT 
                    COUNT(id) AS total_orders,
                    SUM(quantity) AS total_quantity,
                    SUM(Total) AS total_sales
                FROM tbl_order
                WHERE buy_date BETWEEN :startDate AND :endDate
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':startDate', $startDateFormatted, PDO::PARAM_STR);
            $stmt->bindParam(':endDate', $endDateFormatted, PDO::PARAM_STR);
            $stmt->execute();
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);

            // Query for daily order totals (for the chart)
            $chartSql = "
                SELECT 
                    DATE(buy_date) as date,
                    COUNT(id) AS daily_orders,
                    SUM(Total) AS daily_revenue,
                    SUM(quantity) AS daily_quantity
                FROM tbl_order
                WHERE buy_date BETWEEN :startDate AND :endDate
                GROUP BY DATE(buy_date)
                ORDER BY DATE(buy_date)
            ";

            $chartStmt = $pdo->prepare($chartSql);
            $chartStmt->bindParam(':startDate', $startDateFormatted, PDO::PARAM_STR);
            $chartStmt->bindParam(':endDate', $endDateFormatted, PDO::PARAM_STR);
            $chartStmt->execute();
            $chartData = $chartStmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $error = "Invalid date format. Please use dd/mm/yyyy.";
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Statistics</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body class="bg-light">
    <div class="container py-5">
        <h1 class="text-center mb-5">Thống kê đặt hàng</h1>

        <div class="card mb-5">
            <div class="card-body">
                <h2 class="card-title mb-4">Chọn thời gian</h2>
                <form method="GET" action="" autocomplete="off">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label for="startDate" class="form-label">ngày từ:</label>
                            <input type="text" name="startDate" id="startDate"
                                value="<?php echo htmlspecialchars($startDate); ?>" required
                                class="form-control datepicker" placeholder="dd/mm/yyyy">
                        </div>
                        <div class="col-md-5">
                            <label for="endDate" class="form-label">đến ngày:</label>
                            <input type="text" name="endDate" id="endDate"
                                value="<?php echo htmlspecialchars($endDate); ?>" required
                                class="form-control datepicker" placeholder="dd/mm/yyyy">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Xem thống kê</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($stats): ?>
            <div class="card mb-5">
                <div class="card-body">
                    <h2 class="card-title mb-4">Thống kê từ <?php echo htmlspecialchars($startDate); ?> đến
                        <?php echo htmlspecialchars($endDate); ?></h2>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h3 class="card-title">Tổng đơn đặt</h3>
                                    <p class="card-text display-4"><?php echo number_format($stats['total_orders'] ?: 0); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h3 class="card-title">Tổng số lượng bán</h3>
                                    <p class="card-text display-4">
                                        <?php echo number_format($stats['total_quantity'] ?: 0); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h3 class="card-title">Tổng tiền thu về</h3>
                                    <p class="card-text display-4">
                                        <?php echo number_format($stats['total_sales'] ?: 0, 2); ?> VNĐ</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-5">
                <div class="card-body">
                    <canvas id="orderChart"></canvas>
                    <canvas id="revenueChart" class="mt-4"></canvas>
                    <canvas id="quantityChart" class="mt-4"></canvas>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr(".datepicker", {
                dateFormat: "d/m/Y",
                allowInput: true
            });

            <?php if ($chartData): ?>
                var chartData = <?php echo json_encode($chartData); ?>;

                var dates = chartData.map(item => item.date);
                var dailyOrders = chartData.map(item => item.daily_orders);
                var dailyRevenue = chartData.map(item => item.daily_revenue);
                var dailyQuantity = chartData.map(item => item.daily_quantity);

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
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Đặt hàng'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Thời gian'
                                }
                            }
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
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'doanh thu'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Thời gian'
                                }
                            }
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
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Số lượng'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Thời gian'
                                }
                            }
                        }
                    }
                });
            <?php endif; ?>
        });
    </script>
</body>

</html>
<?php require_once('footer.php'); ?>