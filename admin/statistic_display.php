<?php require_once('header.php'); ?>
<?php
$startDate = isset($startDate) ? $startDate : '';
$endDate = isset($endDate) ? $endDate : '';
$stats = isset($stats) ? $stats : null;  // Initialize $stats

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
                <form method="GET" action="statistic_processing.php" autocomplete="off">
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
</body>

</html>

<?php require_once("footer.php"); ?>