<?php require_once('header.php'); ?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Xem Loại Hàng</h1>
	</div>
	<div class="content-header-right">
		<a href="end-category-add.php" class="btn btn-primary btn-sm">Thêm loại hàng mới</a>
	</div>
</section>


<section class="content">

  <div class="row">
    <div class="col-md-12">


      <div class="box box-info">
        
        <div class="box-body table-responsive">
          <table id="example1" class="table table-bordered table-striped">
			<thead>
			    <tr>
			        <th>Số thứ tự</th>
			        <th>Mã loại hàng</th>
			        <th>Tên loại hàng</th>
			        <th>Hành động</th>
			    </tr>
			</thead>
            <tbody>
            	<?php
            	$i=0;
            	$statement = $pdo->prepare("SELECT * 
                                    FROM tbl_end_category t1
                                    JOIN tbl_mid_category t2
                                    ON t1.mcat_id = t2.mcat_id
                                    JOIN tbl_top_category t3
                                    ON t2.tcat_id = t3.tcat_id
                                    ORDER BY t1.ecat_id DESC
                                    ");
            	$statement->execute();
            	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
            	foreach ($result as $row) {
            		$i++;
            		?>
					<tr>
	                    <td><?php echo $i; ?></td>
	                    <td><?php echo $row['ecat_id']; ?></td>
	                    <td><?php echo $row['ecat_name']; ?></td>
	                    <td>
	                        <a href="end-category-edit.php?id=<?php echo $row['ecat_id']; ?>" class="btn btn-primary btn-xs">Sửa</a>
	                        <a href="#" class="btn btn-danger btn-xs" data-href="end-category-delete.php?id=<?php echo $row['ecat_id']; ?>" data-toggle="modal" data-target="#confirm-delete">Xoá</a>
	                    </td>
	                </tr>
            		<?php
            	}
            	?>
            </tbody>
          </table>
        </div>
      </div>
  

</section>


<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Xác nhận xoá</h4>
            </div>
            <div class="modal-body">
                <p>Liệu có chắc chắn xoá?</p>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Huỷ</button>
                <a class="btn btn-danger btn-ok">Xoá</a>
            </div>
        </div>
    </div>
</div>


<?php require_once('footer.php'); ?>