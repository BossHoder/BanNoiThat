<?php
ob_start();
session_start();
include("inc/config.php");
include("inc/functions.php");
include("inc/CSRF_Protect.php");
$csrf = new CSRF_Protect();
$error_message = '';
$success_message = '';
$error_message1 = '';
$success_message1 = '';

// Check if the user is logged in or not
if (!isset($_SESSION['user'])) {
	header('location: login.php');
	exit;
}
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Admin Panel</title>

	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/ionicons.min.css">
	<link rel="stylesheet" href="css/datepicker3.css">
	<link rel="stylesheet" href="css/all.css">
	<link rel="stylesheet" href="css/select2.min.css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.css">
	<link rel="stylesheet" href="css/jquery.fancybox.css">
	<link rel="stylesheet" href="css/AdminLTE.min.css">
	<link rel="stylesheet" href="css/_all-skins.min.css">
	<link rel="stylesheet" href="css/on-off-switch.css" />
	<link rel="stylesheet" href="css/summernote.css">
	<link rel="stylesheet" href="style.css">

</head>

<body class="hold-transition fixed skin-blue sidebar-mini">

	<div class="wrapper">

		<header class="main-header">

			<a href="index.php" class="logo">
				<span class="logo-lg">Shop Theanhdola</span>
			</a>

			<nav class="navbar navbar-static-top">

				<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
					<span class="sr-only">Chuyển đổi nav</span>
				</a>

				<span style="float:left;line-height:50px;color:#fff;padding-left:15px;font-size:18px;">Admin Panel</span>
				<!-- Top Bar ... User Inforamtion .. Login/Log out Area -->
				<div class="navbar-custom-menu">
					<ul class="nav navbar-nav">
						<li class="dropdown user user-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<img src="../assets/uploads/<?php echo $_SESSION['user']['photo']; ?>" class="user-image" alt="User Image">
								<span class="hidden-xs"><?php echo $_SESSION['user']['full_name']; ?></span>
							</a>
							<ul class="dropdown-menu">
								<li class="user-footer">
									<div>
										<a href="profile-edit.php" class="btn btn-default btn-flat">Sửa Profile</a>
									</div>
									<div>
										<a href="logout.php" class="btn btn-default btn-flat">đăng xuất</a>
									</div>
								</li>
							</ul>
						</li>
					</ul>
				</div>

			</nav>
		</header>

		<?php $cur_page = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1); ?>
		<!-- Side Bar to Manage Shop Activities -->
		<aside class="main-sidebar">
			<section class="sidebar">

				<ul class="sidebar-menu">

					<li class="treeview <?php if ($cur_page == 'index.php') {
											echo 'active';
										} ?>">
						<a href="index.php">
							<i class="fa fa-hand-o-right"></i> <span>Bảng điều khiển(Dashboard)</span>
						</a>
					</li>

                    <li class="treeview <?php if(($cur_page == 'end-category.php') || ($cur_page == 'end-category-add.php') || ($cur_page == 'end-category-edit.php') ) {echo 'active';} ?>">
                        <a href="end-category.php">
                            <i class="fa fa-hand-o-right"></i>
                            <span>Loại hàng</span>
                        </a>
                        
                    </li>
					<li class="treeview <?php if (($cur_page == 'product.php') || ($cur_page == 'product-add.php') || ($cur_page == 'product-edit.php')) {
											echo 'active';
										} ?>">
						<a href="product.php">
							<i class="fa fa-hand-o-right"></i> <span>Xem / Thêm sản phẩm</span>
						</a>
					</li>


					<li class="treeview <?php if (($cur_page == 'order.php')) {
											echo 'active';
										} ?>">
						<a href="order.php">
							<i class="fa fa-hand-o-right"></i> <span>Quản lý đặt hàng</span>
						</a>
					</li>


					<li class="treeview <?php if (($cur_page == 'customer.php') || ($cur_page == 'customer-add.php') || ($cur_page == 'customer-edit.php')) {
											echo 'active';
										} ?>">
						<a href="customer.php">
							<i class="fa fa-hand-o-right"></i> <span>Khách hàng</span>
						</a>
					</li>

				</ul>
			</section>
		</aside>

		<div class="content-wrapper">