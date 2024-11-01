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
<?php require_once("footer.php"); ?>