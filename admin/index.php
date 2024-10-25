<?php require_once('header.php'); ?>

<section class="content-header">
	<h1>Dashboard</h1>
</section>

<?php
// Thống kê số lượng các danh mục và sản phẩm
$statement = $pdo->prepare("SELECT * FROM tbl_top_category");
$statement->execute();
$total_top_category = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_mid_category");
$statement->execute();
$total_mid_category = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_end_category");
$statement->execute();
$total_end_category = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_product");
$statement->execute();
$total_product = $statement->rowCount();

// Thống kê đơn hàng
$statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE payment_status=?");
$statement->execute(array('Completed'));
$total_order_completed = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE shipping_status=?");
$statement->execute(array('Completed'));
$total_shipping_completed = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE payment_status=?");
$statement->execute(array('Pending'));
$total_order_pending = $statement->rowCount();

$statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE payment_status=? AND shipping_status=?");
$statement->execute(array('Completed', 'Pending'));
$total_order_complete_shipping_pending = $statement->rowCount();

// Thống kê số hàng đã bán
$statement = $pdo->prepare("SELECT SUM(quantity) AS total_sold FROM tbl_order");
$statement->execute();
$result = $statement->fetch(PDO::FETCH_ASSOC);
$total_sold = $result['total_sold'];

