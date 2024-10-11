<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
	$valid = 1;
    if(empty($_POST['ecat_name'])) {
        $valid = 0;
        $error_message .= "End level category name can not be empty<br>";
    }

    if($valid == 1) {

		//Saving data into the main table tbl_end_category
		$statement = $pdo->prepare("INSERT INTO tbl_end_category (ecat_name,mcat_id) VALUES (?,1)");
		$statement->execute(array($_POST['ecat_name']));
	
    	$success_message = 'End Level Category is added successfully.';
    }
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Thêm loại hàng</h1>
	</div>
	<div class="content-header-right">
		<a href="end-category.php" class="btn btn-primary btn-sm">Xem toàn bộ loại hàng</a>
	</div>
</section>


<section class="content">

	<div class="row">
		<div class="col-md-12">

			<?php if($error_message): ?>
			<div class="callout callout-danger">
			
			<p>
			<?php echo $error_message; ?>
			</p>
			</div>
			<?php endif; ?>

			<?php if($success_message): ?>
			<div class="callout callout-success">
			
			<p><?php echo $success_message; ?></p>
			</div>
			<?php endif; ?>

			<form class="form-horizontal" action="" method="post">

				<div class="box box-info">
					<div class="box-body">
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Tên loại hàng <span>*</span></label>
							<div class="col-sm-4">
								<input type="text" class="form-control" name="ecat_name">
							</div>
						</div>
						
						<div class="form-group">
							<label for="" class="col-sm-3 control-label"></label>
							<div class="col-sm-6">
								<button type="submit" class="btn btn-success pull-left" name="form1">Xác nhận</button>
							</div>
						</div>
					</div>
				</div>

			</form>


		</div>
	</div>

</section>

<?php require_once('footer.php'); ?>