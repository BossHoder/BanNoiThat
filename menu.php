<head>
	<link rel="stylesheet" href="asset/css/style.css">
</head>
<div class="menu-loaihang">
    <div class="nav_menu" style="background-color: aliceblue">
	
      <div style="background-color: #ECF0F1; padding-top: 80px;">
        <span> <a href='index.php'>Loại hàng</a></span>
      </div>
        <ul class="nav-menu-list">
        <?php 
		include 'conn.php';
		$sql="select * from loaihang"; 
		$kq=mysqli_query($conn, $sql);
		$n=mysqli_num_rows($kq);
		if($n!=0)
		{
			while($r=mysqli_fetch_array($kq))
			{
				//$mal=$r[0];
				echo "<li id=".$r[1].">
                  <a href='ViewByType.php?maloaihang=".$r[0]."'>".$r[1].'</a>
				</li>';
			}
		}
				?>
       </ul>
  </div>
	
</div>