// Thống kê hàng bán chạy nhất
$statement = $pdo->prepare("
    SELECT o.product_name, SUM(o.quantity) AS total_quantity_sold 
    FROM tbl_order o
    JOIN tbl_product p ON o.product_id = p.p_id
    GROUP BY o.product_id, o.product_name 
    ORDER BY total_quantity_sold DESC LIMIT 1
");
$statement->execute();
$top_selling_product = $statement->fetch(PDO::FETCH_ASSOC);
$top_product_name = $top_selling_product['product_name'];
$total_most_product_quantity_sold = $top_selling_product['total_quantity_sold'];
// Thống kê hàng bán ế nhất
$statement = $pdo->prepare("
    SELECT o.product_name, SUM(o.quantity) AS total_quantity_sold 
    FROM tbl_order o
    JOIN tbl_product p ON o.product_id = p.p_id
    GROUP BY o.product_id, o.product_name 
    ORDER BY total_quantity_sold ASC LIMIT 1
");
$statement->execute();
$less_selling_product = $statement->fetch(PDO::FETCH_ASSOC);
$less_product_name = $less_selling_product['product_name'];
$total_less_product_quantity_sold = $less_selling_product['total_quantity_sold'];

// Thống kê khách hàng VIP
$statement = $pdo->prepare("SELECT c.cust_name, SUM(p.paid_amount) AS total_spent FROM tbl_payment p JOIN tbl_customer c ON p.customer_id = c.cust_id GROUP BY c.cust_id HAVING total_spent > 51000000");
$statement->execute();
$vip_customers = $statement->fetchAll(PDO::FETCH_ASSOC);

// Thống kê tổng doanh thu
$statement = $pdo->prepare("SELECT SUM(paid_amount) AS total_revenue FROM tbl_payment WHERE payment_status = 'Completed'");
$statement->execute();
$result = $statement->fetch(PDO::FETCH_ASSOC);
$total_revenue = $result['total_revenue'];

// Thống kê giá trị trung bình đơn hàng (AOV)
$statement = $pdo->prepare("SELECT AVG(paid_amount) AS avg_order_value FROM tbl_payment WHERE payment_status = 'Completed'");
$statement->execute();
$result = $statement->fetch(PDO::FETCH_ASSOC);
$avg_order_value = $result['avg_order_value'];

// Thống kê tỷ lệ khách hàng quay lại
$statement = $pdo->prepare("SELECT COUNT(DISTINCT customer_id) AS total_customers FROM tbl_payment WHERE payment_status = 'Completed'");
$statement->execute();
$total_customers = $statement->fetch(PDO::FETCH_ASSOC)['total_customers'];

$statement = $pdo->prepare("SELECT COUNT(DISTINCT customer_id) AS returning_customers FROM tbl_payment WHERE payment_status = 'Completed' AND customer_id IN (SELECT customer_id FROM tbl_payment GROUP BY customer_id HAVING COUNT(*) > 1)");
$statement->execute();
$returning_customers = $statement->fetch(PDO::FETCH_ASSOC)['returning_customers'];

$customer_retention_rate = ($returning_customers / $total_customers) * 100;

// Thống kê doanh thu theo danh mục sản phẩm
$statement = $pdo->prepare("
    SELECT c.ecat_name, SUM(o.quantity * p.p_current_price) AS total_revenue 
    FROM tbl_order o 
    JOIN tbl_product p ON o.product_id = p.p_id 
    JOIN tbl_end_category c ON p.ecat_id = c.ecat_id 
    GROUP BY c.ecat_name 
    ORDER BY total_revenue DESC
");
$statement->execute();
$category_sales = $statement->fetchAll(PDO::FETCH_ASSOC);

// Thống kê sản phẩm sắp hết hàng (Low Stock)
$statement = $pdo->prepare("
    SELECT p_name, p_qty 
    FROM tbl_product 
    WHERE p_qty <= 10 
    ORDER BY p_qty ASC
");
$statement->execute();
$low_stock_products = $statement->fetchAll(PDO::FETCH_ASSOC);

// Thống kê tổng số khách hàng đã đăng ký
$statement = $pdo->prepare("SELECT COUNT(*) AS total_customers FROM tbl_customer");
$statement->execute();
$total_customers = $statement->fetch(PDO::FETCH_ASSOC)['total_customers'];

$statement = $pdo->prepare("SELECT COUNT(*) AS total_end_categories FROM tbl_end_category");
$statement->execute();
$total_end_categories = $statement->fetch(PDO::FETCH_ASSOC)['total_end_categories'];

?>

<section class="content">
	<div class="col-md-4 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-aqua"><i class="fa fa-hand-o-right"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Số sản phẩm</span>
				<span class="info-box-number"><?php echo $total_product; ?></span>
			</div>
		</div>
	</div>

	<div class="col-md-4 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-green"><i class="fa fa-hand-o-right"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Số đơn hàng đặt thành công</span>
				<span class="info-box-number"><?php echo $total_order_completed; ?></span>
			</div>
		</div>
	</div>

	<div class="col-md-4 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-green"><i class="fa fa-hand-o-right"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Số đơn đã giao hàng</span>
				<span class="info-box-number"><?php echo $total_shipping_completed; ?></span>
			</div>
		</div>
	</div>

	<div class="col-md-4 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-yellow"><i class="fa fa-hand-o-right"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Tổng lượng hàng đã bán</span>
				<span class="info-box-number"><?php echo $total_sold; ?></span>
			</div>
		</div>
	</div>

	<div class="col-md-4 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-red"><i class="fa fa-hand-o-right"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">hàng bán chạy nhất</span>
				<span class="info-box-number"><?php echo $top_product_name; ?> (<?php echo $total_most_product_quantity_sold; ?> đã bán)</span>
			</div>
		</div>
	</div>

	<div class="col-md-4 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-red"><i class="fa fa-hand-o-right"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">hàng bán ế nhất</span>
				<span class="info-box-number"><?php echo $less_product_name; ?> (<?php echo $total_less_product_quantity_sold; ?> đã bán)</span>
			</div>
		</div>
	</div>

	<div class="col-md-4 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-blue"><i class="fa fa-dollar"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Tổng doanh thu</span>
				<span class="info-box-number"><?php echo number_format($total_revenue, 0, ',', '.'); ?> đ</span>
			</div>
		</div>
	</div>

	<div class="col-md-4 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-yellow"><i class="fa fa-shopping-cart"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Giá trị trung bình đơn hàng</span>
				<span class="info-box-number"><?php echo number_format($avg_order_value, 0, ',', '.'); ?> đ</span>
			</div>
		</div>
	</div>

	<div class="col-md-4 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-green"><i class="fa fa-retweet"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Tỷ lệ khách hàng quay lại</span>
				<span class="info-box-number"><?php echo number_format($customer_retention_rate, 2); ?>%</span>
			</div>
		</div>
	</div>
	<div class="col-md-4 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-blue"><i class="fa fa-users"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Tổng số khách hàng</span>
				<span class="info-box-number"><?php echo $total_customers ?> </span>
			</div>
		</div>
		</div>
	<div class="col-md-4 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-blue"><i class="fa fa-users"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Tổng số loại hàng</span>
				<span class="info-box-number"><?php echo $total_end_categories ?> </span>
			</div>
		</div>
		</div>
</section>

<?php require_once('footer.php'); ?>