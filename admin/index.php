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
$statement = $pdo->prepare("SELECT product_name, SUM(quantity) AS total_quantity_sold FROM tbl_order GROUP BY product_id, product_name ORDER BY total_quantity_sold DESC LIMIT 1");
$statement->execute();
$top_selling_product = $statement->fetch(PDO::FETCH_ASSOC);
$top_product_name = $top_selling_product['product_name'];
$total_most_product_quantity_sold = $top_selling_product['total_quantity_sold'];
// Thống kê hàng bán ế nhất
$statement = $pdo->prepare("SELECT product_name, SUM(quantity) AS total_quantity_sold FROM tbl_order GROUP BY product_id, product_name ORDER BY total_quantity_sold ASC LIMIT 1");
$statement->execute();
$less_selling_product = $statement->fetch(PDO::FETCH_ASSOC);
$less_product_name = $less_selling_product['product_name'];
$total_less_product_quantity_sold = $less_selling_product['total_quantity_sold'];

// Thống kê khách hàng VIP
$statement = $pdo->prepare("SELECT c.cust_name, SUM(p.paid_amount) AS total_spent FROM tbl_payment p JOIN tbl_customer c ON p.customer_id = c.cust_id GROUP BY c.cust_id HAVING total_spent > 51000000");
$statement->execute();
$vip_customers = $statement->fetchAll(PDO::FETCH_ASSOC);
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
			<span class="info-box-icon bg-blue"><i class="fa fa-hand-o-right"></i></span>
			<div class="info-box-content">
				<span class="info-box-text">Khách hàng VIP</span>
				<span class="info-box-number">
					<?php foreach ($vip_customers as $vip) {
						echo $vip['cust_name'] . ' (Tổng: ' . number_format($vip['total_spent'], 0, ',', '.') . ' đ)<br>';
					} ?>
				</span>

			</div>
		</div>
	</div>

</section>

<?php require_once('footer.php'); ?>