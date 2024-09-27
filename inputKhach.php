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
            <h1 style="text-align: center;">Nhập Khách</h1>
            <div class="form">
                <div class="inputTH">
                    <span>Nhập tên khách</span>
                    <input type="text" name="tenkhach" id="">
                </div>
                <div class="inputDVD">
                    <span>Nhập số điện thoại</span>
                    <input type="text" name="dienthoai" id="">
                </div>
                <div class="inputDG">
                    <span>Nhập địa chỉ</span>
                    <input type="number" name="diachi" id="">
                </div>
                <button type="submit">Nhập khách</button>
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
    $tenkhach = $_POST['tenkhach'];
    $dienthoai = $_POST['dienthoai'];
    $diachi = $_POST['diachi'];

    // Chuẩn bị câu truy vấn SQL
    $sql = "INSERT INTO hang (tenkhach, dienthoai, diachi) 
            VALUES ('$tenkhach', '$dienthoai', '$diachi')";

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