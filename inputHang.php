<?php include "perm.php";
$currentPage = basename($_SERVER['PHP_SELF']); // Gets the current page filename

if (!isAllowedPage($currentPage)) {
    header("Location: index.php");  // Redirect to a default page (e.g., index.php)
    exit();
}?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="post">
    <div class="container">
        <div class="header">
            <h1 style="text-align: center;">Nhập Hàng</h1>
            <div class="form">
                <div class="inputTH">
                    <span>Nhập tên hàng</span>
                    <input type="text" name="tenhang" id="" required>
                </div>
                    <span>Nhập mô tả hàng</span>
                    <input type="text" name="mota" id="">
                </div>
                    <span>Nhập hình ảnh hàng</span>
                    <input type="text" name="hinh" id="">
                </div>
                <div class="inputDVD">
                    <span>Nhập Đơn vị đo</span>
                    <input type="text" name="donvido" id="">
                </div>
                <div class="inputDG">
                    <span>Nhập Đơn giá</span>
                    <input type="number" name="dongia" id="">
                </div>
                </div>
                <div class="inputMLH">
                    <span>Nhập mã loại hàng</span>
                    <input type="number" name="maloaihang" id="">
                </div>
                <div class="inputSLT">
                    <span>Nhập số lượng tồn</span>
                    <input type="number" name="soluongton" id="">
                </div>
                <div class="inputHSD">
                    <span>Nhập hạn sử dụng</span>
                    <input type="date" name="hansudung" id="">
                </div>
                <button type="submit">Nhập hàng</button>
            </div>
        </div>
    </div>
    </form>
    <?php
// Chèn kết nối MySQL
include 'conn.php';

// Kiểm tra nếu form đã được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $tenhang = $_POST['tenhang'];
    $mota = $_POST['mota'];
    $hinh = $_POST['hinh'];
    $donvido = $_POST['donvido'];
    $dongia = $_POST['dongia'];
    $maloaihang = $_POST['maloaihang'];
    $soluongton = $_POST['soluongton'];
    $hansudung = $_POST['hansudung'];

    // Chuẩn bị câu truy vấn SQL
    $sql = "INSERT INTO hang (tenhang, mota, hinh, donvido, dongia, maloaihang, soluongton, hansudung) 
            VALUES ('$tenhang', '$mota', '$hinh' '$donvido', '$dongia', '$maloaihang', '$soluongton', '$hansudung')";

    // Thực hiện truy vấn
    if ($conn->query($sql) === TRUE) {
        echo "Nhập hàng thành công!";
    } else {
        echo "Lỗi: " . $sql . "<br>" . $conn->error;
    }

    // Đóng kết nối
    $conn->close();
}
?>

</body>
</html>