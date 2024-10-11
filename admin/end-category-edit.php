<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
	$valid = 1;

    if(empty($_POST['ecat_name'])) {
        $valid = 0;
        $error_message .= "Tên loại hàng không được trống";
    }

    if($valid == 1) {    	
		// updating into the database
		$statement = $pdo->prepare("UPDATE tbl_end_category SET ecat_name=? WHERE ecat_id=?");
		$statement->execute(array($_POST['ecat_name'],$_REQUEST['id']));

    	$success_message = 'Sửa thành công';
    }
}
?>

<?php
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	// Check the id is valid or not
	$statement = $pdo->prepare("SELECT * 
                            FROM tbl_end_category t1
                            JOIN tbl_mid_category t2
                            ON t1.mcat_id = t2.mcat_id
                            JOIN tbl_top_category t3
                            ON t2.tcat_id = t3.tcat_id
                            WHERE t1.ecat_id=?");
	$statement->execute(array($_REQUEST['id']));
	$total = $statement->rowCount();
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
	if( $total == 0 ) {
		header('location: logout.php');
		exit;
	}
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Sửa loại hàng</h1>
	</div>
	<div class="content-header-right">
		<a href="end-category.php" class="btn btn-primary btn-sm">Xem toàn bộ loại hàng</a>
	</div>
</section>


<?php							
foreach ($result as $row) {
	$ecat_name = $row['ecat_name'];
}
?>

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
                    <label for="" class="col-sm-3 control-label">Sửa tên loại hàng<span>*</span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="ecat_name" value="<?php echo $ecat_name; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                	<label for="" class="col-sm-3 control-label"></label>
                    <div class="col-sm-6">
                      <button type="submit" class="btn btn-success pull-left" name="form1">Cập nhật</button>
                    </div>
                </div>

            </div>

        </div>

        </form>



    </div>
  </div>

</section>

<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
            </div>
            <div class="modal-body">
                Are you sure want to delete this item?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>