<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Hàng theo từng loại</title>
</head>

<body>
<?php
    include 'conn.php';  // Đảm bảo file kết nối cơ sở dữ liệu được đúng
    
    // Kiểm tra biến 'maloaihang' có được truyền vào hay không
    if (isset($_REQUEST['maloaihang'])) {
        $malh = $_REQUEST['maloaihang'];
        
        // Kiểm tra kết nối cơ sở dữ liệu
        if (!$conn) {
            die("Kết nối cơ sở dữ liệu không thành công: " . mysqli_connect_error());
        }
        
        // Thực hiện truy vấn
        $kq = mysqli_query($conn, "SELECT hang.maloaihang, hang.tenhang, hang.mota, loaihang.tenloaihang, hang.hinh, hang.dongia, hang.soluongton 
                                  FROM hang 
                                  JOIN loaihang ON hang.maloaihang = loaihang.maloaihang 
                                  WHERE hang.maloaihang = '$malh'");
        
        // Kiểm tra kết quả truy vấn
        if (mysqli_num_rows($kq) > 0) {
            echo "<table align='center' width='1000' cellpadding='0' cellspacing='0' class='style49' border='0' style='border-collapse:collapse; border-color:#FF6600'>";
            $stt = 0;
            
            while ($row = mysqli_fetch_array($kq)) {
                $tenhang = $row['tenhang'];
                $mota = $row['mota'];
                $nhom = $row['tenloaihang'];
                $hinh = $row['hinh'];
                $dongia = number_format($row['dongia'], 0, '.', '.');  // Format đơn giá
                $soluongton = number_format($row['soluongton'], 0, '.', '.');  // Format số lượng tồn
                $hinh1 = "<a class='img-thumbnail' href='#'><img src='img/".$hinh."' border='0' height='300' width='300'/><span></span><span></span></a>";

                if ($stt % 6 == 0) {
                    echo "<tr>";
                }

                echo "<td valign='top'>";
                echo "<table align='center' border='0'>";
                echo "<tr><td align='center'><font color='red'><b>$tenhang</b></td></tr>";
                echo "<tr><td align='center'><font color='green'>$dongia VNĐ</td></tr>";
                echo "<tr><td align='center'><font color='green'>$nhom</td></tr>";
                echo "<tr><td align='center' valign='middle' bgcolor='#FFFFFF'>$hinh1</td></tr>";
                echo "<tr><td></td></tr>";
                echo "<tr><td>&nbsp;</td></tr>";
                echo "</table>";
                echo "</td>";

                $stt++;
                if ($stt % 6 == 0) {
                    echo "</tr>";
                }
            }
            echo "</table>";
        } else {
            echo "Không có hàng nào thuộc loại này.";
        }
    } else {
        echo "Không có mã loại hàng được truyền.";
    }
?>
</body>
</html>
